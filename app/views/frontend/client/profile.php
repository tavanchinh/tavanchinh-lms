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
                        
                        <form action="/profile/update" method="POST">
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
                                    <hr class="my-3 opacity-10">
                                    <label class="form-label fw-bold text-primary small">Mật khẩu mới (Bỏ trống nếu giữ nguyên)</label>
                                    <input type="password" name="new_password" class="form-control rounded-3" placeholder="••••••••">
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
    </Script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>