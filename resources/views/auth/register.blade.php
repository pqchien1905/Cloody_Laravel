<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ __('common.sign_up') }} - Cloody</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/cloud.png') }}" type="image/png" />
    <link rel="icon" href="{{ asset('assets/images/cloud.png') }}" type="image/png" />
    <link rel="apple-touch-icon" href="{{ asset('assets/images/cloud.png') }}" />
    
    <link rel="stylesheet" href="{{ asset('assets/css/backend-plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/backend.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/remixicon/fonts/remixicon.css') }}">
</head>
<body>
    <!-- Loader -->
    <div id="loading">
        <div id="loading-center"></div>
    </div>

    <div class="wrapper">
        <section class="login-content">
            <div class="container h-100">
                <div class="row justify-content-center align-items-center height-self-center">
                    <div class="col-md-5 col-sm-12 col-12 align-self-center">
                        <div class="sign-user_card">
                            <!-- Logo -->
                            <div class="text-center mb-4">
                                <img src="{{ asset('assets/images/Cloody.png') }}" class="img-fluid rounded-normal logo" alt="Cloody">
                            </div>

                            <h3 class="mb-2">{{ __('common.sign_up') }}</h3>
                            <p class="text-muted">{{ __('common.create_your_account') }}</p>

                            <!-- Register Form -->
                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="row">
                                    <!-- Name -->
                                    <div class="col-lg-12">
                                        <div class="floating-label form-group">
                                            <input class="floating-input form-control @error('name') is-invalid @enderror" 
                                                   type="text" 
                                                   name="name" 
                                                   value="{{ old('name') }}"
                                                   placeholder=" " 
                                                   required 
                                                   autofocus>
                                            <label>{{ __('common.full_name') }}</label>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-lg-12">
                                        <div class="floating-label form-group">
                                            <input class="floating-input form-control @error('email') is-invalid @enderror" 
                                                   type="email" 
                                                   name="email" 
                                                   value="{{ old('email') }}"
                                                   placeholder=" " 
                                                   required>
                                            <label>{{ __('common.email_address') }}</label>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="col-lg-12">
                                        <div class="floating-label form-group">
                                            <input class="floating-input form-control @error('password') is-invalid @enderror" 
                                                   type="password" 
                                                   name="password" 
                                                   placeholder=" " 
                                                   required>
                                            <label>{{ __('common.password') }}</label>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="col-lg-12">
                                        <div class="floating-label form-group">
                                            <input class="floating-input form-control" 
                                                   type="password" 
                                                   name="password_confirmation" 
                                                   placeholder=" " 
                                                   required>
                                            <label>{{ __('common.confirm_password') }}</label>
                                        </div>
                                    </div>

                                    <!-- Terms & Conditions -->
                                    <div class="col-lg-12">
                                        <div class="custom-control custom-checkbox mb-3">
                                            <input type="checkbox" class="custom-control-input" id="terms" required>
                                            <label class="custom-control-label" for="terms">
                                                {{ __('common.agree_to_terms') }} <a href="#" class="text-primary">{{ __('common.terms_and_conditions') }}</a>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="ri-user-add-line mr-2"></i>{{ __('common.create_account') }}
                                </button>

                                <!-- Login Link -->
                                <p class="mt-3 text-center">
                                    {{ __('common.already_have_account') }} 
                                    <a href="{{ route('login') }}" class="text-primary font-weight-bold">{{ __('common.sign_in') }}</a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/backend-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>
