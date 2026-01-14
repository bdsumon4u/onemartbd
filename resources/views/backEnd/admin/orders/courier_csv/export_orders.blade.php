<table>
    <thead>
        <tr>
            <th>Order Date</th>
            <th>Order ID</th>
            <th>Name</th>
            <th>Address</th>
            <th>Number</th>
            <th>Delivery Charge</th>
            <th>Discount</th>
            <th>Subtotal</th>
            <th>Total amount</th>
            <th>Item Name</th>
            <th>Purchase Cost</th>
            <th>Quantity</th>
            <th>Shipping method</th>
            <th>SKU</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ date('d M, Y', strtotime($item->order_date)) }}</td>
                <td>{{ $item->invoice_id }}</td>
                <td>{{ $item->customer_name }}</td>
                <td>{{ $item->customer_address }}</td>
                <td>{{ $item->customer_phone }}</td>
                <td>{{ $item->shipping_cost }}</td>
                <td>{{ $item->discount }}</td>
                <td>{{ $item->sub_total }}</td>
                <td>{{ $item->total }}</td>
                <td>
                    @foreach ($item->products as $orderProduct)
                        {{ $orderProduct->product->name }} <br>
                        @if ($orderProduct->attributes)
                            @foreach (json_decode($orderProduct->attributes, true) as $key => $attr)
                                <small>
                                    @if ($loop->last)
                                        {{ $key }}:
                                        {{ $attr }}
                                    @else
                                        {{ $key }}:
                                        {{ $attr }},
                                    @endif
                                </small>

                            @endforeach
                            <br>
                        @endif
                    @endforeach
                </td>
                <td>
                    @foreach ($item->products as $key => $p_item)@if ($key!=0)<br> @endif {{ $p_item->purchase_cost }}@endforeach
                </td>
                <td>
                    @foreach ($item->products as $orderProduct)
                        {{ $orderProduct->qty }} <br>
                    @endforeach
                </td>
                <td>{{ $item->shippingMethod ? $item->shippingMethod->type : '' }}</td>
                <td>
                    @foreach ($item->products as $orderProduct)
                        {{ $orderProduct->product->sku }}
                    @endforeach
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
