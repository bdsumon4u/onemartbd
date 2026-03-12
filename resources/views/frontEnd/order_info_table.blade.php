<table class="cart_table table text-center mb-0">
    <thead>
    <tr>
        <th></th>
        <th>Product</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Total</th>
    </tr>
    </thead>

    <tbody>
    @foreach(\Cart::getContent()->sort() as $item)
        <tr>
            <td>
                <a href="{{route('cart.item.delete',$item->id)}}"><i class="fa fa-trash-o text-danger"></i></a>
            </td>
            <td class="text-left">
                <img width="35" src="{{$item->associatedModel->get_thumb->file_url}}" alt="">
                <a style="font-size: 14px"
                   href="{{route('single.product',[$item->associatedModel->slug,$item->associatedModel->id])}}">{{$item->name}}</a>
            </td>
            <td>{{$item->price}}</td>
            <td width="15%" class="cart_qty">
                <a href="javascript:void(0);"><i class="fa fa-minus qty_minus" id="" data-id="{{$item->id}}"></i></a>
                <input type="text" name="qty" id="qty" min="1" value="{{$item->quantity}}" readonly>
                <a href="javascript:void(0);"><i class="fa fa-plus qty_plus" id="" data-id="{{$item->id}}"></i></a>
            </td>
            <td>{{$item->getPriceSum()}}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <th colspan="4" class="text-right pr-2">Sub Total</th>
        <td><span id="net_total">{{\Cart::getTotal()}}</span></td>
    </tr>
    <tr>
        <th colspan="4" class="text-right pr-2">Shipping Cost</th>
        <td>
            <span id="cart_shipping_cost">0</span>
        </td>
    </tr>
    <tr id="extra_discount_row" style="display: none;">
        <th colspan="4" class="text-right pr-2 text-success">
            Special Discount
        </th>
        <td class="text-success">
            -<span id="extra_discount_amount">0</span>
        </td>
    </tr>
    <tr>
        <th colspan="4" class="text-right pr-2">Total</th>
        <td>
            <span id="grand_total"></span>
        </td>
    </tr>
    </tfoot>
</table>
