<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Invoice ID</th>
            <th>Products</th>
            <th>Total</th>
            <th>Courier</th>
            <th>Date</th>
            <th>Status</th>
            <th>Payment</th>
            <th>Assigned</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($oldOrders as $item)
            <tr id="tr_{{ $item->id }}">
                <td>
                    @if ($item->source == 'page')
                        <span class="badge badge-primary">{{ ucfirst($item->source) }}</span>
                    @elseif ($item->source == 'whatsapp')
                        <span class="badge badge-success">{{ ucfirst($item->source) }}</span>
                    @elseif ($item->source == 'call')
                        <span class="badge badge-info">{{ ucfirst($item->source) }}</span>
                    @elseif ($item->source == 'direct')
                        <span class="badge badge-warning">{{ ucfirst($item->source) }}</span>
                    @elseif($item->source == 'incomplete')
                        <span class="badge badge-dark">{{ ucfirst($item->source) }}</span>
                    @endif
                    <br>
                    {{ $item->invoice_id }}
                    @if ($item->is_fake == 1)
                        <br>
                        <small class="badge badge-danger">
                            Fake!
                            @if (Auth::guard('admin')->check())
                                <a href="{{ route('admin.fake.remove', $item->id) }}"
                                    onclick="return confirm('Are You Sure?')">
                                    <i class="fa fa-trash-alt text-white"></i>
                                </a>
                            @endif
                        </small>
                    @endif
                </td>
                <td>
                    @foreach ($item->get_products as $product)
                        {{ $product->qty }} x
                        <a target="_blank"
                            href="{{ $product->get_product ? route('single.product', [$product->get_product->slug, $product->get_product->id]) : '' }}">
                            {{ $product->get_product ? $product->get_product->name : '' }}
                        </a>
                        <br>
                        @if ($product->attributes)
                            @foreach (json_decode($product->attributes, true) as $key => $attr)
                                <span class="text-primary">{{ $key }} - {{ $attr }}</span>
                                <br>
                            @endforeach
                        @endif
                    @endforeach
                </td>
                <td>
                    {{ $item->total }}<br>
                    <span class="text-success">P:-{{ $item->paid }}</span><br>
                    <span class="text-danger">D:-{{ $item->due }}</span>
                </td>
                <td>
                    {{ $item->get_courier->courier_name ?? '---' }}<br>
                    @if ($item->pathao_consignment_id)
                        <a href="https://merchant.pathao.com/tracking?consignment_id={{ $item->pathao_consignment_id }}&phone={{ $item->customer_phone }}"
                            target="_blank"><i class="fa fa-eye"></i></a>
                    @elseif($item->redx_tracking_id)
                        <a href="https://redx.com.bd/track-parcel/?trackingId={{ $item->redx_tracking_id }}"
                            target="_blank"><i class="fa fa-eye"></i></a>
                    @endif
                    @if ($item->courier_api_response)
                        <span data-toggle="tooltip" data-placement="top" title="{{ $item->courier_api_response }}">
                            <i class="fa fa-exclamation-circle text-warning"></i>
                        </span>
                    @endif
                    @if ($item->courier_status)
                        <br>
                        <small>{{ $item->courier_status ?? '' }}</small>
                    @endif
                    @if ($item->courier_status_reason)
                        <br>
                        <small style="color:#eab000">{{ $item->courier_status_reason ?? '' }}</small>
                    @endif
                </td>
                <td>
                    {{ date('d M, Y', strtotime($item->order_date)) }}<br>
                    {{ date('h:i:s A', strtotime($item->created_at)) }}
                </td>
                <td class="text-center">
                    @php
                        $statusEnum = \App\Enums\OrderStatus::tryFrom($item->status);
                        $variant = $statusEnum?->variant() ?? 'secondary';
                    @endphp
                    <button type="button" class="btn btn-{{ $variant }} status_btn btn-sm">
                        {{ $statusEnum?->label() ?? 'Unknown' }}
                    </button>
                </td>
                <td>
                    <button type="button"
                        class="btn {{ $item->payment_status == 0 ? 'btn-danger' : '' }} {{ $item->payment_status == 1 ? 'btn-info' : '' }} {{ $item->payment_status == 2 ? 'btn-success' : '' }} status_btn btn-sm">
                        @if ($item->payment_status == 0)
                            Unpaid
                        @endif
                        @if ($item->payment_status == 1)
                            Partial
                        @endif
                        @if ($item->payment_status == 2)
                            Paid
                        @endif
                    </button>
                </td>
                <td>
                    {{ $item->get_assigned ? $item->get_assigned->get_employee->name : '' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center text-danger font-weight-bold">No previous orders found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
