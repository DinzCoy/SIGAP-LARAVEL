<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $user       = Auth::user();
        $activeRole = session('active_role_id');

        // Pimpinan (1), Admin (2), Pengelola Barang (4): see all tickets
        if (in_array($activeRole, [1, 2, 4])) {
            $tickets = Ticket::with(['asset', 'reporter', 'technician'])
                ->latest()
                ->paginate(25);
        }
        // Teknisi (3): sees tickets except those pending pengelola review
        elseif ($activeRole == 3) {
            $tickets = Ticket::where('status', '!=', 'Menunggu Pengecekan Pengelola')
                ->with(['asset', 'reporter', 'technician'])
                ->latest()
                ->paginate(25);
        }
        // Regular users (6) and Pengelola Ruangan (5): own tickets only
        else {
            $tickets = Ticket::where('reported_by', $user->id)
                ->with(['asset', 'reporter', 'technician'])
                ->latest()
                ->paginate(25);
        }

        return view('tickets.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket     = Ticket::with(['asset', 'reporter', 'technician', 'replies.user'])->findOrFail($id);
        $user       = Auth::user();
        $activeRole = session('active_role_id');

        // Only ticket owner or privileged roles (Admin, Teknisi, Pimpinan, Pengelola) may view
        $canView = in_array($activeRole, [1, 2, 3, 4]) || $ticket->reported_by === $user->id;
        if (! $canView) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        return view('tickets.show', compact('ticket'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:Asset,General',
            'asset_id' => 'required_if:type,Asset|nullable|exists:assets,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:Rendah,Sedang,Tinggi',
        ]);

        $status = $request->type === 'Asset' ? 'Menunggu Pengecekan Pengelola' : 'Open';

        Ticket::create([
            'type' => $request->type,
            'asset_id' => $request->type === 'Asset' ? $request->asset_id : null,
            'reported_by' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $status,
        ]);

        return redirect()->route('tickets.index')->with('success', 'Tiket berhasil dibuat.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Menunggu Pengecekan Pengelola,Diteruskan ke Teknisi,Open,In Progress,Menunggu Persetujuan Biaya,Approved,Selesai,Dibatalkan',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        $ticket = Ticket::findOrFail($id);

        $ticket->status = $request->status;

        if ($request->has('estimated_cost')) {
            $ticket->estimated_cost = $request->estimated_cost;
        }

        // Auto-assign technician if status is moving from Open to In Progress
        if ($ticket->status === 'In Progress' && !$ticket->technician_id) {
             $ticket->technician_id = Auth::id();
        }

        $ticket->save();

        return back()->with('success', 'Status tiket berhasil diupdate.');
    }

    public function addReply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket     = Ticket::findOrFail($id);
        $user       = Auth::user();
        $activeRole = session('active_role_id');

        // Only ticket owner or privileged roles may reply
        $canReply = in_array($activeRole, [1, 2, 3, 4]) || $ticket->reported_by === $user->id;
        if (! $canReply) {
            abort(403, 'Anda tidak memiliki akses untuk membalas tiket ini.');
        }

        TicketReply::create([
            'ticket_id' => $id,
            'user_id'   => Auth::id(),
            'message'   => $request->message,
        ]);

        return back()->with('success', 'Balasan berhasil ditambahkan.');
    }
}
