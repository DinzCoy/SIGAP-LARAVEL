<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Asset;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:Insiden,Permintaan',
            'asset_id' => 'nullable|exists:assets,id', // Optional, if they scanned QR
            // Bisa tambahkan validasi foto jika nanti mobile app support upload
        ]);

        // Cari room_id jika asset_id disediakan
        $roomId = null;
        if ($request->asset_id) {
            $asset = Asset::find($request->asset_id);
            $roomId = $asset ? $asset->room_id : null;
            
            // Opsional: Otomatis ubah status kondisi aset jika rusak
            if ($request->type === 'Insiden') {
                $asset->update(['status_kondisi' => 'Rusak']);
            }
        }

        $ticket = Ticket::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'status' => Ticket::STATUS_MENUNGGU_PENGELOLA,
            'priority' => 'Medium', // Default, admin web yang akan menilai
            'reported_by' => $request->user()->id,
            'asset_id' => $request->asset_id,
            'room_id' => $roomId,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil dikirim.',
            'data' => [
                'ticket_id' => $ticket->id,
            ]
        ], 201);
    }

    public function index(Request $request)
    {
        $status = $request->query('status');
        $query = Ticket::with(['asset', 'reporter', 'technician']);
        
        if ($status) {
            $query->where('status', $status);
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $tickets
        ], 200);
    }

    public function myTickets(Request $request)
    {
        $tickets = Ticket::where('reported_by', $request->user()->id)
            ->with(['asset'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $tickets
        ], 200);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'tanggapan' => 'nullable|string',
        ]);

        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json(['status' => 'error', 'message' => 'Tiket tidak ditemukan'], 404);
        }

        $ticket->status = $request->status;
        // Optionally save tanggapan if column exists
        // $ticket->response = $request->tanggapan;
        $ticket->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status tiket berhasil diupdate.'
        ], 200);
    }
}
