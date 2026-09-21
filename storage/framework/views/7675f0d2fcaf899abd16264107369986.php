<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Login - GenZ Live / Zaldoris</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('assets/favicon.png')); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Outfit', sans-serif; }
        body {
            background: #0f1017;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .admin-login-card {
            background: #181926;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            width: 100%;
            max-width: 440px;
            padding: 40px 32px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
            position: relative;
            overflow: hidden;
        }
        .admin-login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #FE2C55, #25F4EE);
        }
        .input-group {
            margin-bottom: 18px;
        }
        .input-group label {
            display: block;
            font-size: 13px;
            color: #8e90a6;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .input-group input {
            width: 100%;
            background: #13141f;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }
        .input-group input:focus {
            border-color: #FE2C55;
        }
        .login-submit-btn {
            width: 100%;
            background: #FE2C55;
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            box-shadow: 0 4px 16px rgba(254, 44, 85, 0.4);
        }
        .login-submit-btn:hover {
            background: #ff436b;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <div class="admin-login-card">
        <div style="text-align: center; margin-bottom: 28px;">
            <div style="font-size: 26px; font-weight: 900; margin-bottom: 4px;">
                GenZ <span style="color: #FE2C55;">Live</span>
            </div>
            <p style="color: #8e90a6; font-size: 13px;">Super Admin Control Portal</p>
        </div>

        <?php if($errors->any()): ?>
            <div style="background: rgba(254, 44, 85, 0.15); border: 1px solid #FE2C55; color: #FE2C55; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 20px;">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <?php if(session('success')): ?>
            <div style="background: rgba(37, 244, 238, 0.15); border: 1px solid #25F4EE; color: #25F4EE; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 20px;">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('admin.login')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="input-group">
                <label>Admin Email Address</label>
                <input type="email" name="email" value="<?php echo e(old('email', 'admin@zaldoris.com')); ?>" required placeholder="admin@zaldoris.com">
            </div>

            <div class="input-group">
                <label>Admin Password</label>
                <input type="password" name="password" value="password" required placeholder="••••••••">
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; font-size: 12px; color: #8e90a6;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                    <input type="checkbox" name="remember" checked style="accent-color: #FE2C55;"> Remember session
                </label>
                <span style="color: #25F4EE;">Master Clearance</span>
            </div>

            <button type="submit" class="login-submit-btn">
                <i class="bi bi-shield-lock-fill"></i> Log In to Admin Panel
            </button>
        </form>

        <div style="margin-top: 24px; text-align: center; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 16px; font-size: 12px; color: #666;">
            Demo Credentials: <span style="color: #25F4EE;">admin@zaldoris.com</span> / <span style="color: #25F4EE;">password</span>
        </div>
    </div>

</body>
</html>
<?php /**PATH C:\wamp64\www\zaldoris-tiktok\resources\views/admin/login.blade.php ENDPATH**/ ?>