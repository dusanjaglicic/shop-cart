<?php

namespace App\Jobs;

use App\Mail\DailySalesReportMail;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendDailySalesReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $adminEmail = config('mail.admin_email');

        if (!$adminEmail) {
            return;
        }

        $today = Carbon::today(); // koristi app timezone
        $start = $today->copy()->startOfDay();
        $end = $today->copy()->endOfDay();

        // uzmi sve order_items iz današnjih order-a
        $items = OrderItem::query()
            ->whereHas('order', function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            })
            ->with('product:id,name')
            ->get();

        // grupiši po product_id
        $grouped = $items->groupBy('product_id');

        $rows = [];
        $grandTotal = 0;

        foreach ($grouped as $productId => $groupItems) {
            $productName = $groupItems->first()->product?->name ?? "Product #{$productId}";

            $qtySold = $groupItems->sum('quantity');
            $revenue = (float) $groupItems->sum('line_total');

            $rows[] = [
                'product_name' => $productName,
                'qty_sold' => $qtySold,
                'revenue' => $revenue,
            ];

            $grandTotal += $revenue;
        }

        // sort po revenue desc (lepše u mail-u)
        usort($rows, fn ($a, $b) => $b['revenue'] <=> $a['revenue']);

        Mail::to($adminEmail)->send(new DailySalesReportMail(
            date: $today->toDateString(),
            rows: $rows,
            grandTotal: $grandTotal
        ));
    }
}
