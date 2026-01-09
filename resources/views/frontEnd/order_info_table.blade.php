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
    <tr>
        <th colspan="4" class="text-right pr-2">Total</th>
        <td>
            <span id="grand_total"></span>
        </td>
    </tr>
    </tfoot>
</table>

<script>
    $(document).ready(function () {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: '{{route('ajax.get.shipp.meth')}}',
            type: 'POST',
            data: {_token: CSRF_TOKEN, id: $('#shipping_method').val()},
            success: function (data) {
                $("#cart_shipping_cost").text(data);
                $("#shipping_cost").val(data);
                calculate();
            }
        });

        $("#shipping_method").on('change', function () {
            if ($(this).val()) {
                $.ajax({
                    url: '{{route('ajax.get.shipp.meth')}}',
                    type: 'POST',
                    data: {_token: CSRF_TOKEN, id: $(this).val()},
                    success: function (data) {
                        $("#cart_shipping_cost").text(data);
                        $("#shipping_cost").val(data);
                        calculate();
                    }
                });
            } else {
                $("#cart_shipping_cost").text(0);
                $("#shipping_cost").val(0);
                calculate();
            }

        });

        function calculate() {
            var net_total = parseFloat($('#net_total').text());
            var cart_shipping_cost = parseFloat($('#cart_shipping_cost').text());
            $('#grand_total').text(net_total + cart_shipping_cost);
        }


        $("#checkout_form").submit(function () {
            $("#conf_order_btn").attr("disabled", true).text('সাবমিট হচ্ছে...');
        });

        $(".qty_plus").click(function () {
            var qty = $('#qty').val();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '{{route('cart.item.plus')}}',
                type: 'POST',
                data: {_token: CSRF_TOKEN, id: $(this).data('id')},
                success: function (data) {
                    $('#order_info_table').empty();
                    $('#order_info_table').append(data);

                    console.log(data);
                    /*$("#qty").val(qty);*/
                    calculate();
                }
            });
        });

        $(".qty_minus").click(function () {
            var qty = $('#qty').val();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '{{route('cart.item.minus')}}',
                type: 'POST',
                data: {_token: CSRF_TOKEN, id: $(this).data('id')},
                success: function (data) {
                    $('#order_info_table').empty();
                    $('#order_info_table').append(data);
                    calculate();
                }
            });
        });

    });
</script>
