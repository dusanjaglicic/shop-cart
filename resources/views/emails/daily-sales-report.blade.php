@component('mail::message')
    # Daily Sales Report

    Date: **{{ $date }}**

    @if(count($rows) === 0)
        No sales today.
    @else

        @component('mail::table')
            | Product | Qty Sold | Revenue |
            |:--|--:|--:|
            @foreach($rows as $row)
                | {{ $row['product_name'] }} | {{ $row['qty_sold'] }} | {{ number_format($row['revenue'], 2) }} |
            @endforeach
        @endcomponent

        **Grand Total:** {{ number_format($grandTotal, 2) }}

    @endif

    Thanks,
    {{ config('app.name') }}
@endcomponent

