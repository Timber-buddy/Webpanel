@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{translate('Subscription Purchase History')}}</h1>
    </div>
</div>


<div class="card">
    <form class="" id="sort_customers" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-0 h6">{{translate('Subscription Purchase History')}}</h5>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name or amount & Enter') }}">
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <!--<th data-breakpoints="lg">#</th>-->
                        <th>#</th>
                        <th>{{translate('Seller')}}</th>
                        <th>{{translate('Subscription Plan')}}</th>
                        <th data-breakpoints="lg">{{translate('Purchase Date')}}</th>
                        <th data-breakpoints="lg">{{translate('Valid Upto')}}</th>
                        <th>{{translate('Amount')}}</th>
                        <th>{{translate('Transaction ID')}}</th>
                        <th data-breakpoints="lg">{{translate('Gateway')}}</th>
                        <th data-breakpoints="lg">{{translate('Method')}}</th>
                        <th>{{translate('Transaction Status')}}</th>
                        <th class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!is_null($subscriptions))
                        @foreach($subscriptions as $key => $subscription)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>{{(!is_null($subscription->user)) ? $subscription->user->name: ''}}</td>
                                <td>{{$subscription->plan->title}}</td>
                                <td>{{date('d M, Y', strtotime($subscription->purchase_at))}}</td>
                                <td>{{date('d M, Y', strtotime($subscription->valid_upto))}}</td>
                                <td>{{number_format($subscription->amount, 2)}}</td>
                                <td>{{$subscription->payment_id}}</td>
                                <td>{{$subscription->gateway}}</td>
                                <td>{{$subscription->method}}</td>
                                <td>
                                    @if($subscription->status == 'P')
                                        Pending
                                    @elseif($subscription->status == 'C')
                                        Captured
                                    @elseif($subscription->status == 'S')
                                        Success
                                    @else
                                        Failed
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @can('purchase_history_edit')
                                        <a href="javascript:void(0);" class="btn btn-soft-success btn-icon btn-circle btn-sm" onclick="setRecord(`{{route('subscription.transaction.update', $subscription->id)}}`, `{{$subscription->status}}`)" title="{{ translate('Update Status') }}">
                                            <i class="las la-edit"></i>
                                        </a>
                                    @endcan
                                    &ensp;
                                    @can('purchase_history_delete')
                                        <a href="javascript:void(0);" class="btn btn-soft-danger btn-icon btn-circle btn-sm" onclick="deleteRecord(`{{route('subscription.transaction.destroy', $subscription->id)}}`)" title="{{ translate('Mark as Failed') }}">
                                            <i class="las la-trash"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $subscriptions->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="edit-transaction">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6">{{translate('Update Transaction Status')}}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data" id="edit-form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{translate('Status')}} <span class="text-danger">*</span></label>
                        <select class="form-control" name="status" required>
                            <option value="">Select Status</option>
                            <option value="P" id="status-p">Pending</option>
                            <option value="C" id="status-c">Captured</option>
                            <option value="S" id="status-s">Success</option>
                            <option value="F" id="status-f">Failed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <button type="submit" name="sub" value="create" id="updateBtn" class="btn btn-success">{{translate('Update')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="confirm-delete">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{translate('Do you really want to mark this transaction as failed?')}}</p>
            </div>
            <div class="modal-footer">
                <form action="" method="get" id="confirmationdelete">
                    @csrf
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <button type="submit" name="btn" class="btn btn-primary">{{translate('Proceed!')}}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
        function sort_customers(el){
            $('#sort_customers').submit();
        }

        function deleteRecord(url)
        {
            $('#confirm-delete').modal('show', {backdrop: 'static'});
            document.getElementById('confirmationdelete').setAttribute('action' , url);
        }

        function setRecord(url, status)
        {
            document.getElementById('edit-form').setAttribute('action' , url);

            if(status == "P")
            {
                $("#status-p").attr('selected', 'selected');
            }
            else if(status == "C")
            {
                $("#status-c").attr('selected', 'selected');
            }
            else if(status == "S")
            {
                $("#status-s").attr('selected', 'selected');
            }
            else if(status == "F")
            {
                $("#status-f").attr('selected', 'selected');
                $("#updateBtn").attr('disabled', 'disabled');
            }

            $('#edit-transaction').modal('show', {backdrop: 'static'});
        }
    </script>
@endsection
