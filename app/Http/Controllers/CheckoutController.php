<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendLowStockNotification;


class CheckoutController extends Controller
{
    public function store()
    {
        $userId = auth()->id();

        $cartItems = CartItem::query()
            ->where('user_id', $userId)
            ->with(['product:id,name,price,stock_quantity'])
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->withErrors([
                'cart' => 'Your cart is empty.',
            ]);
        }

        DB::transaction(function () use ($userId, $cartItems) {

            // zaključaj proizvode (sprečava race condition)
            $productIds = $cartItems->pluck('product_id')->unique()->values();

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // re-check stock
            foreach ($cartItems as $item) {
                $product = $products[$item->product_id];

                if ($item->quantity > $product->stock_quantity) {
                    throw new \Exception("Not enough stock for {$product->name}");
                }
            }

            // napravi order
            $order = Order::create([
                'user_id' => $userId,
                'total_amount' => 0,
                'status' => 'paid',
            ]);

            $total = 0;

            foreach ($cartItems as $item) {
                $product = $products[$item->product_id];

                $unitPrice = (float) $product->price;
                $lineTotal = $unitPrice * $item->quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'unit_price' => $unitPrice,
                    'quantity' => $item->quantity,
                    'line_total' => $lineTotal,
                ]);

                // umanji stock
                $product->decrement('stock_quantity', $item->quantity);


                $product->refresh();

                if ($product->stock_quantity <= $product->low_stock_threshold) {
                    SendLowStockNotification::dispatch($product->id);
                }


                $total += $lineTotal;
            }

            // update total
            $order->update([
                'total_amount' => $total,
            ]);

            // isprazni cart
            CartItem::query()
                ->where('user_id', $userId)
                ->delete();
        });

        return redirect()->route('cart.index')->with('success', 'Order placed successfully!');
    }
}
