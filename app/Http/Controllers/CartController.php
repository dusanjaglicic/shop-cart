<?php

namespace App\Http\Controllers;

    use App\Models\CartItem;
    use App\Models\Product;
    use Illuminate\Http\Request;
    use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::query()
            ->where('user_id', auth()->id())
            ->with(['product:id,name,price,stock_quantity'])
            ->orderByDesc('id')
            ->get();

        $total = $cartItems->sum(function ($item) {
            return (float) $item->product->price * $item->quantity;
        });

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::query()->findOrFail($validated['product_id']);

        // već u cartu?
        $existing = CartItem::query()
            ->where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        $newQty = ($existing?->quantity ?? 0) + (int) $validated['quantity'];

        if ($newQty > $product->stock_quantity) {
            return back()->withErrors([
                'quantity' => "Not enough stock. Available: {$product->stock_quantity}",
            ]);
        }

        CartItem::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ],
            [
                'quantity' => $newQty,
            ]
        );

        return redirect()->route('cart.index')->with('success', 'Added to cart.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        abort_if($cartItem->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cartItem->load('product');

        if ((int) $validated['quantity'] > $cartItem->product->stock_quantity) {
            return back()->withErrors([
                'quantity' => "Not enough stock. Available: {$cartItem->product->stock_quantity}",
            ]);
        }

        $cartItem->update([
            'quantity' => (int) $validated['quantity'],
        ]);

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(CartItem $cartItem)
    {
        abort_if($cartItem->user_id !== auth()->id(), 403);

        $cartItem->delete();

        return back()->with('success', 'Item removed.');
    }
}


