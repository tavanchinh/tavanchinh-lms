<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khôi phục mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; height: 100vh; display: flex; align-items: center; }
        .login-card { border: none; border-radius: 1.5rem; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); max-width: 1000px; width: 100%; margin: auto;}
        .login-side-image { background: linear-gradient(135deg, #0d6efd 0%, #003d99 100%); color: white; display: flex; flex-direction: column; justify-content: center; padding: 3rem; }
        .form-section { background: white; padding: 3.5rem; }
        .form-control { padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #dee2e6; }
        .form-control:focus { box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15); border-color: #0d6efd; }
        .btn-login { padding: 0.8rem; border-radius: 0.75rem; font-weight: 600; transition: all 0.3s; }
        @media (max-width: 768px) { .login-side-image { display: none; } }
    </style>
</head>
<body>

<div class="container">
    <div class="login-card row g-0 shadow-lg">
        <div class="col-md-5 login-side-image">
            <h2 class="fw-bold mb-4">Khôi phục quyền truy cập</h2>
            <p class="opacity-75">Đừng lo lắng! Chỉ cần nhập email đã đăng ký, chúng tôi sẽ gửi liên kết để bạn thiết lập lại mật khẩu mới.</p>
            <div class="mt-auto">
                <small class="opacity-50">© 2026 tavanchinh.com</small>
            </div>
        </div>

        <div class="col-md-7 form-section">
            <div class="mb-5">
                <a href="/dang-nhap" class="text-decoration-none small fw-bold"><i class="bi bi-arrow-left"></i> Quay lại đăng nhập</a>
                <h3 class="fw-bold text-dark mt-3">Quên mật khẩu?</h3>
                <p class="text-muted">Nhập email để nhận hướng dẫn khôi phục</p>
            </div>

            <?php if (isset($_GET['sent'])): ?>
                <div class="alert alert-success border-0 rounded-4 p-3 mb-4 shadow-sm" role="alert">
                    <div class="d-flex">
                        <i class="bi bi-send-check-fill fs-4 me-3 text-success"></i>
                        <div>
                            <div class="fw-bold">Đã gửi email thành công!</div>
                            <small>Vui lòng kiểm tra hộp thư đến (hoặc hòm thư rác) để tiếp tục.</small>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger border-0 rounded-4 p-3 mb-4 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <?php 
                        echo ($_GET['error'] == 'token_expired') ? 'Liên kết đã hết hạn, vui lòng yêu cầu lại.' : 'Đã có lỗi xảy ra, vui lòng thử lại.'; 
                    ?>
                </div>
            <?php endif; ?>

            <form action="/forgot-password-process" method="POST">
                <div class="mb-4">
                    <label class="form-label small fw-bold text-dark">Địa chỉ Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3 px-3"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control border-start-0 rounded-end-3" placeholder="ten_email@gmail.com" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-login shadow-sm">
                    Gửi yêu cầu khôi phục
                </button>
            </form>
            
            <div class="mt-5 pt-3 border-top text-center">
                <p class="small text-muted mb-0">Hỗ trợ kỹ thuật: <strong>0972 808 368</strong></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>