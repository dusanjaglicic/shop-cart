<?php

namespace App\Jobs;

use App\Mail\LowStockMail;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLowStockNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $productId
    ) {}

    public function handle(): void
    {
        $adminEmail = config('mail.admin_email');

        if (!$adminEmail) {
            return; // nema admin email, nema slanja
        }

        $product = Product::query()->find($this->productId);

        if (!$product) {
            return;
        }

        // dodatna zaštita: šalji samo ako je zaista low stock
        if ($product->stock_quantity > $product->low_stock_threshold) {
            return;
        }

        Mail::to($adminEmail)->send(new LowStockMail($product));
    }
}
