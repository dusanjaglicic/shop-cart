<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Product $product
    ) {}

    public function build()
    {
        return $this
            ->subject("LOW STOCK: {$this->product->name}")
            ->markdown('emails.low-stock', [
                'product' => $this->product,
            ]);
    }
}
