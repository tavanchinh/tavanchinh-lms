<?php 
  // Tự động lấy giao thức (http hoặc https)
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
  // Tự động lấy domain (ví dụ: dev.tavanchinh.com)
  $host = $_SERVER['HTTP_HOST'];
  // URL đầy đủ của trang hiện tại
  $fullUrl = $protocol . $host . $_SERVER['REQUEST_URI'];
  // Đường dẫn gốc của ảnh
  $baseUrl = $protocol . $host;
  $meta_description = isset($meta_description) ? $meta_description : 'Đào tạo phần mềm CNC thực chiến';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Học SketchUp & CNC Thực Chiến' ?></title>

    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= $title ?? 'Học SketchUp & CNC Thực Chiến' ?>">
    <meta property="og:description" content="<?= mb_substr(strip_tags($meta_description), 0, 200) ?>...">
    <meta property="og:url" content="<?= $fullUrl ?>">
    <meta property="og:image" content="<?= isset($course['image']) ? $baseUrl.'/uploads/'.$course['image'] : $baseUrl.'/uploads/default-share.jpg' ?>">

    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="vi_VN">

    <link rel="icon" href="/uploads/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/main.css?v=1.2">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-TZ8EXCEZ63"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-TZ8EXCEZ63');
    </script>
</head>

<body>
    <nav class="navbar navbar-expand-lg sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <span class="text-primary">CHINH</span>.EDU.VN
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php $current_route = $_SERVER['REQUEST_URI']; ?>
                    <li class="nav-item"><a class="nav-link <?= $current_route === '/' ? 'active-custom' : '' ?>" href="/">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_route === '/tai-lieu' ? 'active-custom' : '' ?>" href="/tai-lieu">Tài liệu</a></li>
                    <?php if (isset($currentUser) && $currentUser !== null): ?>
    
                        <li class="nav-item">
                            <a class="nav-link text-dark <?= ($current_route ?? '') === '/khoa-hoc-cua-toi' ? 'active-custom' : '' ?>" href="/khoa-hoc-cua-toi">
                                <i class="bi bi-play-circle me-1"></i> Khóa học của tôi
                            </a>
                        </li>

                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle d-flex align-items-center bg-light rounded-pill px-3 py-1 border shadow-sm" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="avatar-circle me-2 bg-primary text-white d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 30px; height: 30px; font-size: 0.85rem; font-weight: bold;">
                                    <?= mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <span class="text-dark fw-bold" style="font-size: 0.9rem;">
                                    <?= htmlspecialchars($_SESSION['user_name'] ?? 'Thành viên') ?>
                                </span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end mt-2 shadow border-0">
                                <li><a class="dropdown-item py-2" href="/trang-ca-nhan"><i class="bi bi-person me-2"></i>Trang cá nhân</a></li>
                                
                                <?php 
                                    $userRole = $_SESSION['user_role'] ?? '';
                                    if ($userRole === 'admin' || $userRole === 'staff'): 
                                ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header text-uppercase text-primary" style="font-size: 0.65rem; letter-spacing: 1px;">Quản trị & Vận hành</h6></li>
                                    
                                    <li><a class="dropdown-item fw-bold" href="/admin/accounts">
                                        <i class="bi bi-speedometer2 me-2"></i>Quản trị hệ thống
                                    </a></li>
                                    
                                    <li><a class="dropdown-item" href="/admin/study">
                                        <i class="bi bi-mortarboard me-2"></i>Quản lý học tập
                                    </a></li>
                                <?php endif; ?>
                                
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger py-2" href="/dang-xuat"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                            </ul>
                        </li>

                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-login ms-lg-4 px-4 py-2 fw-bold" href="/dang-nhap">Đăng nhập</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3">
        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 py-3" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-octagon-fill fs-4 me-3"></i>
                    <div><?= $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 py-3" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <div><?= $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>