@extends('backend.layouts.app')

@section('content')

<div class="col-lg-12 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Role Information')}}</h5>
        </div>
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="name">{{translate('Name')}}</label>
                    <div class="col-md-9">
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Permissions') }}</h5>
                </div>
                <br>
                @php
                    $permission_groups =  \App\Models\Permission::all()->groupBy('section');
                    $addons = array("offline_payment", "club_point", "pos_system", "paytm", "seller_subscription", "otp_system", "refund_request", "affiliate_system", "african_pg", "delivery_boy", "auction", "wholesale");
                @endphp
                @foreach ($permission_groups as $key => $permission_group)
                    @php
                        $show_permission_group = true;

                        if(in_array($permission_group[0]['section'], $addons)){

                            if (addon_is_activated($permission_group[0]['section']) == false) {
                                $show_permission_group = false;
                            }
                        }
                    @endphp
                    @if($show_permission_group)
                        <ul class="list-group mb-4">
                            @if($permission_group[0]['section'] !== 'sale')
                            <li class="list-group-item bg-light" aria-current="true">{{ translate(Str::headline($permission_group[0]['section'])) }}</li>
                            <li class="list-group-item">
                                <div class="row">
                                    @foreach ($permission_group as $key => $permission)
                                        @if ($permission->name !== 'show_digital_products' && $permission->name !== 'add_digital_product' && $permission->name !== 'edit_digital_product' && $permission->name !== 'delete_digital_product'  && $permission->name !== 'download_digital_product' && $permission->name !== 'pay_to_seller' && $permission->name !== 'seller_payment_history' && $permission->name !== 'view_seller_payout_requests' && $permission->name !== 'seller_commission_configuration' && $permission->name !== 'seller_verification_form_configuration' && $permission->name !== 'wallet_transaction_report' && $permission->name !== 'commission_history_report' && $permission->name !== 'language_setup' && $permission->name !== 'currency_setup'
                                         && $permission->name !== 'pickup_point_setup' && $permission->name !== 'order_configuration' && $permission->name !== 'shipping_configuration' && $permission->name !== 'shipping_country_setting' && $permission->name !== 'manage_shipping_states' && $permission->name !== 'manage_shipping_cities' && $permission->name !== 'manage_zones' && $permission->name !== 'manage_carriers' && $permission->name !== 'view_classified_products' && $permission->name !== 'publish_classified_product'  && $permission->name !== 'delete_classified_product' && $permission->name !== 'view_classified_packages' && $permission->name !== 'add_classified_package'&& $permission->name !== 'edit_classified_package'&& $permission->name !== 'delete_classified_package')
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                            <div class="p-2 border mt-1 mb-2">
                                                <label class="control-label d-flex">{{ translate(Str::headline($permission->name)) }}</label>
                                                <label class="aiz-switch aiz-switch-success">
                                                    <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="{{ $permission->id }}">
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </li>
                            @endif
                        </ul>
                    @endif
                @endforeach

                <div class="form-group mb-3 mt-3 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
