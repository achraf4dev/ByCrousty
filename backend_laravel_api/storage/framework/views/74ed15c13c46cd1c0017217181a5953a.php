

<?php $__env->startSection('title', 'Iniciar Sesión Admin'); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('admin.login')); ?>">
    <?php echo csrf_field(); ?>
    
    <div class="login-input-group">
        <i class="bi bi-envelope"></i>
        <input type="email" 
               class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
               name="email" 
               placeholder="Dirección de Correo" 
               value="<?php echo e(old('email')); ?>" 
               required 
               autofocus>
    </div>
    
    <div class="login-input-group">
        <i class="bi bi-lock"></i>
        <input type="password" 
               class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
               name="password" 
               placeholder="Contraseña" 
               required>
    </div>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">
                Recordarme
            </label>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary login-btn">
        <i class="bi bi-box-arrow-in-right me-2"></i>
        Iniciar Sesión
    </button>
</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ashraf\Desktop\pj\ByCrousty\backend_laravel_api\resources\views/admin/auth/login.blade.php ENDPATH**/ ?>