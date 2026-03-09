<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="card shadow-sm mt-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between">
        <h5 class="mb-0">Tiến độ học tập của học viên</h5>
    </div>
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Học viên</th>
                    <th>Khóa học</th>
                    <th width="30%">Tiến độ</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($progress as $row): ?>
                <tr>
                    <td>
                        <strong><?= $row['fullname'] ?></strong><br>
                        <small class="text-muted"><?= $row['email'] ?></small>
                    </td>
                    <td><?= $row['course_name'] ?></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1" style="height: 12px;">
                                <div class="progress-bar bg-success" style="width: <?= $row['percent'] ?>%"></div>
                            </div>
                            <span class="ms-2 fw-bold small"><?= $row['percent'] ?>%</span>
                        </div>
                        <small class="text-muted"><?= $row['completed_lessons'] ?>/<?= $row['total_lessons'] ?> bài đã xong</small>
                    </td>
                    <td class="text-end">
                        <form action="/admin/fast-complete" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn mở khóa toàn bộ bài học cho học viên này?')">
                            <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                            <input type="hidden" name="course_id" value="<?= $row['course_id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-lightning-charge"></i> Hoàn thành tất cả
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>