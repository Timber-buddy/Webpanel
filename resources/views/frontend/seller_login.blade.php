@extends('backend.layouts.layout')

@section('content')
<style>
    .error {
        color: red;
    }
</style>
<div class="h-100 bg-cover bg-center py-5 d-flex align-items-center" style="background-image: url({{ uploaded_asset(get_setting('seller_login_page_bg')) }})">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-xl-4 mx-auto">
                <div class="card text-left">
                    <div class="card-body">
                        <div class="mb-5 text-center">
                            @if(get_setting('system_logo_black') != null)
                                <img src="{{ uploaded_asset(get_setting('system_logo_black')) }}" class="mw-100 mb-4" height="40">
                            @else
                                <img src="{{ static_asset('assets/img/logo.png') }}" class="mw-100 mb-4" height="40">
                            @endif
                            <h1 class="fs-20 fs-md-24 fw-700 text-primary">{{ translate('Welcome Back !')}}</h1>
                            <h5 class="fs-14 fw-400 text-dark">{{ translate('Login To Your Seller Account')}}</h5>
                        </div>
                        <form class="pad-hor" onsubmit="return validateForm()" method="POST" role="form" action="{{ route('login') }}">
                            @csrf
                            <!-- Email or Phone -->
                            <div class="form-group">
                                <label for="email" class="fs-12 fw-500 text-secondary">{{  translate('Email') }}</label>
                                <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }} rounded-0" value="{{ old('email') }}" placeholder="{{  translate('johndoe@example.com') }}" name="email" id="email" autocomplete="off">
                                <span id="emailError" class="error"></span>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <!-- password -->
                            <div class="form-group">
                                <label for="password" class="fs-12 fw-500 text-secondary">{{  translate('Password') }}</label>
                                <input type="password" class="form-control rounded-0 {{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ translate('Password')}}" name="password" id="password">
                                <span id="passwordError" class="error"></span>
                            </div>

                            <div class="row mb-2">
                                <!-- Remember Me -->
                                <div class="col-6">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <span class="has-transition fs-12 fw-400 text-gray-dark hov-text-primary">{{  translate('Remember Me') }}</span>
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                                <!-- Forgot password -->
                                <div class="col-6 text-right">
                                    <a href="{{ route('password.request') }}" class="text-reset fs-12 fw-400 text-gray-dark hov-text-primary"><u>{{ translate('Forgot password?')}}</u></a>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="mb-4 mt-4">
                                <button type="submit" class="btn btn-primary btn-block fw-700 fs-14 rounded-4">{{  translate('Login') }}</button>
                            </div>
                        </form>
                        @if (env("DEMO_MODE") == "On")
                            <div class="mt-4">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td>seller@example.com</td>
                                            <td>123456</td>
                                            <td><button class="btn btn-info btn-xs" onclick="autoFill()">{{ translate('Copy') }}</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
    <script type="text/javascript">

    function validateForm() {
            // Reset error messages
            document.getElementById('emailError').textContent = '';
            document.getElementById('passwordError').textContent = '';

            // Get form values
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;

            // Validate email and password
            if (email === '') {
                document.getElementById('emailError').textContent = 'Email is required';
                AIZ.plugins.notify('danger', 'Email is required');
                return false;
            }

            if (password === '') {
                document.getElementById('passwordError').textContent = 'Password is required';
                AIZ.plugins.notify('danger', 'Password is required');
                return false;
            }

            // Get CSRF token from meta tag
            var csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

            // Send AJAX request for email existence check
            $.ajax({
                type: 'POST',
                url: '{{ route('check_email') }}',
                data: {
                    email: email,
                    password: password,
                    _token: csrfToken
                },
                success: function(response) {
                    if (!response.exists) {
                        document.getElementById('emailError').textContent = 'Invalid Email';
                        AIZ.plugins.notify('danger', 'Invalid Email');
                        return false;
                    } else if (!response.emailexists) {
                        document.getElementById('passwordError').textContent = 'Invalid Password';
                        AIZ.plugins.notify('danger', 'Invalid Password');
                        return false;
                    }
                },
                error: function(error) {
                    console.error(error);
                    // Handle error, display user-friendly message
                    AIZ.plugins.notify('danger', 'Error checking email Or Password existence');
                }
            });
            // Prevent the form from being submitted here, as it will be handled in the AJAX success callback
            return true;
        }

        function autoFill(){
            $('#email').val('seller@example.com');
            $('#password').val('123456');
        }
    </script>
@endsection

