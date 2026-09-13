<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;

class ReviewController extends Controller
{
    public function create($kode)
    {
        return view('review', compact('kode'));
    }

    public function store(Request $request, $kode)
    {
        $request->validate([
            'rating_total' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|min:20',
        ]);

        $order = Order::where('order_number', $kode)->first();
        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan.']);
        }

        $item = DB::table('order_items')
            ->where('order_id', $order->id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Produk tidak valid.']);
        }

        $hasStatus = Schema::hasColumn('reviews', 'status');

        $dataToSave = [
            'rating_overall' => $request->rating_total,
            'rating_rasa' => $request->rating_total,
            'rating_kualitas' => $request->rating_total,
            'rating_packaging' => $request->rating_total,
            'rating_pelayanan' => $request->rating_total,
            'comment' => $request->komentar,
            'user_id' => auth()->id() ?? $order->user_id ?? 1,
            'is_anonymous' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($hasStatus) {
            $dataToSave['status'] = 'approved';
        }

        DB::table('reviews')->updateOrInsert(
            ['order_item_id' => $item->id],
            $dataToSave
        );

        $reviewsQuery = DB::table('reviews')
            ->join('order_items', 'reviews.order_item_id', '=', 'order_items.id')
            ->where('order_items.product_id', $request->product_id);

        if ($hasStatus) {
            $reviewsQuery->where('reviews.status', 'approved');
        }

        $reviews = $reviewsQuery->get();
        $count = $reviews->count();
        $avg = $count > 0 ? $reviews->avg('rating_overall') : 0;

        DB::table('products')->where('id', $request->product_id)->update([
            'rating_avg' => $avg,
            'rating_count' => $count
        ]);

        return response()->json(['status' => 'success', 'message' => 'Ulasan berhasil disimpan.']);
    }

    public function toggleVisibility($id)
    {
        $hasStatus = Schema::hasColumn('reviews', 'status');
        
        if ($hasStatus) {
            $review = DB::table('reviews')->where('id', $id)->first();
            if ($review) {
                $newStatus = $review->status === 'approved' ? 'hidden' : 'approved';
                DB::table('reviews')->where('id', $id)->update(['status' => $newStatus]);
                
                $item = DB::table('order_items')->where('id', $review->order_item_id)->first();
                if ($item) {
                    $approved = DB::table('reviews')
                        ->join('order_items', 'reviews.order_item_id', '=', 'order_items.id')
                        ->where('order_items.product_id', $item->product_id)
                        ->where('reviews.status', 'approved')
                        ->get();
                        
                    $count = $approved->count();
                    $avg = $count > 0 ? $approved->avg('rating_overall') : 0;

                    DB::table('products')->where('id', $item->product_id)->update([
                        'rating_avg' => $avg,
                        'rating_count' => $count
                    ]);
                }
                
                return response()->json(['status' => 'success', 'new_status' => $newStatus]);
            }
        }
        return response()->json(['status' => 'error', 'message' => 'Fitur sembunyikan tidak didukung database.'], 400);
    }
}