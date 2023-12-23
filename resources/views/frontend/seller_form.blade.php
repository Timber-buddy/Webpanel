@extends('frontend.layouts.app')

@section('content')
    <section class="pt-4 mb-4">
        <div class="container text-center">
            <div class="row">
                <div class="col-lg-6 text-center text-lg-left">
                    {{-- <h1 class="fw-700 fs-24 text-dark">{{ translate('Register your Organization')}}</h1> --}}
                </div>
                <div class="col-lg-6">
                    <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                        <li class="breadcrumb-item has-transition opacity-50 hov-opacity-100">
                            <a class="text-reset" href="{{ route('home') }}">{{ translate('Home') }}</a>
                        </li>
                        <li class="text-dark fw-600 breadcrumb-item">
                            "{{ translate('Register your Organization') }}"
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="pt-4 mb-4">
        <div class="container">
            <div class="row">
                <div class="col-xxl-5 col-xl-6 col-md-8 mx-auto">
                    <h1 class="fw-700 fs-20 fs-md-24 text-dark text-center mb-3">
                        {{ translate('Register Your Organization') }}</h1>
                    <form id="shop" class="" onsubmit="return validateForm()" action="{{ route('shops.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white border mb-4">
                            <div class="fs-15 fw-600 p-3">
                                {{ translate('Personal Info') }}
                            </div>
                            <div class="p-3">
                                <div class="form-group">
                                    <label>{{ translate('Your Name') }} <span class="text-primary">*</span></label>
                                    <input type="text"
                                        class="form-control rounded-0{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                        value="{{ old('name') }}" placeholder="{{ translate('Name') }}" name="name"
                                        required>
                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('Your Email') }} <span class="text-primary">*</span></label>
                                    <input type="email"
                                        class="form-control rounded-0{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                        value="{{ old('email') }}" placeholder="{{ translate('Email') }}" name="email"
                                        id="email" required>
                                    <span id="emailError" class="error"></span>
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('Your Password') }} <span class="text-primary">*</span></label>
                                    <input type="password"
                                        class="form-control rounded-0{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                        value="{{ old('password') }}" placeholder="{{ translate('Password') }}"
                                        name="password" required>
                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('Repeat Password') }} <span class="text-primary">*</span></label>
                                    <input type="password" class="form-control rounded-0"
                                        placeholder="{{ translate('Confirm Password') }}" name="password_confirmation"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border mb-4">
                            <div class="fs-15 fw-600 p-3">
                                {{ translate('Basic Info') }}
                            </div>
                            <div class="p-3">
                                <div class="form-group">
                                    <label>{{ translate('Organization Name') }} <span class="text-primary">*</span></label>
                                    <input type="text"
                                        class="form-control rounded-0{{ $errors->has('shop_name') ? ' is-invalid' : '' }}"
                                        value="{{ old('shop_name') }}" placeholder="{{ translate('Organization Name') }}"
                                        name="shop_name" required>
                                    @if ($errors->has('shop_name'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('shop_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('City') }} <span class="text-primary">*</span></label>
                                    <select class="form-control form-control-sm aiz-selectpicker rounded-0"
                                        data-live-search="true" name="city" required>
                                        <option value="">{{ translate('Locations') }}</option>
                                        @php
                                            $response = getLocations();

                                            $locations = $response['locations'];
                                            if (Session()->has('selected_location')) {
                                                $selected_location = Session()->get('selected_location');
                                            } else {
                                                $selected_location = 0;
                                            }
                                        @endphp

                                        @if (count($locations) > 0)
                                            @foreach ($locations as $location)
                                                @if (!is_null($location->cities))
                                                    @foreach ($location->cities as $city)
                                                        <option value="{{ $city->id }}"
                                                            @isset($selected_location) @if ($selected_location == $city->id) selected @endif @endisset>
                                                            {{ $city->name }}, {{ $location->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                    @if ($errors->has('city'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('city') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('GST Number') }} <span class="text-primary">*</span></label>
                                    <input type="text"
                                        class="form-control rounded-0{{ $errors->has('gst_number') ? ' is-invalid' : '' }}"
                                        value="{{ old('gst_number') }}" placeholder="{{ translate('GST Number') }}"
                                        name="gst_number" id="gst_number" required onkeyup="gstNumberValidation()">
                                    <span class="text-danger mb-3" id="errGst"></span>
                                    @if ($errors->has('gst_number'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('gst_number') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>{{ translate('Subscription Plan') }}</label>
                                    <select class="form-control form-control-sm aiz-selectpicker rounded-0"
                                        data-live-search="true" name="subscription_plan"
                                        onchange="getPlanDetails(this.value)">
                                        <option value="">Select Plan</option>
                                        @if (!is_null($subscriptionPlans))
                                            {{-- @foreach ($subscriptionPlans as $plan)
                                                <option value="{{ $plan->id }}">
                                                    {{ $plan->title }} (₹{{ number_format($plan->price, 2) }} for
                                                    {{ $plan->duration }} days or {{ $plan->product_limit }} products)
                                                </option>
                                            @endforeach --}}
                                        @foreach ($subscriptionPlans as $plan)
                                            <option value="{{ $plan->id }}" {{ $plan->is_default == 1 ? 'selected' : '' }}>
                                                {{ $plan->title }} (₹{{ number_format($plan->price, 2) }} for
                                                {{ $plan->duration }} days or {{ $plan->product_limit }} products)
                                            </option>
                                        @endforeach

                                        @endif
                                    </select>
                                    <p id="plan" class="pt-2"></p>
                                    @if ($errors->has('subscription_plan'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('subscription_plan') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" id="decleration" required>
                                        {{ translate('I agree that all the above information is valid and correct') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        @if (get_setting('google_recaptcha') == 1)
                            <div class="form-group mt-2 mx-auto row">
                                <div class="g-recaptcha" data-sitekey="{{ env('CAPTCHA_KEY') }}"></div>
                            </div>
                        @endif

                        <div class="text-right">
                            <button type="submit" id="subBtn"
                                class="btn btn-primary fw-600 rounded-0">{{ translate('Register Your Organization') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('script')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script type="text/javascript">
        function validateForm() {
            document.getElementById('emailError').textContent = '';
            var email = document.getElementById('email').value;
            var csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

            var isEmailValid = true;

            $.ajax({
                type: 'POST',
                url: '{{ route('check_email') }}',
                data: {
                    email: email,
                    _token: csrfToken
                },
                async: false, // Make the request synchronous
                success: function(response) {
                    if (response.exists) {
                        document.getElementById('emailError').textContent = 'Email already exists';
                        AIZ.plugins.notify('danger', 'Email already exists');
                        isEmailValid = false;
                    }
                },
                error: function(error) {
                    console.error(error);
                    AIZ.plugins.notify('danger', 'Error checking email or password existence');
                    isEmailValid = false;
                }
            });

            return isEmailValid;
        }


        // making the CAPTCHA  a required field for form submission
        $(document).ready(function() {
            $("#shop").on("submit", function(evt) {
                var response = grecaptcha.getResponse();
                if (response.length == 0) {
                    //reCaptcha not verified
                    alert("please verify you are humann!");
                    evt.preventDefault();
                    return false;
                }
                //captcha verified
                //do the rest of your validations here
                $("#reg-form").submit();
            });
        });

        function getPlanDetails(id) {
            if (id.length > 0) {
                $.get('{{ url('seller/planDetails') }}/' + id, function(data) {
                    $("#plan").empty().html(data);
                });

                $("#subBtn").html("{{ translate('Proceed to Pay') }}");
            } else {
                $("#plan").empty();
                $("#subBtn").html("{{ translate('Register Your Organization') }}");
            }
        }

        function gstNumberValidation() {
            var regex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
            var str = document.getElementById('gst_number').value;

            if (str.length == 0) {
                $("#subBtn").attr('disabled', 'disabled');
                $("#errGst").html('<b>GST Number Required</b>');
            } else {
                if (regex.test(str) == true) {
                    $("#subBtn").removeAttr('disabled');
                    $("#errGst").empty();
                } else {
                    $("#subBtn").attr('disabled', 'disabled');
                    $("#errGst").html('<b>Invalid GST Number</b>');
                }
            }
        }
    </script>
@endsection
