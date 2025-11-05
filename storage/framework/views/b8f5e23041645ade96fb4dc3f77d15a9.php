<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sign In - CloudBOX</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo e(asset('assets/images/favicon.ico')); ?>" />
    
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/backend-plugin.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/backend.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/remixicon/fonts/remixicon.css')); ?>">
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
                                <img src="<?php echo e(asset('assets/images/logo.png')); ?>" class="img-fluid rounded-normal logo" alt="CloudBOX" style="max-width: 200px;">
                            </div>

                            <h3 class="mb-2">Sign In</h3>
                            <p class="text-muted">Login to stay connected.</p>

                            <!-- Session Status -->
                            <?php if(session('status')): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo e(session('status')); ?>

                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <!-- Login Form -->
                            <form method="POST" action="<?php echo e(route('login')); ?>">
                                <?php echo csrf_field(); ?>
                                <div class="row">
                                    <!-- Email -->
                                    <div class="col-lg-12">
                                        <div class="floating-label form-group">
                                            <input class="floating-input form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   type="email" 
                                                   name="email" 
                                                   value="<?php echo e(old('email', request()->cookie('cloudbox_email'))); ?>"
                                                   placeholder=" " 
                                                   required 
                                                   autofocus>
                                            <label>Email</label>
                                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="col-lg-12">
                                        <div class="floating-label form-group">
                                            <input class="floating-input form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   type="password" 
                                                   name="password" 
                                                   placeholder=" " 
                                                   required>
                                            <label>Password</label>
                                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>

                                    <!-- Remember & Forgot -->
                                    <div class="col-lg-6">
                                        <div class="custom-control custom-checkbox mb-3 text-left">
                                            <input type="checkbox" class="custom-control-input" id="remember_me" name="remember" <?php echo e(request()->cookie('cloudbox_email') ? 'checked' : ''); ?>>
                                            <label class="custom-control-label" for="remember_me">Remember Me</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <?php if(Route::has('password.request')): ?>
                                            <a href="<?php echo e(route('password.request')); ?>" class="text-primary float-right">
                                                Forgot Password?
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="ri-login-box-line mr-2"></i>Sign In
                                </button>

                                <!-- Register Link -->
                                <p class="mt-3 text-center">
                                    Don't have an account? 
                                    <a href="<?php echo e(route('register')); ?>" class="text-primary font-weight-bold">Sign Up</a>
                                </p>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Scripts -->
    <script src="<?php echo e(asset('assets/js/backend-bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/app.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\laragon\www\cloudbox-laravel1\cloudbox-laravel\resources\views/auth/login.blade.php ENDPATH**/ ?>