<?php include __DIR__ . '/../layouts/header.php'; ?>


<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm p-3 text-center rounded-4 sticky-lg-top" style="top: 100px; z-index: 10;">
                <div class="position-relative d-inline-block mx-auto mb-3">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['name']) ?>&background=0D6EFD&color=fff" 
                         class="rounded-circle border" width="100" height="100">
                </div>
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($user['name']) ?></h5>
                <p class="text-muted small mb-4"><?= htmlspecialchars($user['email']) ?></p>
                
                <div class="list-group list-group-flush text-start custom-nav-tabs">
                    <a href="#tab-courses" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 <?= $activeTab == 'courses' ? 'active' : '' ?>" data-bs-toggle="list">
                        <i class="bi bi-play-circle me-2"></i> Khóa học của tôi
                    </a>
                    <a href="#tab-settings" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 <?= $activeTab == 'settings' ? 'active' : '' ?>" data-bs-toggle="list">
                        <i class="bi bi-person-gear me-2"></i> Cài đặt tài khoản
                    </a>
                    <a href="/dang-xuat" class="list-group-item list-group-item-action border-0 rounded-3 text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> Cập nhật thông tin thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                </div>
            <?php endif; ?>
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php 
                        // Hiển thị nội dung lỗi dựa trên mã lỗi truyền về
                        switch($_GET['error']) {
                            case 'password_mismatch':
                                echo 'Mật khẩu xác nhận không khớp, vui lòng kiểm tra lại!';
                                break;
                            case 'wrong_password':
                                echo 'Mật khẩu hiện tại không chính xác!';
                                break;
                            case 'password_too_short':
                                echo 'Mật khẩu quá ngắn!';
                                break;
                            default:
                                echo 'Đã có lỗi xảy ra, vui lòng thử lại sau!';
                        }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                </div>
            <?php endif; ?>

            <div class="tab-content">
                <div class="tab-pane fade show <?= $activeTab == 'courses' ? 'show active' : '' ?>" id="tab-courses">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Khóa học đang theo học</h4>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                            <?= count($myCourses ?? []) ?> Khóa học
                        </span>
                    </div>
                    
                    <?php if (!empty($myCourses)): ?>
                        <div class="row g-3">
                            <?php foreach ($myCourses as $course): ?>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm rounded-4 overflow-hidden course-learning-card">
                                    <div class="row g-0">
                                        <div class="col-md-3 position-relative">
                                            <img src="/uploads/<?= $course['image'] ?: 'default.jpg' ?>" 
                                                 class="img-fluid h-100" style="object-fit: cover; min-height: 140px;">
                                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center opacity-0 opacity-hover">
                                                <i class="bi bi-play-circle-fill text-white fs-1"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="card-body p-4">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="fw-bold mb-0 text-dark pe-3">
                                                        <?= htmlspecialchars($course['name']) ?>
                                                    </h5>
                                                    <a href="/<?= $course['slug'] ?>" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold">Học tiếp</a>
                                                </div>
                                                
                                                <p class="text-muted small mb-3">
                                                    <i class="bi bi-clock me-1"></i> Cập nhật lần cuối: <?= date('d/m/Y') ?>
                                                </p>
                                                
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="progress flex-grow-1" style="height: 10px;">
                                                        <div class="progress-bar bg-success shadow-sm" 
                                                            role="progressbar" 
                                                            style="width: <?= $course['progress_percent'] ?>%" 
                                                            aria-valuenow="<?= $course['progress_percent'] ?>" 
                                                            aria-valuemin="0" 
                                                            aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span class="fw-bold small text-success">
                                                        <?= $course['progress_percent'] ?>%
                                                    </span>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-dashed">
                            <div class="mb-4">
                                <i class="bi bi-journal-x text-muted" style="font-size: 4rem; opacity: 0.2;"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Bạn chưa tham gia khóa học nào!</h5>
                            <p class="text-muted mb-4 mx-auto" style="max-width: 400px;">
                                Khám phá kho tài liệu CNC, SketchUp thực chiến ngay.
                            </p>
                            <a href="/" class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow">
                                Xem danh sách khóa học
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade <?= $activeTab == 'settings' ? 'show active' : '' ?>" id="tab-settings">
                    <div class="card border-0 shadow-sm p-4 rounded-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary-subtle p-2 rounded-3 me-3">
                                <i class="bi bi-person-vcard text-primary fs-4"></i>
                            </div>
                            <h4 class="fw-bold mb-0">Cài đặt tài khoản</h4>
                        </div>
                        
                        <form action="/cap-nhat-thong-tin" id="form_update_profile" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Họ và tên</label>
                                    <input type="text" name="name" class="form-control rounded-3" value="<?= htmlspecialchars($user['name']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Số điện thoại</label>
                                    <input type="text" name="phone_number" class="form-control rounded-3" value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-muted">Email (Tài khoản đăng nhập)</label>
                                    <input type="email" class="form-control bg-light rounded-3" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                                </div>
                                <div class="col-12">
                                    <hr class="my-4 opacity-10">
                                    
                                    <div class="form-check form-switch mb-3 d-flex align-items-center gap-2">
                                        <input class="form-check-input" type="checkbox" id="enableChangePassword" 
                                            style="cursor: pointer; width: 2.5rem; height: 1.25rem; margin-top: 0;">
                                        <label class="form-check-label fw-bold text-primary small mb-0" for="enableChangePassword" style="cursor: pointer;">
                                            Thay đổi mật khẩu tài khoản
                                        </label>
                                    </div>

                                    <div id="passwordFields" style="display: none;">
                                        <div class="row g-3 animate__animated animate__fadeInUp">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Mật khẩu mới</label>
                                                <input type="password" name="new_password" id="new_password" 
                                                    class="form-control rounded-3" placeholder="Tối thiểu 6 ký tự">
                                                <div id="passwordStrength" class="small mt-1" style="display: none;"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Xác nhận mật khẩu mới</label>
                                                <input type="password" name="confirm_password" id="confirm_password" 
                                                    class="form-control rounded-3" placeholder="Nhập lại mật khẩu mới">
                                                <div id="passwordError" class="text-danger small mt-1" style="display: none;">Mật khẩu không khớp!</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm py-2">
                                        Lưu thay đổi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<Script>
    // Hiệu ứng hover cho card khóa học
    document.querySelectorAll('.course-learning-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.querySelector('.opacity-hover').classList.add('opacity-100');
        });
        card.addEventListener('mouseleave', () => {
            card.querySelector('.opacity-hover').classList.remove('opacity-100');
        });
    });


    document.addEventListener('DOMContentLoaded', function() {
        const passwordSwitch = document.getElementById('enableChangePassword');
        const passwordFields = document.getElementById('passwordFields');
        const newPass = document.getElementById('new_password');
        const strengthText = document.getElementById('passwordStrength');
        const confirmPass = document.getElementById('confirm_password');
        const passError = document.getElementById('passwordError');
        const profileForm = document.getElementById('form_update_profile');

        // 1. Xử lý Ẩn/Hiện khi gạt Switch
        passwordSwitch.addEventListener('change', function() {
            if (this.checked) {
                passwordFields.style.display = 'block';
                newPass.setAttribute('required', 'required');
                confirmPass.setAttribute('required', 'required');
            } else {
                passwordFields.style.display = 'none';
                newPass.removeAttribute('required');
                confirmPass.removeAttribute('required');
                // Xóa giá trị đã nhập khi tắt switch
                newPass.value = '';
                confirmPass.value = '';
                passError.style.display = 'none';
            }
        });

        // Hàm kiểm tra độ mạnh
        newPass.addEventListener('input', function() {
            const val = this.value;
            if (val.length === 0) {
                strengthText.style.display = 'none';
                return;
            }

            strengthText.style.display = 'block';
            
            if (val.length < 6) {
                strengthText.innerHTML = '<i class="bi bi-x-circle-fill"></i> Quá ngắn (tối thiểu 6 ký tự)';
                strengthText.className = 'small mt-1 text-danger';
                newPass.classList.add('is-invalid');
            } else if (val.length < 10) {
                strengthText.innerHTML = '<i class="bi bi-exclamation-circle-fill"></i> Độ mạnh: Trung bình';
                strengthText.className = 'small mt-1 text-warning';
                newPass.classList.remove('is-invalid');
                newPass.classList.add('is-valid');
            } else {
                strengthText.innerHTML = '<i class="bi bi-check-circle-fill"></i> Độ mạnh: Rất tốt';
                strengthText.className = 'small mt-1 text-success';
                newPass.classList.remove('is-invalid');
                newPass.classList.add('is-valid');
            }
        });

        // Chặn submit nếu bật switch mà mật khẩu < 6 ký tự
        profileForm.addEventListener('submit', function(e) {
            if (passwordSwitch.checked && newPass.value.length < 6) {
                e.preventDefault();
                alert('Mật khẩu phải từ 6 ký tự trở lên anh nhé!');
                newPass.focus();
            }
        });

        // 2. Kiểm tra mật khẩu khớp nhau trước khi submit
        profileForm.addEventListener('submit', function(e) {
            if (passwordSwitch.checked) {
                if (newPass.value !== confirmPass.value) {
                    e.preventDefault(); // Dừng gửi form
                    passError.style.display = 'block';
                    confirmPass.classList.add('is-invalid');
                    confirmPass.focus();
                }
            }
        });

        // 3. Ẩn thông báo lỗi khi đang gõ lại
        confirmPass.addEventListener('input', function() {
            passError.style.display = 'none';
            confirmPass.classList.remove('is-invalid');
        });
    });
    </Script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>