<table>
    <thead>
    <tr>
        <th>Order Date</th>
        <th>Order ID</th>
        <th>Name</th>
        <th>Address</th>
        <th>Number</th>
        <th>Total amount</th>
        <th>Item Name</th>
        <th>Quantity</th>
        <th>Shipping method</th>
        <th>SKU</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ date('d M, Y',strtotime($item->order_date)) }}</td>
            <td>{{ $item->invoice_id }}</td>
            <td>{{ $item->customer_name }}</td>
            <td>{{ $item->customer_address }}</td>
            <td>{{ $item->customer_phone }}</td>
            <td>{{ $item->total }}</td>
            <td>
                @foreach($item->get_products as $product)
                    {{$product->get_product->name}} <br>
                    @if($product->attributes)
                        @foreach(json_decode($product->attributes, true) as $key => $attr)
                            <span
                                class="text-primary">{{$key}} - {{$attr}}</span>
                            <br>
                        @endforeach
                    @endif
                @endforeach
            </td>
            <td>
                @foreach($item->get_products as $product)
                    {{$product->qty}}
                @endforeach
            </td>
            <td>{{$item->get_shipping_method->type}}</td>
            <td>
                @foreach($item->get_products as $product)
                    {{$product->get_product->sku}}
                @endforeach
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
