<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Hệ thống Quản trị' ?> - TAVANCHINH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; min-height: 100vh; display: flex; flex-direction: column; }
        .navbar-brand { font-weight: bold; }
        .main-content { flex: 1; }
        footer { background: #212529; color: white; padding: 20px 0; margin-top: 40px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand text-primary" href="/dashboard">TAVANCHINH</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) ? 'active' : '' ?>" href="/dashboard">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/students">Học viên</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], 'courses') !== false) ? 'active' : '' ?>" href="/admin/courses">Khóa học</a>
                </li>
            </ul>
            <div class="d-flex align-items-center text-white">
                <span class="me-3 small text-secondary">Chào, <strong><?= $_SESSION['user_name'] ?? 'Admin' ?></strong></span>
                <a href="/logout" class="btn btn-outline-danger btn-sm">Thoát</a>
            </div>
        </div>
    </div>
</nav>
