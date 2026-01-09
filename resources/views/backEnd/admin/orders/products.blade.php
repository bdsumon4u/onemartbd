<tr>
    <input type="hidden" name="product_id[]" id="product_id" value="{{$data->id}}">
    <td>
        @if ($data->get_thumb)
            <img src="{{ asset($data->get_thumb->file_url) }}"
                alt="Product Image" style="width: 34px">
        @else
            <img src="{{ asset('backEnd/assets/images/no_image.png') }}"
                alt="Product Image" style="width: 34px">
        @endif

    </td>
    <td>{{$data->sku}}</td>
    <td class="text-left">
        {{$data->name}}
        @if(count($data->get_attributes) >0)
            <br>
            @foreach($data->get_attributes as $key => $att)
                @if($key!=0)
                    <br>
                @endif
                <label for=""><b>{{$att->get_attribute->title}}</b></label>
                <br>
                <input type="hidden" name="attribute_id[{{$data->id}}][]" value="{{$att->get_attribute->id}}">
                @foreach($att->get_attribute_items as $key2 => $attr_item)
                    <input type="radio" id="val_{{$key}}{{$key2}}"
                           name="attribute_item_id[{{$data->id}}][{{$att->get_attribute->id}}][]"
                           value="{{$attr_item->get_attribute_item->id}}"
                           class="attr_checkbox" {{$key2==0?'checked':""}} required>
                    <label for="val_{{$key}}{{$key2}}">
                        <span>{{$attr_item->get_attribute_item->item_title}}</span>
                    </label>
                @endforeach
            @endforeach
        @endif
    </td>
    <td>
        <input style="width: 60px;border: 1px solid #ddd;" min="1" type="number" class="form-control qty" name="qty[]" id="qty2" value="1">
        <input type="hidden" name="price[]" id="price" class="price" value="{{$data->sale_price > 0 ? $data->sale_price : $data->price}}">
    </td>
    <td class="total_price">{{$data->sale_price > 0 ? $data->sale_price : $data->price}}</td>
    <td><i class="fa fa-trash remove_btn text-danger" style="cursor: pointer"></i></td>
</tr>

{{--<script>
    function calcSubTotal() {
        var result = 0;
        $('#prod_row tr').each(function () {
            $('.total_price', this).each(function (index, val) {
                result += parseInt($(val).text());
            });
        });

        $('#sub_total').val(result);
    }

    function finalCalc() {
        calcSubTotal();
        var shipping_cost = parseFloat($('#shipping_cost').val());
        var discount = parseFloat($('#discount').val());
        var sub_total = parseFloat($('#sub_total').val());
        var total = parseFloat((sub_total + shipping_cost) - discount);
        $('#total').val(total);
    }


    $('.remove_btn').on('click', function () {
        $(this).closest("tr").remove();
        finalCalc();
    });

    $('.qty').on('keyup change', function () {
        var total_price = parseFloat($(this).next().val()) * parseInt($(this).val());
        $(this).parent().next().text(total_price);
        finalCalc();
    });

    $('#shipping_cost,#discount').on('keyup', function () {
        finalCalc();
    });
</script>--}}
