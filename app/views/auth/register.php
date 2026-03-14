<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký thành viên | Tạ Văn Chinh CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; min-height: 100vh; display: flex; align-items: center; }
        .register-card { border: none; border-radius: 1.5rem; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); max-width: 1000px; width: 100%; margin: 2rem auto; }
        .register-side-image { background: linear-gradient(135deg, #198754 0%, #0d5031 100%); color: white; display: flex; flex-direction: column; justify-content: center; padding: 3rem; }
        .form-section { background: white; padding: 3rem; }
        .form-control { padding: 0.65rem 1rem; border-radius: 0.75rem; border: 1px solid #dee2e6; }
        .btn-register { padding: 0.8rem; border-radius: 0.75rem; font-weight: 600; background: #198754; border: none; transition: all 0.3s; }
        .btn-register:hover { background: #146c43; transform: translateY(-2px); }
        @media (max-width: 768px) { .register-side-image { display: none; } .form-section { padding: 2rem; } }
    </style>
</head>
<body>

<div class="container">
    <div class="register-card row g-0">
        <div class="col-md-5 register-side-image text-center">
            <div class="mb-4">
                <i class="bi bi-person-plus-fill" style="font-size: 4rem;"></i>
            </div>
            <h2 class="fw-bold mb-3">Gia nhập cộng đồng</h2>
            <p class="opacity-75">Đăng ký để bắt đầu hành trình làm chủ SketchUp ABF và quy trình sản xuất nội thất CNC chuyên nghiệp.</p>
            <div class="mt-5 text-start">
                <div class="d-flex mb-3">
                    <i class="bi bi-check-circle-fill me-2 text-warning"></i>
                    <span>Truy cập kho bài giảng độc quyền</span>
                </div>
                <div class="d-flex mb-3">
                    <i class="bi bi-check-circle-fill me-2 text-warning"></i>
                    <span>Tải file mẫu thực hành miễn phí</span>
                </div>
                <div class="d-flex mb-3">
                    <i class="bi bi-check-circle-fill me-2 text-warning"></i>
                    <span>Hỗ trợ kỹ thuật từ Tạ Văn Chinh</span>
                </div>
            </div>
        </div>

        <div class="col-md-7 form-section">
            <div class="mb-4">
                <h3 class="fw-bold text-dark">Tạo tài khoản mới</h3>
                <p class="text-muted small">Vui lòng điền chính xác thông tin dưới đây.</p>
            </div>

            <form id="registerForm" action="/register-process" method="POST">
                <input type="hidden" name="back_url" value="<?= htmlspecialchars($_GET['back'] ?? '/') ?>">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label small fw-bold">Họ và tên</label>
                        <input type="text" name="name" class="form-control" placeholder="VD: Nguyễn Văn An" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="VD: an@gmail.com" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Số điện thoại (Zalo)</label>
                    <input type="text" name="phone_number" class="form-control" placeholder="09xx xxx xxx" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Mật khẩu</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label small fw-bold">Xác nhận mật khẩu</label>
                        <input type="password" id="confirm_password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-register text-white">Đăng ký thành viên</button>
            </form>
            
            <p class="text-center mt-4 small text-muted">
                Đã có tài khoản? <a href="/dang-nhap" class="text-success fw-bold text-decoration-none">Đăng nhập tại đây</a>
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Chặn việc load lại trang của form mặc định

        const form = this;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');

        // Kiểm tra mật khẩu khớp nhau trước khi gửi (giữ lại logic cũ của bạn)
        const pass = document.getElementById('password').value;
        const confirmPass = document.getElementById('confirm_password').value;
        if (pass !== confirmPass) {
            Swal.fire('Lỗi!', 'Mật khẩu xác nhận không khớp.', 'error');
            return;
        }

        // Hiệu ứng Loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang xử lý...';

        fetch('/register-process', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // HIỆN THÔNG BÁO VÀ CHỜ ẤN OK
                Swal.fire({
                    title: 'Đăng ký thành công!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'Bắt đầu học ngay',
                    confirmButtonColor: '#198754',
                    allowOutsideClick: false // Ép người dùng phải ấn nút
                }).then((result) => {
                    if (result.isConfirmed) {
                        // CHỈ ĐIỀU HƯỚNG KHI ĐÃ ẤN OK
                        window.location.href = data.redirect;
                    }
                });
            } else {
                Swal.fire('Thất bại!', data.message, 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Đăng ký thành viên';
            }
        })
        .catch(error => {
            Swal.fire('Lỗi!', 'Không thể kết nối máy chủ.', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Đăng ký thành viên';
        });
    });
</script>


</body>
</html>