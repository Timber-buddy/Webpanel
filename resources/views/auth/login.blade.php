@extends('backend.layouts.layout')

@section('content')
    <style>
        .error {
            color: red;
        }
    </style>
    <div class="h-100 bg-cover bg-center py-5 d-flex align-items-center"
        style="background-image: url({{ uploaded_asset(get_setting('admin_login_background')) }})">
        <div class="container">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="row">
                <div class="col-lg-6 col-xl-4 mx-auto">
                    <div class="card text-left">
                        <div class="card-body">
                            <div class="mb-5 text-center">
                                @if (get_setting('system_logo_black') != null)
                                    <img src="{{ uploaded_asset(get_setting('system_logo_black')) }}" class="mw-100 mb-4"
                                        height="40">
                                @else
                                    <img src="{{ static_asset('assets/img/logo.png') }}" class="mw-100 mb-4" height="40">
                                @endif
                                <h1 class="h3 text-primary mb-0">{{ translate('Welcome to') }} {{ env('APP_NAME') }}</h1>
                                <p>{{ translate('Login to your account.') }}</p>
                            </div>
                            <form class="pad-hor" onsubmit="return validateForm()" action="{{ route('login') }}"
                                method="POST" role="form">
                                @csrf
                                <div class="form-group">
                                    <input id="email" type="email"
                                        class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                                        value="{{ old('email') }}" autofocus placeholder="{{ translate('Email') }}">
                                    <span id="emailError" class="error"></span>
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <input id="password" type="password"
                                        class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                        name="password" placeholder="{{ translate('Password') }}">
                                    <span id="passwordError" class="error"></span>
                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <div class="text-left">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" name="remember" id="remember"
                                                    {{ old('remember') ? 'checked' : '' }}>
                                                <span>{{ translate('Remember Me') }}</span>
                                                <span class="aiz-square-check"></span>
                                            </label>
                                        </div>
                                    </div>
                                    @if (env('MAIL_USERNAME') != null && env('MAIL_PASSWORD') != null)
                                    @endif
                                    <div class="col-sm-6">
                                        <div class="text-right">
                                            <a href="{{ route('password.request') }}"
                                                class="text-reset fs-14">{{ translate('Forgot password ?') }}</a>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg btn-block">
                                    {{ translate('Login') }}
                                </button>
                            </form>
                            @if (env('DEMO_MODE') == 'On')
                                <div class="mt-4">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td>admin@example.com</td>
                                                <td>123456</td>
                                                <td><button class="btn btn-info btn-xs"
                                                        onclick="autoFill()">{{ translate('Copy') }}</button></td>
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

        function autoFill() {
            $('#email').val('admin@example.com');
            $('#password').val('123456');
        }
    </script>
@endsection
