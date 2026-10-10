<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    /**
     * Get list of announcements
     */
    public function index(Request $request)
    {
        try {
            $query = Pengumuman::query();

            if (!empty($request->search)) {
                $query->where(function ($q) use ($request) {
                    $q->where('judul', 'like', '%' . $request->search . '%')
                      ->orWhere('isi', 'like', '%' . $request->search . '%');
                });
            } elseif (!empty($request->judul)) {
                $query->where('judul', 'like', '%' . $request->judul . '%');
            }

            $pengumuman = $query->orderBy('created_at', 'desc')->get();

            $data = $pengumuman->map(function ($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'isi' => $item->isi,
                    'lampiran' => $item->lampiran,
                    'lampiran_url' => $item->lampiran ? asset('storage/uploads/pengumuman/' . $item->lampiran) : null,
                    'created_at' => $item->created_at ? $item->created_at->toIso8601String() : null,
                    'formatted_date' => $item->created_at ? Carbon::parse($item->created_at)->locale('id')->isoFormat('D MMMM Y, HH:mm') : '-',
                    'short_date' => $item->created_at ? Carbon::parse($item->created_at)->locale('id')->isoFormat('D MMM Y') : '-',
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data pengumuman berhasil diambil',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pengumuman: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get detail announcement
     */
    public function show($id)
    {
        try {
            $item = Pengumuman::find($id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengumuman tidak ditemukan',
                ], 404);
            }

            $data = [
                'id' => $item->id,
                'judul' => $item->judul,
                'isi' => $item->isi,
                'lampiran' => $item->lampiran,
                'lampiran_url' => $item->lampiran ? asset('storage/uploads/pengumuman/' . $item->lampiran) : null,
                'created_at' => $item->created_at ? $item->created_at->toIso8601String() : null,
                'formatted_date' => $item->created_at ? Carbon::parse($item->created_at)->locale('id')->isoFormat('D MMMM Y, HH:mm') : '-',
                'short_date' => $item->created_at ? Carbon::parse($item->created_at)->locale('id')->isoFormat('D MMM Y') : '-',
            ];

            return response()->json([
                'success' => true,
                'message' => 'Detail pengumuman berhasil diambil',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail pengumuman: ' . $e->getMessage(),
            ], 500);
        }
    }
}
