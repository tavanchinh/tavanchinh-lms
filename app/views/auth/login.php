<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập | Tạ Văn Chinh CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; height: 100vh; display: flex; align-items: center; }
        .login-card { border: none; border-radius: 1.5rem; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); max-width: 1000px; width: 100%; margin: auto; }
        .login-side-image { background: linear-gradient(135deg, #0d6efd 0%, #003d99 100%); color: white; display: flex; flex-direction: column; justify-content: center; padding: 3rem; }
        .form-section { background: white; padding: 3.5rem; }
        .form-control { padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #dee2e6; }
        .form-control:focus { box-shadow: 0 0 0 0.25 darkred; border-color: #0d6efd; }
        .btn-login { padding: 0.8rem; border-radius: 0.75rem; font-weight: 600; transition: all 0.3s; }
        .btn-google { background: white; border: 1px solid #dee2e6; color: #444; }
        .btn-google:hover { background: #f8f9fa; }
        .btn-zalo { background: #0068ff; color: white; border: none; }
        .btn-zalo:hover { background: #0056d2; color: white; }
        .social-icon { width: 20px; margin-right: 10px; }
        .divider { display: flex; align-items: center; text-align: center; margin: 1.5rem 0; color: #888; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #dee2e6; }
        .divider:not(:empty)::before { margin-right: .5rem; }
        .divider:not(:empty)::after { margin-left: .5rem; }
        @media (max-width: 768px) { .login-side-image { display: none; } }
    </style>
</head>
<body>

<div class="container">
    <div class="login-card row g-0">
        <div class="col-md-5 login-side-image">
            <h2 class="fw-bold mb-4">Chào mừng trở lại!</h2>
            <p class="opacity-75">Hệ thống quản lý đào tạo trực tuyến về SketchUp ABF và sản xuất CNC.</p>
            <div class="mt-auto">
                <small class="opacity-50">© 2026 tavanchinh.com</small>
            </div>
        </div>

        <div class="col-md-7 form-section">
            <div class="mb-4">
                <h3 class="fw-bold text-dark">Đăng nhập</h3>
                <p class="text-muted">Nhập thông tin để truy cập vào hệ thống</p>
            </div>

            <?php 
                $saved_email = $_COOKIE['user_email'] ?? ''; 
                $saved_pass  = $_COOKIE['user_pass'] ?? ''; 
                $is_remembered = isset($_COOKIE['user_email']) ? 'checked' : '';
            ?>

            <form action="/login-process" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= $saved_email ?>" placeholder="tendangnhap@gmail.com" required>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label class="form-label small fw-bold">Mật khẩu</label>
                        <a href="/forgot-password" class="small text-decoration-none">Quên mật khẩu?</a>
                    </div>
                    <input type="password" name="password" class="form-control" value="<?= $saved_pass ?>" placeholder="••••••••" required>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked <?= $is_remembered ?>>
                    <label class="form-check-label small" for="remember" >Ghi nhớ đăng nhập</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-login mb-3">Đăng nhập ngay</button>

                <div class="divider small">HOẶC ĐĂNG NHẬP VỚI</div>

                <div class="row mt-2">
                    <div class="">
                        <a href="/login-google" class="btn btn-google w-100 btn-login d-flex align-items-center justify-content-center small">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" class="social-icon"> Google
                        </a>
                    </div>
                </div>
            </form>
            
            <p class="text-center mt-4 small text-muted">
                Chưa có tài khoản? <a href="/dang-ky" class="text-primary fw-bold text-decoration-none">Đăng ký ngay</a>
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($error)): ?>
<script>
    Swal.fire({ icon: 'error', title: 'Đăng nhập thất bại', text: '<?= $error ?>', confirmButtonColor: '#0d6efd' });
</script>
<?php endif; ?>

</body>
</html>