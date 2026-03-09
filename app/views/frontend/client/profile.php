<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm p-3 text-center rounded-4">
                <div class="position-relative d-inline-block mx-auto mb-3">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['name']) ?>&background=0D6EFD&color=fff" 
                         class="rounded-circle border" width="100" height="100">
                </div>
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($user['name']) ?></h5>
                <p class="text-muted small"><?= htmlspecialchars($user['email']) ?></p>
                
                <div class="list-group list-group-flush text-start mt-3">
                    <a href="#tab-courses" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 active" data-bs-toggle="list">
                        <i class="bi bi-book me-2"></i> Khóa học của tôi
                    </a>
                    <a href="#tab-settings" class="list-group-item list-group-item-action border-0 rounded-3 mb-1" data-bs-toggle="list">
                        <i class="bi bi-person-gear me-2"></i> Cài đặt tài khoản
                    </a>
                    <a href="/logout" class="list-group-item list-group-item-action border-0 rounded-3 text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                    Cập nhật thông tin thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-courses">
                    
                    
                    <?php if (!empty($myCourses)): ?>
                        <h4 class="fw-bold mb-4">Khóa học đang theo học</h4>
                        <div class="row g-3">
                            <?php foreach ($myCourses as $course): ?>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                                    <div class="d-flex align-items-center p-3">
                                        <img src="/uploads/<?= $course['image'] ?: 'default.jpg' ?>" 
                                            class="rounded" width="80" height="60" style="object-fit: cover;">
                                        <div class="ms-3 flex-grow-1">
                                            <h6 class="fw-bold mb-1 text-truncate" style="max-width: 180px;"><?= htmlspecialchars($course['name']) ?></h6>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-success" style="width: <?= $course['progress'] ?? 0 ?>%"></div>
                                            </div>
                                            <small class="text-muted" style="font-size: 11px;"><?= $course['progress'] ?? 0 ?>% hoàn thành</small>
                                        </div>
                                        <a href="/learning/<?= $course['slug'] ?>" class="btn btn-sm btn-primary rounded-pill">Học tiếp</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                            <div class="mb-4">
                                <i class="bi bi-journal-x text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Bạn chưa tham gia khóa học nào!</h5>
                            <p class="text-muted mb-4 mx-auto" style="max-width: 400px;">
                                Đừng để kiến thức dừng lại. Hãy khám phá các khóa học của Tạ Văn Chinh để nâng cao tay nghề ngay hôm nay.
                            </p>
                            <a href="/" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-search me-2"></i> Khám phá khóa học ngay
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="tab-settings">
                    <div class="card border-0 shadow-sm p-4 rounded-4">
                        <h4 class="fw-bold mb-4">Cài đặt tài khoản</h4>
                        <form action="/profile/update" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Họ và tên</label>
                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Số điện thoại</label>
                                    <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">Email (Không thể thay đổi)</label>
                                    <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                                </div>
                                <div class="col-12 mb-4">
                                    <label class="form-label fw-bold text-primary">Đổi mật khẩu mới (Để trống nếu không muốn đổi)</label>
                                    <input type="password" name="new_password" class="form-control" placeholder="Nhập mật khẩu mới">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">Lưu thay đổi</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>