<table class="table table-borderless">
    <thead>
    <tr>
        <th>SL.</th>
        <th>Message</th>
        <th>Date & Time</th>
    </tr>
    </thead>
    <tbody>
    @php($i=1)
    @if(count($transactions)>0)
        @foreach($transactions as $transaction)
            <tr>
                <td>{{$i++}}</td>
                <td>
                    @if($transaction->type == 'api')
                        <span class="badge badge-warning">API</span>
                    @elseif($transaction->type == 'local')
                        <span class="badge badge-success">Local</span>
                    @endif
                    {{$transaction->text}}</td>
                <td>{{date('d M, Y',strtotime($transaction->created_at))}}<br>
                    <small>{{date('h:i:s A',strtotime($transaction->created_at))}}</small>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="3" class="text-danger text-center">No Transaction Found!</td>
        </tr>
    @endif
    </tbody>
</table>
