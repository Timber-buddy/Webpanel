@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{translate('Subscriptions')}}</h1>
    </div>
</div>


<div class="card">
    <form class="" id="sort_customers" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-0 h6">{{translate('Subscriptions')}}</h5>
            </div>
            <div class="col-md-2 text-right">
                @can('subscription_plan_create')
                    <div class="form-group mb-0">
                        <a href="javascript:void(0);" data-toggle="modal" data-target="#create_new" class="btn btn-primary">Create new</a>
                    </div>
                @endcan
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type title or duration & Enter') }}">
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <!--<th data-breakpoints="lg">#</th>-->
                        <th>
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-all">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        </th>
                        <th>{{translate('Title')}}</th>
                        <th data-breakpoints="lg">{{translate('Image')}}</th>
                        <th>{{translate('Duration')}}</th>
                        <th>{{translate('Product Limit')}}</th>
                        <th>{{translate('Price')}}</th>
                        <th>{{translate('Seller Count')}}</th>
                        <th data-breakpoints="lg">{{translate('Description')}}</th>
                        <th class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!is_null($subscriptions))
                        @foreach($subscriptions as $subscription)
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="aiz-checkbox-inline">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" class="check-one" name="id[]" value="{{$subscription->id}}">
                                                <span class="aiz-square-check"></span>
                                            </label>
                                        </div>
                                    </div>
                                </td>
                                <td>{{$subscription->title}}</td>
                                <td>
                                    <img src="{{asset('public/'.$subscription->image)}}" style="height:55px;" alt="">
                                </td>
                                <td>{{$subscription->duration}} Days</td>
                                <td>{{$subscription->product_limit}}</td>
                                <td>{{number_format($subscription->price, 2)}}</td>
                                <td>{{subscriptionUserCount($subscription->id)}}</td>
                                <td>{!! wordwrap($subscription->description, 70, "<br>") !!}</td>
                                <td style="text-align: right;">
                                    @can('subscription_plan_edit')
                                    <a href="javascript:void(0);" class="btn btn-soft-success btn-icon btn-circle btn-sm" onclick="setRecord(`{{route('subscriptions.show', $subscription->id)}}`)" title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                    @endcan
                                    &ensp;
                                    @can('subscription_plan_delete')
                                    <a href="javascript:void(0);" class="btn btn-soft-danger btn-icon btn-circle btn-sm" onclick="deleteRecord(`{{route('subscriptions.destroy', $subscription->id)}}`)" title="{{ translate('Delete') }}">
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

<div class="modal fade" id="create_new">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6">{{translate('Create New Plan')}}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{route('subscriptions.store')}}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>{{translate('Plan Title')}} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" value="" placeholder="{{translate('Plan Title')}}" required>
                        </div>
                        <div class="col-md-6">
                            <label>{{translate('Plan Image')}} <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="image" accept="image/*" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>{{translate('Product Limit')}} <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="product_limit" value="" placeholder="{{translate('Product Limit')}}" required min="1">
                        </div>
                        <div class="col-md-4">
                            <label>{{translate('Plan Duration (in Days)')}} <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="duration" value="" placeholder="{{translate('Plan Duration (in Days)')}}" required min="1">
                        </div>
                        <div class="col-md-4">
                            <label>{{translate('Plan Price')}} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="price" value="" placeholder="{{translate('Plan Price')}}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{translate('Plan Description')}} <span class="text-danger">*</span></label>
                            <textarea class="form-control" maxlength="300" rows="6" name="description" style="resize: none;" placeholder="{{translate('Plan Description')}}" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <button type="submit" name="sub" value="create" class="btn btn-success">{{translate('Create')}}</button>
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
                <p>{{translate('Do you really want to delete this subscription plan?')}}</p>
            </div>
            <div class="modal-footer">
                <form action="" method="post" id="confirmationdelete">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <button type="submit" name="btn" class="btn btn-primary">{{translate('Proceed!')}}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-record">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6">{{translate('Update Plan')}}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{route('subscriptions.store')}}" enctype="multipart/form-data" id="edit-form">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>{{translate('Plan Title')}} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" id="title" value="" placeholder="{{translate('Plan Title')}}" required>
                        </div>
                        <div class="col-md-6">
                            <label>{{translate('Plan Image')}}</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>{{translate('Product Limit')}} <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="product_limit" id="product_limit" value="" placeholder="{{translate('Product Limit')}}" required min="1">
                        </div>
                        <div class="col-md-4">
                            <label>{{translate('Plan Duration (in Days)')}} <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="duration" id="duration" value="" placeholder="{{translate('Plan Duration (in Days)')}}" required min="1">
                        </div>
                        <div class="col-md-4">
                            <label>{{translate('Plan Price')}} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="price" id="price" value="" placeholder="{{translate('Plan Price')}}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>{{translate('Plan Description')}} <span class="text-danger">*</span></label>
                            <textarea class="form-control" maxlength="300" rows="6" name="description" id="description" style="resize: none;" placeholder="{{translate('Plan Description')}}" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <button type="submit" name="sub" value="create" class="btn btn-success">{{translate('Update')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">

        $(document).on("change", ".check-all", function() {
            if(this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });

        function sort_customers(el){
            $('#sort_customers').submit();
        }

        function deleteRecord(url)
        {
            $('#confirm-delete').modal('show', {backdrop: 'static'});
            document.getElementById('confirmationdelete').setAttribute('action' , url);
        }

        function setRecord(url)
        {
            $.get(url, function(result){
                document.getElementById('edit-form').setAttribute('action' , result.url);
                $("#title").val(result.plan.title);
                $("#product_limit").val(result.plan.product_limit);
                $("#duration").val(result.plan.duration);
                $("#price").val(result.plan.price);
                $("#description").html(result.plan.description);

                $('#edit-record').modal('show', {backdrop: 'static'});
            });
        }
    </script>
@endsection
