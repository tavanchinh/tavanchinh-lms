

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
        
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">
                    <i class="bi bi-bar-chart-fill me-2 text-primary"></i>
                    Biểu đồ học viên đăng ký mới (30 ngày gần nhất)
                </div>
                <div class="card-body p-0">
                    <div class="chart-container" style="position: relative; width: 100%; min-height: 400px; padding: 15px;">
                        <canvas id="registrationChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Cảnh báo đăng nhập (Nhiều thiết bị)</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Học viên</th>
                                <th>Số thiết bị</th>
                                <th>Số IP</th>
                                <th>Lần cuối</th>
                                <th>Hành động </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($suspected_users as $s_user): ?>
                            <tr>
                                <td>
                                    <strong><?= $s_user['name'] ?></strong><br>
                                    <small class="text-muted"><?= $s_user['email'] ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $s_user['total_devices'] > 2 ? 'danger' : 'warning' ?>">
                                        <?= $s_user['total_devices'] ?> thiết bị
                                    </span>
                                </td>
                                <td class="text-center"><?= $s_user['total_ips'] ?> IP</td>
                                <td><small><?= date('H:i d/m', strtotime($s_user['last_login'])) ?></small></td>
                                <td class="text-center">
                                <a href="/admin/users/logs/<?= $s_user['id'] ?>" class="btn btn-sm btn-info">
                                    <i class="fa fa-eye"></i> Chi tiết
                                </a>
                            </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Danh sách Học viên mới</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($users as $user): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($user['name']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($user['email']) ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Đăng ký: <?= date('d/m/Y', strtotime($user['registered_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <span class="badge bg-secondary rounded-pill d-block mb-1"><?= $user['role'] ?></span>
                                    <small class="text-muted" style="font-size: 0.7rem;"><?= date('H:i', strtotime($user['registered_at'])) ?></small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="/admin/accounts?tab=student" class="small text-decoration-none">Xem tất cả học viên</a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-person-check-fill me-2"></i>Học viên đang Online
                    </h6>
                    <span class="badge bg-success rounded-pill">
                        <?= count($online_users) ?> đang hoạt động
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Học viên</th>
                                    <th>Hoạt động cuối</th>
                                    <th class="text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($online_users)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">Không có học viên nào online</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($online_users as $user): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold"><?= htmlspecialchars($user['name']) ?></div>
                                                <div class="text-muted small"><?= htmlspecialchars($user['email']) ?></div>
                                            </td>
                                            <td>
                                                <?php 
                                                    $time = strtotime($user['last_seen']);
                                                    echo date('H:i:s', $time); 
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="online-indicator"></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    

    // Cấu hình Chart.js
    const ctx = document.getElementById('registrationChart').getContext('2d');
    const dataFromServer = <?= json_encode($chartData) ?>;

    new Chart(ctx, {
        data: {
            labels: dataFromServer.labels,
            datasets: [
                {
                    type: 'line',
                    label: 'Số lượng học viên',
                    data: dataFromServer.students,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    yAxisID: 'y', // Trục bên trái
                    tension: 0.3,
                    fill: true
                },
                {
                    type: 'bar',
                    label: 'Doanh thu (VNĐ)',
                    data: dataFromServer.revenue,
                    backgroundColor: 'rgba(40, 167, 69, 0.5)', // Màu xanh lá cho tiền
                    borderColor: '#28a745',
                    borderWidth: 1,
                    yAxisID: 'y1', // Trục bên phải
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Học viên (Người)' },
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Doanh thu (VNĐ)' },
                    grid: { drawOnChartArea: false }, // Để không bị rối lưới
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + ' đ';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.parsed.y;
                            if (context.datasetIndex === 1) { // Dataset doanh thu
                                return label + ': ' + value.toLocaleString('vi-VN') + ' VNĐ';
                            }
                            return label + ': ' + value + ' học viên';
                        }
                    }
                }
            }
        }
    });
    </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>