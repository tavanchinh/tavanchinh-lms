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
    <h2>CMS LOGIN</h2>
    
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="/login-process" method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="admin@example.com">
        </div>
        <div class="form-group">
            <label>Mật khẩu</label>
            <input type="password" name="password" required placeholder="******">
        </div>
        <button type="submit">Đăng nhập</button>
    </form>
</div>

</body>
</html>