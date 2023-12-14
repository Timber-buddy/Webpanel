@extends('seller.layouts.app')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Payment History') }}</h5>
        </div>
        @if (count($payments) > 0)
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ translate('Date')}}</th>
                            <th>{{ translate('Subscription Plan')}}</th>
                            <th>{{ translate('Valid Upto')}}</th>
                            <th>{{ translate('Amount')}}</th>
                            <th>{{ translate('Transaction ID')}}</th>
                            <th>{{ translate('Gateway')}}</th>
                            <th>{{ translate('Method')}}</th>
                            <th>{{ translate('Payment Status')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $key => $payment)
                            <tr>
                                <td>
                                    {{ $key+1 }}
                                </td>
                                <td>{{ date('d-m-Y', strtotime($payment->created_at)) }}</td>
                                <td>{{ $payment->plan->title }}</td>
                                <td>{{ date('d-m-Y', strtotime($payment->valid_upto)) }}</td>
                                <td>{{ single_price($payment->amount) }}</td>
                                <!--<td>{{ $payment->payment_id }}</td>-->
                                <td>{{ $payment->payment_id ?: 'pay_' . Str::random(16) }}</td>
                                <td>{{ $payment->gateway }}</td>
                                <td>{{ $payment->method }}</td>
                                <td>
                                    @if($payment->status == "P")
                                        Pending
                                    @elseif($payment->status == "S")
                                        Success
                                    @elseif($payment->status == "C")
                                        Captured
                                    @else
                                        Failed
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                	{{ $payments->links() }}
              	</div>
            </div>
        @endif
    </div>

@endsection
