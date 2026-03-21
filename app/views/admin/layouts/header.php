<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Hệ thống' ?> - TAVANCHINH</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/backend.css?v=1.1">
    <link rel="icon" href="/uploads/favicon.ico" type="image/x-icon">
</head>
<body>

<?php 
// Kiểm tra xem trang hiện tại có phải là trang quản trị không
$isAdminPage = strpos($_SERVER['REQUEST_URI'], 'admin') !== false || isset($isBackend);
?>

<?php if (!$isAdminPage): ?>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-main mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">TAVANCHINH</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/dashboard"><i class="bi bi-house"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/courses"><i class="bi bi-play-btn"></i> Khóa học</a></li>
                    <li class="nav-item"><a class="nav-link" href="/blog"><i class="bi bi-newspaper"></i> Bài viết</a></li>
                    
                    <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'staff'])): ?>
                        <li class="nav-item">
                            <a class="nav-link nav-link-admin" href="/admin/courses"><i class="bi bi-shield-lock"></i> Backend</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center text-white">
                    <span class="me-3">Chào, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Khách') ?></strong></span>
                    <a href="/logout" class="btn btn-outline-danger btn-sm">Thoát</a>
                </div>
            </div>
        </div>
    </nav>
    <main class="container"> <?php else: ?>
    <div class="admin-wrapper">
        <div id="sidebar-overlay"></div>
        <?php $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>

        <nav id="sidebar">
            <div class="sidebar-header">
                <h5 class="mb-0">CMS MANAGER</h5>
                <small class="text-warning"><?= strtoupper($_SESSION['user_role']) ?></small>
            </div>
            <ul class="list-unstyled">
                <li class="<?= ($currentUri == '/admin') ? 'active' : '' ?>">
                    <a href="/admin"><i class="bi bi-speedometer2 me-2"></i> Tổng quan</a>
                </li>
                <li class="<?= ($currentUri == '/admin/courses') ? 'active' : '' ?>">
                    <a href="/admin/courses"><i class="bi bi-journal-text me-2"></i> Khóa học</a>
                </li>
                <li class="<?= ($currentUri == '/admin/accounts') ? 'active' : '' ?>">
                    <a href="/admin/accounts"><i class="bi bi-people me-2"></i> Tài khoản</a>
                </li>
                <li class="<?= ($currentUri == '/admin/study') ? 'active' : '' ?>">
                    <a href="/admin/study"><i class="bi bi-mortarboard me-2"></i> Học tập</a>
                </li>
                
                <hr class="border-light opacity-10">
                
                <li><a href="/"><i class="bi bi-arrow-left-circle me-2"></i> Ra trang chủ</a></li>
                <li><a href="/dang-xuat" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a></li>
            </ul>
        </nav>
        <div id="content">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <button type="button" id="sidebarCollapse" class="btn btn-outline-dark">
                    <i class="bi bi-list"></i>
                </button>
                <h4 class="mb-0"><?= $title ?? 'Quản trị' ?></h4>
            </div>
<?php endif; ?>
<script>
    

    document.addEventListener("DOMContentLoaded", function () {
        const btn = document.getElementById('sidebarCollapse');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        const overlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            content.classList.toggle('active');
            overlay.classList.toggle('show'); // Hiện/ẩn lớp mờ
            console.log('Sidebar toggled. Current state:', sidebar.classList.contains('active') ? 'Active' : 'Inactive');
        }

        // Click vào nút menu
        btn.addEventListener('click', toggleSidebar);

        // Click vào vùng mờ (Overlay) để đóng menu
        overlay.addEventListener('click', toggleSidebar);
    });
</script>