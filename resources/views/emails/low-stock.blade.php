@component('mail::message')
    # Low Stock Alert

    Product is running low on stock.

    **Product:** {{ $product->name }}
    **Price:** {{ $product->price }}
    **Stock:** {{ $product->stock_quantity }}
    **Threshold:** {{ $product->low_stock_threshold }}

    Thanks,
    {{ config('app.name') }}
@endcomponent

