<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Quản lý tiến độ học tập</h3>
        <a href="/admin/study" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>
    <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
            <form action="" method="GET" class="row g-2">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" 
                            placeholder="Tìm kiếm theo tên, email hoặc số điện thoại học viên..." 
                            value="<?= htmlspecialchars($search ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3">
                        <i class="bi bi-filter me-1"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>
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
                            <strong><?= $row['name'] ?></strong><br>
                            <small class="text-muted"><?= $row['email'] ?></small>
                        </td>
                        <td><?= $row['course_name'] ?></td>
                        <td>
                            <div class="d-flex align-items-center" id="progress-container-<?= $row['user_id'] ?>-<?= $row['course_id'] ?>">
                                <div class="progress flex-grow-1" style="height: 12px;">
                                    <div class="progress-bar bg-success js-progress-bar" style="width: <?= $row['percent'] ?>%"></div>
                                </div>
                                <span class="ms-2 fw-bold small js-progress-text"><?= $row['percent'] ?>%</span>
                            </div>
                            <small class="text-muted"><?= $row['completed_lessons'] ?>/<?= $row['total_lessons'] ?> bài đã xong</small>
                        </td>
                        <td class="text-end">
                            <button type="button" 
                                    class="btn btn-sm btn-outline-danger rounded-pill px-3 btn-fast-complete" 
                                    data-user-id="<?= $row['user_id'] ?>" 
                                    data-course-id="<?= $row['course_id'] ?>"
                                    data-user-name="<?= htmlspecialchars($row['name']) ?>">
                                <i class="bi bi-lightning-charge"></i> Hoàn thành nhanh
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($totalPages > 1): ?>
                <div class="d-flex justify-content-between align-items-center mt-4 p-3">
                    <div class="text-muted small">
                        Hiển thị trang <?= $currentPage ?> / <?= $totalPages ?> (Tổng <?= $totalRecords ?> trang)
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=1">Đầu</a>
                            </li>

                            <?php 
                            $delta = 2;
                            for ($i = 1; $i <= $totalPages; $i++): 
                                if ($i == 1 || $i == $totalPages || ($i >= $currentPage - $delta && $i <= $currentPage + $delta)):
                            ?>
                                <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php elseif ($i == $currentPage - $delta - 1 || $i == $currentPage + $delta + 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; endfor; ?>

                            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $totalPages ?>">Cuối</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>


            
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.btn-fast-complete').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const courseId = this.getAttribute('data-course-id');
            const userName = this.getAttribute('data-user-name');

            Swal.fire({
                title: 'Xác nhận mở khóa?',
                text: `Bạn có chắc chắn muốn hoàn thành toàn bộ bài học cho học viên ${userName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Đồng ý, mở hết!',
                cancelButtonText: 'Hủy bỏ',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Hiển thị hiệu ứng loading
                    Swal.fire({
                        title: 'Đang xử lý...',
                        didOpen: () => { Swal.showLoading() },
                        allowOutsideClick: false
                    });

                    // Gửi AJAX
                    fetch('/admin/study/fast-complete', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `user_id=${userId}&course_id=${courseId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Thành công!',
                                `Đã mở khóa toàn bộ bài học cho ${userName}.`,
                                'success'
                            );
                            // Tìm vùng chứa tiến độ của học viên vừa nhấn
                            const container = document.getElementById(`progress-container-${userId}-${courseId}`);
                            
                            if (container) {
                                const progressBar = container.querySelector('.js-progress-bar');
                                const progressText = container.querySelector('.js-progress-text');

                                // Hiệu ứng mượt mà
                                progressBar.style.transition = "width 1s ease-in-out";
                                progressBar.style.width = '100%';
                                progressText.innerText = '100%';
                                
                                // Có thể đổi màu nút bấm hoặc ẩn đi vì đã xong
                                btn.classList.remove('btn-outline-danger');
                                btn.classList.add('btn-success');
                                btn.innerHTML = '<i class="bi bi-check-lg"></i> Đã hoàn thành';
                                btn.disabled = true;
                            }

                        } else {
                            Swal.fire('Lỗi!', 'Không thể xử lý yêu cầu.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>