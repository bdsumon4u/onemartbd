<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    <channel>
        <title>{{ env('APP_NAME') }}</title>
        <link>{{ env('APP_URL') }}</link>
        <description>All Products from {{ env('APP_NAME') }}</description>

        @if(count($products) > 0)
            @foreach($products as $product)
                <item>
                    <g:id>{{ $product->id ?? null }}</g:id>
                    <g:title><![CDATA[{{ $product->name ?? null }}]]></g:title>
                    <g:description><![CDATA[{{ $product->fb_description ?? "No Description" }}]]></g:description>
                    <g:link>{{ route('single.product', [$product->slug, $product->id]) }}</g:link>
                    <g:image_link>{{ $product->get_image ? asset($product->get_image->file_url) : "" }}</g:image_link>
                    <g:brand><![CDATA[{{ $product->brand_name ?? "Non Brand" }}]]></g:brand>
                    <g:condition>new</g:condition>
                    <g:availability>in stock</g:availability>
                    <g:price>{{ number_format($product->price, 2) }} BDT</g:price>
                    @if($product->sale_price > 0)
                        <g:sale_price>{{ number_format($product->sale_price, 2) }} BDT</g:sale_price>
                    @endif
                </item>
            @endforeach
        @endif
    </channel>
</rss>
