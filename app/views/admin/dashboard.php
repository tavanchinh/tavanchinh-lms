<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; color: #0d6efd !important; }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    </style>
</head>
<body>

<?php include __DIR__ . '/layouts/header.php'; ?>

<div class="container-fluid">
    <?php if (isset($_GET['assign_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> Gán khóa học cho học viên thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'already_assigned'): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> Học viên này đã sở hữu khóa học này rồi.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <div class="row">
        
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Danh sách Khóa học</h5>
                    <a href="/admin/courses/create" class="btn btn-primary btn-sm">+ Thêm mới</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tên khóa học</th>
                                    <th>Trình độ</th>
                                    <th>Giá</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td>
                                        <strong><?= $course['name'] ?></strong>
                                        <div class="small text-muted"><?= $course['summary'] ?></div>
                                    </td>
                                    <td><span class="badge bg-info text-dark"><?= ucfirst($course['level']) ?></span></td>
                                    <td><?= number_format($course['price'], 0, ',', '.') ?>đ</td>
                                    <td><?= $course['status'] == 1 ? '<span class="text-success">● Đang chạy</span>' : '<span class="text-danger">● Tạm dừng</span>' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Danh sách Học viên</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($users as $user): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold"><?= $user['name'] ?></div>
                                <div class="small text-muted"><?= $user['email'] ?></div>
                            </div>
                            <span class="badge bg-secondary rounded-pill"><?= $user['role'] ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="/admin/accounts?tab=student" class="small text-decoration-none">Xem tất cả học viên</a>
                </div>
            </div>

            <div class="card mt-4 bg-primary text-white">
                <div class="card-body text-center">
                    <h6>Tiện ích nhanh</h6>
                    <button class="btn btn-light btn-sm w-100 mb-2" onclick="location.href='/assign-course'">Gán khóa học cho học viên</button>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>