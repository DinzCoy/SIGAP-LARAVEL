<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\DeviceName;
use App\Models\PcReport;
use App\Models\Room;
use App\Models\User;
use App\Http\Requests\CekInputAset;
use App\Http\Requests\CekUpdateAset;
use App\Services\Assets\AssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function __construct(protected AssetService $assetService) {}

    public function masterAset(): RedirectResponse
    {
        // lempar ke manajemen merek & tipe barang
        return redirect()->route('device-names.index');
    }

    // Menampilkan daftar aset inventaris
    public function index(Request $request): View
    {
        $query = Asset::with(['pcReport', 'room', 'deviceName', 'user'])
            ->filter($request->only(['device_name_id', 'room', 'filter_linked', 'filter_bmn', 'search']));

        $perPage = $request->input('per_page', 10);
        if ($perPage === 'all') {
            $perPage = (clone $query)->count() ?: 10;
        }

        $assets             = $query->orderByDesc('id')->paginate($perPage)->withQueryString()->onEachSide(1);
        $unlinkedPcs        = PcReport::whereDoesntHave('asset')->orderBy('hostname')->get();
        $allDeviceNames     = DeviceName::orderBy('brand')->orderBy('name')->get();
        $currentDeviceNameId = $request->device_name_id;
        $rooms              = Room::orderBy('name')->get();
        $users              = User::orderBy('name')->get();

        return view('assets.index', compact(
            'assets',
            'unlinkedPcs',
            'allDeviceNames',
            'currentDeviceNameId',
            'rooms',
            'users'
        ));
    }

    // Menyimpan data aset baru ke database
    public function store(CekInputAset $request): RedirectResponse
    {
        Asset::create($request->validated());

        return redirect()->back()->with('success', 'Aset BMN berhasil ditambahkan.');
    }

    public function update(CekUpdateAset $request, string $id): RedirectResponse
    {
        Asset::findOrFail($id)->update($request->validated());

        return redirect()->back()->with('success', 'Data Aset berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        Asset::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Data Aset berhasil dihapus.');
    }

    // Menautkan aset BMN dengan laporan PC (Agent)
    // Catatan: otorisasi sudah ditangani middleware role:2 di web.php
    public function linkDevice(Request $request, string $assetId): RedirectResponse
    {
        $request->validate([
            'mac_address' => 'required|exists:pc_reports,mac_address',
        ]);

        $asset = Asset::findOrFail($assetId);
        $error = $this->assetService->linkDevice($asset, $request->mac_address);

        if ($error) {
            return redirect()->back()->with('error', $error);
        }

        return redirect()->back()->with('success', 'Device berhasil ditautkan ke BMN.');
    }

    // Halaman detail saat scan QR Code (Web/Mobile)
    public function scan(Request $request, string $id): View
    {
        $asset       = Asset::with(['pcReport', 'room', 'deviceName', 'user', 'activeLoan.borrower', 'pendingLoan.borrower'])->findOrFail($id);
        $activeLoan  = $asset->activeLoan;
        $pendingLoan = $asset->pendingLoan;
        $mode        = $request->query('mode'); // 'loan', 'transfer', or null (show all)

        return view('assets.scan', compact('asset', 'activeLoan', 'pendingLoan', 'mode'));
    }

    // =========================================================================
    // ALUR 1: Peminjaman Sementara
    // =========================================================================

    // Proses permintaan peminjaman aset sementara
    public function loan(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'loan_reason' => 'required|string|min:5|max:255',
        ], [
            'loan_reason.required' => 'Alasan meminjam wajib diisi agar pemilik aset bisa mempertimbangkan permintaan Anda.',
            'loan_reason.min'      => 'Alasan meminjam terlalu singkat, minimal 5 karakter.',
            'loan_reason.max'      => 'Alasan meminjam terlalu panjang, maksimal 255 karakter.',
        ]);

        $asset  = Asset::findOrFail($id);
        $user   = $this->currentUser();
        $error  = $this->assetService->requestLoan($asset, $user, $request->loan_reason);

        if ($error) {
            $flash = str_starts_with($error, 'Aset ini sudah milik') ? 'info' : 'error';
            return redirect()->back()->with($flash, $error);
        }

        $targetApprover = $asset->user_id ? 'pemilik aset' : 'Admin/Pengelola Aset';

        return redirect()->route('assets.scan', $asset->id)
            ->with('success', "Permintaan peminjaman berhasil dikirim. Menunggu persetujuan dari $targetApprover.");
    }

    public function approveLoan(Request $request, string $id): RedirectResponse
    {
        $loan = AssetLoan::with('asset')->findOrFail($id);

        if (!$loan->canBeManagedBy($this->currentUser())) {
            return redirect()->back()->with('error', 'Anda tidak berhak menyetujui peminjaman ini.');
        }

        if ($loan->status !== AssetLoan::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Status peminjaman ini tidak dalam masa tunggu.');
        }

        $this->assetService->approveLoan($loan);

        return redirect()->back()->with('success', 'Permintaan peminjaman berhasil disetujui.');
    }

    public function rejectLoan(Request $request, string $id): RedirectResponse
    {
        $loan = AssetLoan::findOrFail($id);

        if (!$loan->canBeManagedBy($this->currentUser())) {
            return redirect()->back()->with('error', 'Anda tidak berhak menolak peminjaman ini.');
        }

        if ($loan->status !== AssetLoan::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Status peminjaman ini tidak dalam masa tunggu.');
        }

        $this->assetService->rejectLoan($loan);

        return redirect()->back()->with('success', 'Permintaan peminjaman telah ditolak.');
    }

    // Proses pengembalian aset pinjaman ke pemilik asli
    public function returnLoan(Request $request, string $id): RedirectResponse
    {
        $asset = Asset::with('activeLoan')->findOrFail($id);
        $loan  = $asset->activeLoan;

        if (!$loan) {
            return redirect()->back()->with('error', 'Tidak ada peminjaman aktif untuk aset ini.');
        }

        if (!$loan->canBeReturnedBy($this->currentUser())) {
            return redirect()->back()->with('error', 'Anda tidak berhak mengembalikan aset ini.');
        }

        $this->assetService->returnLoan($asset, $loan, $this->currentUser());

        return redirect()->route('assets.scan', $asset->id)->with('success', 'Aset berhasil dikembalikan ke pemilik!');
    }

    // =========================================================================
    // ALUR 2: Serah Terima Permanen (Mutasi)
    // =========================================================================

    // Proses mutasi/serah terima aset permanen via QR Code
    public function takeover(Request $request, string $id): RedirectResponse
    {
        $asset = Asset::findOrFail($id);
        $user  = $this->currentUser();

        if ($asset->user_id === $user->id) {
            return redirect()->back()->with('info', 'Aset ini sudah tercatat atas nama Anda.');
        }

        $this->assetService->takeover($asset, $user);

        return redirect()->route('assets.scan', $asset->id)
            ->with('success', 'Aset berhasil diserahterimakan ke Anda secara permanen!');
    }

    // Halaman cetak label QR Code (Stiker BMN)
    public function print(string $id): View
    {
        $asset = Asset::with('deviceName')->findOrFail($id);

        return view('assets.print', compact('asset'));
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    /**
     * Mengambil user yang sedang login dengan tipe User yang eksplisit.
     * Dibutuhkan agar static analyzer bisa resolve method model User.
     */
    private function currentUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
