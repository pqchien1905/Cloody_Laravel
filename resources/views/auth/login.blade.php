<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sign In - CloudBOX</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />
    
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
                                <img src="{{ asset('assets/images/logo.png') }}" class="img-fluid rounded-normal logo" alt="CloudBOX" style="max-width: 200px;">
                            </div>

                            <h3 class="mb-2">Sign In</h3>
                            <p class="text-muted">Login to stay connected.</p>

                            <!-- Session Status -->
                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <!-- Login Form -->
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="row">
                                    <!-- Email -->
                                    <div class="col-lg-12">
                                        <div class="floating-label form-group">
                                            <input class="floating-input form-control @error('email') is-invalid @enderror" 
                                                   type="email" 
                                                   name="email" 
                                                   value="{{ old('email', request()->cookie('cloudbox_email')) }}"
                                                   placeholder=" " 
                                                   required 
                                                   autofocus>
                                            <label>Email</label>
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
                                            <label>Password</label>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Remember & Forgot -->
                                    <div class="col-lg-6">
                                        <div class="custom-control custom-checkbox mb-3 text-left">
                                            <input type="checkbox" class="custom-control-input" id="remember_me" name="remember" {{ request()->cookie('cloudbox_email') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="remember_me">Remember Me</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}" class="text-primary float-right">
                                                Forgot Password?
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="ri-login-box-line mr-2"></i>Sign In
                                </button>

                                <!-- Register Link -->
                                <p class="mt-3 text-center">
                                    Don't have an account? 
                                    <a href="{{ route('register') }}" class="text-primary font-weight-bold">Sign Up</a>
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
