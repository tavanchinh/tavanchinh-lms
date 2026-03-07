<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Hệ thống CMS</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; border: none; color: white; border-radius: 4px; cursor: pointer; }
        button:hover { background: #218838; }
        .error { color: red; font-size: 14px; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Đăng nhập</h2>
    
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php 
        $saved_email = $_COOKIE['user_email'] ?? ''; 
        $saved_pass  = $_COOKIE['user_pass'] ?? ''; 
        $is_remembered = isset($_COOKIE['user_email']) ? 'checked' : '';
    ?>

    <form action="/login-process" method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required value="<?php echo $saved_email; ?>" placeholder="admin@example.com">
        </div>
        <div class="form-group">
            <label>Mật khẩu</label>
            <input type="password" name="password" required value="<?php echo $saved_pass; ?>" placeholder="******">
        </div>

        <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
            <input type="checkbox" name="remember" id="remember" <?php echo $is_remembered; ?> style="width: auto; cursor: pointer;">
            <label for="remember" style="margin-bottom: 0; cursor: pointer; font-size: 14px;">Ghi nhớ đăng nhập</label>
        </div>

        <button type="submit">Đăng nhập</button>
    </form>
</div>

</body>
</html>