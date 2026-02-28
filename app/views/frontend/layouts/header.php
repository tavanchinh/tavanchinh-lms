<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Học trực tuyến' ?> - TAVANCHINH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --primary-color: #2563eb; --header-height: 65px; }
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        .navbar-frontend { height: var(--header-height); background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .nav-link { font-weight: 500; color: #475569 !important; padding: 0.5rem 1rem !important; }
        .nav-link:hover, .nav-link.active { color: var(--primary-color) !important; }
        .user-avatar { width: 35px; height: 35px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #64748b; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-frontend sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold text-primary fs-4" href="/">TAVANCHINH</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#feNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="feNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item">
                    <a class="nav-link" href="/documents"><i class="bi bi-file-earmark-text me-1"></i> Tài liệu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/all-courses"><i class="bi bi-grid me-1"></i> Khóa học</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], 'my-courses') !== false) ? 'active' : '' ?>" href="/my-courses">
                        <i class="bi bi-play-btn me-1"></i> Khóa học của tôi
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <div class="d-flex align-items-center btn btn-link text-decoration-none dropdown-toggle p-0" data-bs-toggle="dropdown">
                        <span class="me-2 d-none d-lg-block text-dark fw-semibold small"><?= $_SESSION['user_name'] ?? 'Học viên' ?></span>
                        <div class="user-avatar">
                            <?= mb_substr($_SESSION['user_name'] ?? 'H', 0, 1) ?>
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                        <li><a class="dropdown-item py-2" href="/profile"><i class="bi bi-person me-2"></i> Hồ sơ cá nhân</a></li>
                        <li><a class="dropdown-item py-2" href="/settings"><i class="bi bi-gear me-2"></i> Cài đặt</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>