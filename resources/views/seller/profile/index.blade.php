@extends('seller.layouts.app')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Manage Profile') }}</h1>
        </div>
      </div>
    </div>
    <form action="{{ route('seller.profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        <input name="_method" type="hidden" value="POST">
        @csrf
        <!-- Basic Info-->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Basic Info')}}</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="name">{{ translate('Your Name') }}</label>
                    <div class="col-md-10">
                        <input type="text" name="name" value="{{ $user->name }}" id="name" class="form-control" placeholder="{{ translate('Your Name') }}" required>
                        @error('name')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="phone">{{ translate('Your Phone') }}</label>
                    <div class="col-md-10">
                        <input type="text" name="phone" value="{{ $user->phone }}" id="phone" class="form-control" placeholder="{{ translate('Your Phone')}}">
                        @error('phone')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label">{{ translate('Photo') }}</label>
                    <div class="col-md-10">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="photo" value="{{ $user->avatar_original }}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="password">{{ translate('Your Password') }}</label>
                    <div class="col-md-10">
                        <input type="password" name="new_password" id="password" class="form-control" placeholder="{{ translate('New Password') }}">
                        @error('new_password')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="confirm_password">{{ translate('Confirm Password') }}</label>
                    <div class="col-md-10">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="{{ translate('Confirm Password') }}" >
                        @error('confirm_password')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

            </div>
        </div>

        @if($user->user_type == "seller")
        <!-- Payment System -->
        <!--
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Payment Setting')}}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <label class="col-md-3 col-form-label">{{ translate('Cash Payment') }}</label>
                    <div class="col-md-9">
                        <label class="aiz-switch aiz-switch-success mb-3">
                            <input value="1" name="cash_on_delivery_status" type="checkbox" @if ($user->shop->cash_on_delivery_status == 1) checked @endif>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label">{{ translate('Bank Payment') }}</label>
                    <div class="col-md-9">
                        <label class="aiz-switch aiz-switch-success mb-3">
                            <input value="1" name="bank_payment_status" type="checkbox" @if ($user->shop->bank_payment_status == 1) checked @endif>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="bank_name">{{ translate('Bank Name') }}</label>
                    <div class="col-md-9">
                        <input type="text" name="bank_name" value="{{ $user->shop->bank_name }}" id="bank_name" class="form-control mb-3" placeholder="{{ translate('Bank Name')}}">
                        @error('phone')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="bank_acc_name">{{ translate('Bank Account Name') }}</label>
                    <div class="col-md-9">
                        <input type="text" name="bank_acc_name" value="{{ $user->shop->bank_acc_name }}" id="bank_acc_name" class="form-control mb-3" placeholder="{{ translate('Bank Account Name')}}">
                        @error('bank_acc_name')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="bank_acc_no">{{ translate('Bank Account Number') }}</label>
                    <div class="col-md-9">
                        <input type="text" name="bank_acc_no" value="{{ $user->shop->bank_acc_no }}" id="bank_acc_no" class="form-control mb-3" placeholder="{{ translate('Bank Account Number')}}">
                        @error('bank_acc_no')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="bank_routing_no">{{ translate('Bank Routing Number') }}</label>
                    <div class="col-md-9">
                        <input type="number" name="bank_routing_no" value="{{ $user->shop->bank_routing_no }}" id="bank_routing_no" lang="en" class="form-control mb-3" placeholder="{{ translate('Bank Routing Number')}}">
                        @error('bank_routing_no')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        -->
        @endif

        <div class="form-group mb-0 text-right">
            <button type="submit" class="btn btn-primary">{{translate('Update Profile')}}</button>
        </div>
    </form>

    <br>

    @if($user->user_type == "seller")
    <!-- Address -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Address')}}</h5>
        </div>
        <div class="card-body">
            <div class="row gutters-10">
                @foreach ($addresses as $key => $address)
                    <div class="col-lg-4">
                        <div class="border p-3 pr-5 rounded mb-3 position-relative">
                            <div>
                                <span class="w-50 fw-600">{{ translate('Address') }}:</span>
                                <span class="ml-2">{{ $address->address }}</span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">{{ translate('Postal Code') }}:</span>
                                <span class="ml-2">{{ $address->postal_code }}</span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">{{ translate('City') }}:</span>
                                <span class="ml-2">{{ optional($address->city)->name }}</span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">{{ translate('State') }}:</span>
                                <span class="ml-2">{{ optional($address->state)->name }}</span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">{{ translate('Country') }}:</span>
                                <span class="ml-2">{{ optional($address->country)->name }}</span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">{{ translate('Phone') }}:</span>
                                <span class="ml-2">{{ $address->phone }}</span>
                            </div>
                            @if ($address->set_default)
                                <div class="position-absolute right-0 bottom-0 pr-2 pb-3">
                                    <span class="badge badge-inline badge-primary">{{ translate('Default') }}</span>
                                </div>
                            @endif
                            <div class="dropdown position-absolute right-0 top-0">
                                <button class="btn bg-gray px-2" type="button" data-toggle="dropdown">
                                    <i class="la la-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" onclick="edit_address('{{$address->id}}')">
                                        {{ translate('Edit') }}
                                    </a>
                                    @if (!$address->set_default)
                                        <a class="dropdown-item" href="{{ route('seller.addresses.set_default', $address->id) }}">{{ translate('Make This Default') }}</a>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('seller.addresses.destroy', $address->id) }}">{{ translate('Delete') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="col-lg-4 mx-auto" onclick="add_new_address()">
                    <div class="border p-3 rounded mb-3 c-pointer text-center bg-light">
                        <i class="la la-plus la-2x"></i>
                        <div class="alpha-7">{{ translate('Add New Address') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Change Email -->
    <form action="{{ route('user.change.email') }}" method="POST">
        @csrf
        <div class="card">
          <div class="card-header">
              <h5 class="mb-0 h6">{{ translate('Change your email')}}</h5>
          </div>
          <div class="card-body">
              <div class="row">
                  <div class="col-md-2">
                      <label>{{ translate('Your Email') }}</label>
                  </div>
                  <div class="col-md-10">
                      <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="{{ translate('Your Email')}}" name="email" value="{{ $user->email }}" />
                        <div class="input-group-append">
                           <button type="button" class="btn btn-outline-secondary new-email-verification">
                               <span class="d-none loading">
                                   <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>{{ translate('Sending Email...') }}
                               </span>
                               <span class="default">{{ translate('Verify') }}</span>
                           </button>
                        </div>
                      </div>
                      <div class="form-group mb-0 text-right">
                          <button type="submit" class="btn btn-primary">{{translate('Update Email')}}</button>
                      </div>
                  </div>
              </div>
          </div>
        </div>
    </form>

    @if($user->user_type == "seller")
    <!-- Change Email -->
    <form action="{{ route('seller.upgrade.package') }}" method="POST" id="upgrade-package-form">
        @csrf
        <div class="card">
          <div class="card-header">
              <h5 class="mb-0 h6">{{ translate('Upgrade Package')}}</h5>
          </div>
          <div class="card-body">
                @if(!is_null($subscription) || !empty($subscription))
                    <div class="row mb-3">
                        <div class="col-md-2">
                            Current Plan Details
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-2">
                                    <img src="{{asset('public/'.$subscription->plan->image)}}" alt="" class="img-fluid" style="max-height: 100px;">
                                </div>
                                <div class="col-md-10">
                                    <h6>{{$subscription->plan->title}}</h6>
                                    <p>{{$subscription->plan->description}}</p>
                                    <div class="row mb-1">
                                        <div class="col-md-6">
                                            Product Limit : {{$subscription->plan->product_limit}}
                                        </div>
                                        <div class="col-md-6">
                                            Amount : ₹{{number_format($subscription->amount, 2)}}
                                            @if($subscription->status == 'P')
                                                <span style="width: fit-content !important;" class="badge badge-warning">Pending</span>
                                            @elseif($subscription->status == 'C')
                                                <span style="width: fit-content !important;" class="badge badge-primary">Captured</span>
                                            @elseif($subscription->status == 'S')
                                                <span style="width: fit-content !important;" class="badge badge-success">Success</span>
                                            @else
                                                <span style="width: fit-content !important;" class="badge badge-danger">Failed</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            Purchase On : {{date('d F, Y', strtotime($subscription->purchase_at))}}
                                        </div>
                                        <div class="col-md-6">
                                            Valid Upto : {{date('d F, Y', strtotime($subscription->valid_upto))}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-2">
                        <label>{{ translate('Package') }}</label>
                    </div>
                    <div class="col-md-10">
                        <div class="input-group mb-3">
                            <select class="form-control form-control-sm aiz-selectpicker rounded-0" data-live-search="true" name="subscription_plan" required onchange="getPlanDetails(this.value)">
                                <option value="">Select Plan</option>
                                @if(!is_null($subscriptionPlans))
                                    @foreach($subscriptionPlans as $plan)
                                        <option value="{{$plan->id}}">
                                            {{$plan->title}} (₹{{number_format($plan->price, 2)}} for {{$plan->duration}} days or {{$plan->product_limit}} products)
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div id="plan">
                            <input type="hidden" id="selected_plan_amount" value="0">
                            <input type="hidden" id="selected_plan_product_limit" value="0">
                        </div>
                        <div class="form-group mb-0 text-right">
                            @if(!is_null($subscription) || !empty($subscription))
                                <button type="button" class="btn btn-primary" onclick="checkPackageStatus('{{$subscription->valid_upto}}', '{{$subscription->amount}}', '{{$active_product}}')">{{translate('Upgrade')}}</button>
                            @else
                                <button type="submit" class="btn btn-primary">{{translate('Purchase')}}</button>
                            @endif
                        </div>
                  </div>
              </div>
          </div>
        </div>
    </form>
    @endif

@endsection

@section('modal')
    {{-- New Address Modal --}}
    <div class="modal fade" id="new-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-default" role="form" action="{{ route('seller.addresses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Address')}}</label>
                                </div>
                                <div class="col-md-10">
                                    <textarea class="form-control mb-3" placeholder="{{ translate('Your Address')}}" rows="2" name="address" required></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Country')}}</label>
                                </div>
                                <div class="col-md-10">
                                    <div class="mb-3">
                                        <select class="form-control aiz-selectpicker" data-live-search="true" data-placeholder="{{ translate('Select your country') }}" name="country_id" required>
                                            <option value="">{{ translate('Select your country') }}</option>
                                            @foreach (\App\Models\Country::where('status', 1)->get() as $key => $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('State')}}</label>
                                </div>
                                <div class="col-md-10">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="state_id" required>

                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('City')}}</label>
                                </div>
                                <div class="col-md-10">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="city_id" required>

                                    </select>
                                </div>
                            </div>

                            @if (get_setting('google_map') == 1)
                                <div class="row">
                                    <input id="searchInput" class="controls" type="text" placeholder="{{translate('Enter a location')}}">
                                    <div id="map"></div>
                                    <ul id="geoData">
                                        <li style="display: none;">{{ translate('Full Address') }}: <span id="location"></span></li>
                                        <li style="display: none;">{{ translate('Postal Code') }}: <span id="postal_code"></span></li>
                                        <li style="display: none;">{{ translate('Country') }}: <span id="country"></span></li>
                                        <li style="display: none;">{{ translate('Latitude') }}: <span id="lat"></span></li>
                                        <li style="display: none;">{{ translate('Longitude') }}: <span id="lon"></span></li>
                                    </ul>
                                </div>

                                <div class="row">
                                    <div class="col-md-2" id="">
                                        <label for="exampleInputuname">{{ translate('Longitude') }}</label>
                                    </div>
                                    <div class="col-md-10" id="">
                                        <input type="text" class="form-control mb-3" id="longitude" name="longitude" readonly="">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2" id="">
                                        <label for="exampleInputuname">{{ translate('Latitude') }}</label>
                                    </div>
                                    <div class="col-md-10" id="">
                                        <input type="text" class="form-control mb-3" id="latitude" name="latitude" readonly="">
                                    </div>
                                </div>
                            @endif
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Postal code')}}</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3" placeholder="{{ translate('Your Postal Code')}}" name="postal_code" value="" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Phone')}}</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3" placeholder="{{ translate('+880')}}" name="phone" value="" required>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Address Modal --}}
    <div class="modal fade" id="edit-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body" id="edit_modal_body">

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="subscription-error-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('Important Notice') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3">
                        
                        <div class="row">
                            <div class="col-md-12" id="sub_err_msg">
                            </div>
                        </div>
                        <div class="form-group text-right">
                            <button type="button" class="btn btn-sm btn-primary" onclick="submitForm()">{{translate('Proceed')}}</button>
                            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal" onclick="location.reload();">{{translate('Cancel')}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">        
        $('.new-email-verification').on('click', function() {
            $(this).find('.loading').removeClass('d-none');
            $(this).find('.default').addClass('d-none');
            var email = $("input[name=email]").val();

            $.post('{{ route('user.new.verify') }}', {_token:'{{ csrf_token() }}', email: email}, function(data){
                data = JSON.parse(data);
                $('.default').removeClass('d-none');
                $('.loading').addClass('d-none');
                if(data.status == 2)
                    AIZ.plugins.notify('warning', data.message);
                else if(data.status == 1)
                    AIZ.plugins.notify('success', data.message);
                else
                    AIZ.plugins.notify('danger', data.message);
            });
        });

        function add_new_address(){
            $('#new-address-modal').modal('show');
        }

        function edit_address(address) {
            var url = '{{ route("seller.addresses.edit", ":id") }}';
            url = url.replace(':id', address);
            
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                success: function (response) {
                    $('#edit_modal_body').html(response.html);
                    $('#edit-address-modal').modal('show');
                    AIZ.plugins.bootstrapSelect('refresh');

                    @if (get_setting('google_map') == 1)
                        var lat     = -33.8688;
                        var long    = 151.2195;

                        if(response.data.address_data.latitude && response.data.address_data.longitude) {
                            lat     = parseFloat(response.data.address_data.latitude);
                            long    = parseFloat(response.data.address_data.longitude);
                        }

                        initialize(lat, long, 'edit_');
                    @endif
                }
            });
        }
        
        $(document).on('change', '[name=country_id]', function() {
            var country_id = $(this).val();
            get_states(country_id);
        });

        $(document).on('change', '[name=state_id]', function() {
            var state_id = $(this).val();
            get_city(state_id);
        });
        
        function get_states(country_id) {
            $('[name="state"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('seller.get-state')}}",
                type: 'POST',
                data: {
                    country_id  : country_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="state_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

        function get_city(state_id) {
            $('[name="city"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('seller.get-city')}}",
                type: 'POST',
                data: {
                    state_id: state_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="city_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

        function getPlanDetails(id) 
        {
            if (id.length > 0) 
            {
                $.get('{{url("seller/planDetails")}}/'+id, function(data){
                    $("#plan").empty().html(data);
                });
            }
            else
            {
                $("#plan").empty();
            }
        }

        function checkPackageStatus(expiry, amount, active_product) 
        {
            var date1 = new Date("{{date('Y-m-d')}}"); 
            var date2 = new Date(expiry); 

            var Difference_In_Time = date2.getTime() - date1.getTime();
            var diff = Difference_In_Time / (1000 * 3600 * 24);

            var selected_plan_amount = Number($("#selected_plan_amount").val());
            var selected_plan_product_limit = Number($("#selected_plan_product_limit").val());

            if(diff > 0 && selected_plan_amount < Number(amount))
            {
               var html = '<p><b>Attention:</b> Upgrading your plan will override all your previous plan settings and features. If you proceed with the upgrade, all existing plan configurations will be replaced by the new chosen plan. Please review your new plan carefully to ensure it meets your requirements.<br>By clicking "Proceed," you acknowledge that you understand and accept the consequences of upgrading your plan, including the override of previous plan details.</p>';

                if (active_product > selected_plan_product_limit) 
                {
                    html += '<p class="mt-2"><b>Attention:</b> According to your selected plan, you can publish up to '+selected_plan_product_limit+' products. As a result, '+ (active_product - selected_plan_product_limit) +' of your existing published products will be automatically unpublished. These products will no longer be visible to your customers.<br>By continuing, you acknowledge that '+ (active_product - selected_plan_product_limit) +' of your existing published products will be unpublished automatically based on your new plan limits.</p>';
                }
                else
                {
                    html += '<p class="mt-2"><b>Attention:</b> According to your selected plan, you can publish up to '+selected_plan_product_limit+' products. As a result, all of your existing published products will be automatically unpublished. These products will no longer be visible to your customers.<br>By continuing, you acknowledge that all of your existing published products will be unpublished automatically based on your new plan limits.</p>';
                }

                $("#sub_err_msg").empty().html(html);
                $("#subscription-error-modal").modal('show');
            }
            else if (diff > 0)
            {
                $("#sub_err_msg").empty().html('<p><b>Attention:</b> Upgrading your plan will override all your previous plan settings and features. If you proceed with the upgrade, all existing plan configurations will be replaced by the new chosen plan. Please review your new plan carefully to ensure it meets your requirements.<br>By clicking "Proceed," you acknowledge that you understand and accept the consequences of upgrading your plan, including the override of previous plan details.</p>');
                $("#subscription-error-modal").modal('show');
            }
            else if(selected_plan_amount < Number(amount))
            {
                if (active_product > selected_plan_product_limit) 
                {
                    $("#sub_err_msg").empty().html('<p><b>Attention:</b> According to your selected plan, you can publish up to '+selected_plan_product_limit+' products. As a result, '+ (active_product - selected_plan_product_limit) +' of your existing published products will be automatically unpublished. These products will no longer be visible to your customers.<br>By continuing, you acknowledge that '+ (active_product - selected_plan_product_limit) +' of your existing published products will be unpublished automatically based on your new plan limits.</p>');
                }
                else
                {
                    $("#sub_err_msg").empty().html('<p><b>Attention:</b> According to your selected plan, you can publish up to '+selected_plan_product_limit+' products. As a result, all of your existing published products will be automatically unpublished. These products will no longer be visible to your customers.<br>By continuing, you acknowledge that all of your existing published products will be unpublished automatically based on your new plan limits.</p>');
                }
                $("#subscription-error-modal").modal('show');
            }
            else
            {
                $("#upgrade-package-form").submit();
            }
        }

        function submitForm() 
        {
            $("#subscription-error-modal").modal('hide');
            $("#upgrade-package-form").submit();
        }
    </script>

    @if (get_setting('google_map') == 1)
        
        @include('frontend.partials.google_map')
        
    @endif

@endsection
