<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;

class FinancialTransactionController extends Controller
{
    public function index()
    {
        $transaksi = FinancialTransaction::with('order')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($t) => $this->format($t))
            ->values();

        return view('admin.keuangan', compact('transaksi'));
    }

    public function store(Request $request)
    {
        $trx = FinancialTransaction::create([
            ...$this->validasi($request),
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi tersimpan.',
            'data' => $this->format($trx),
        ]);
    }

    public function update(Request $request, $id)
    {
        $trx = FinancialTransaction::findOrFail($id);

        if ($trx->order_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi ini tercatat otomatis dari pesanan ' . optional($trx->order)->order_number . ' dan tidak bisa diubah manual. Ubah lewat status pesanan di Manajemen Pesanan kalau nilainya perlu dikoreksi.',
            ], 422);
        }

        $trx->update($this->validasi($request));

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi diperbarui.',
            'data' => $this->format($trx->fresh()),
        ]);
    }

    public function destroy($id)
    {
        $trx = FinancialTransaction::findOrFail($id);

        if ($trx->order_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi ini tercatat otomatis dari pesanan ' . optional($trx->order)->order_number . ' dan tidak bisa dihapus manual, supaya laporan tidak kehilangan jejak penjualan yang sudah selesai.',
            ], 422);
        }

        $trx->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi dihapus.',
        ]);
    }

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'kategori' => 'required|string|max:50',
            'ket' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'tgl' => 'required|date',
        ]);

        return [
            'type' => $data['jenis'],
            'category' => $data['kategori'],
            'description' => $data['ket'],
            'amount' => $data['nominal'],
            'transaction_date' => $data['tgl'],
        ];
    }

    private function format(FinancialTransaction $t): array
    {
        return [
            'id' => $t->id,
            'tgl' => $t->transaction_date->format('Y-m-d'),
            'jenis' => $t->type,
            'kategori' => $t->category,
            'ket' => $t->description,
            'nominal' => (float) $t->amount,
            'otomatis' => (bool) $t->order_id,
            'kode_pesanan' => optional($t->order)->order_number,
        ];
    }
}
