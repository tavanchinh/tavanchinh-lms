<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; height: 100vh; display: flex; align-items: center; }
        .login-card { border: none; border-radius: 1.5rem; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); max-width: 1000px; width: 100%; margin: auto;}
        .login-side-image { background: linear-gradient(135deg, #198754 0%, #0a5131 100%); color: white; display: flex; flex-direction: column; justify-content: center; padding: 3rem; }
        .form-section { background: white; padding: 3.5rem; }
        .form-control { padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #dee2e6; }
        .form-control:focus { box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.15); border-color: #198754; }
        .btn-update { padding: 0.8rem; border-radius: 0.75rem; font-weight: 600; transition: all 0.3s; background-color: #198754; border: none; color: white; }
        .btn-update:hover { background-color: #157347; }
        .btn-update:disabled { background-color: #a5d6a7; cursor: not-allowed; }
        @media (max-width: 768px) { .login-side-image { display: none; } }
    </style>
</head>
<body>

<div class="container">
    <div class="login-card row g-0 shadow-lg">
        <div class="col-md-5 login-side-image">
            <h2 class="fw-bold mb-4">Mật khẩu mới</h2>
            <p class="opacity-75">Vui lòng thiết lập mật khẩu mới dễ nhớ nhưng đủ mạnh để bảo vệ quyền lợi học tập của anh/chị.</p>
            <div class="mt-auto">
                <small class="opacity-50">© 2026 tavanchinh.com</small>
            </div>
        </div>

        <div class="col-md-7 form-section">
            <div class="mb-5">
                <h3 class="fw-bold text-dark">Thiết lập mật khẩu</h3>
                <p class="text-muted small">Mật khẩu phải có ít nhất 6 ký tự</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger border-0 rounded-4 p-3 mb-4 shadow-sm small">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Dữ liệu không hợp lệ hoặc link đã hết hạn. Vui lòng thử lại.
                </div>
            <?php endif; ?>

            <form action="/reset-password-process" method="POST" id="resetForm">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Mật khẩu mới</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-shield-lock text-muted"></i></span>
                        <input type="password" name="password" id="password" class="form-control border-start-0 rounded-end-3" placeholder="••••••••" required minlength="6">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-dark">Xác nhận mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-check2-circle text-muted"></i></span>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control border-start-0 rounded-end-3" placeholder="••••••••" required>
                    </div>
                    <div id="passwordHelp" class="form-text small mt-2"></div>
                </div>

                <button type="submit" id="submitBtn" class="btn btn-update w-100 shadow-sm" disabled>
                    Cập nhật mật khẩu ngay
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const helpText = document.getElementById('passwordHelp');
    const submitBtn = document.getElementById('submitBtn');

    function validatePassword() {
        const pVal = password.value;
        const cpVal = confirmPassword.value;

        if (cpVal.length === 0) {
            helpText.innerHTML = '';
            submitBtn.disabled = true;
            return;
        }

        if (pVal === cpVal && pVal.length >= 6) {
            helpText.innerHTML = '<span class="text-success small"><i class="bi bi-check-circle-fill"></i> Mật khẩu hoàn toàn khớp!</span>';
            submitBtn.disabled = false;
        } else if (pVal.length < 6) {
            helpText.innerHTML = '<span class="text-danger small">Mật khẩu quá ngắn.</span>';
            submitBtn.disabled = true;
        } else {
            helpText.innerHTML = '<span class="text-danger small"><i class="bi bi-x-circle-fill"></i> Mật khẩu xác nhận chưa đúng.</span>';
            submitBtn.disabled = true;
        }
    }

    password.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', validatePassword);
</script>

</body>
</html>