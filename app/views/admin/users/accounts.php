<?php include __DIR__ . '/../layouts/header.php'; ?>
<style>
    .course-selector {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #eee;
        padding: 10px;
        border-radius: 8px;
        background: #fff;
    }
    .course-card-item {
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid transparent;
        background: #f8f9fa;
        margin-bottom: 8px;
        padding: 10px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .course-card-item:hover { background: #eef2f7; }
    .course-card-item.active {
        border-color: #0d6efd;
        background: #e7f1ff;
    }
    .course-card-item .check-icon {
        display: none;
        color: #0d6efd;
        font-size: 1.2rem;
    }
    .course-card-item.active .check-icon { display: block; }
</style>

<?php
    $delta = 2; // Số lượng trang hiển thị xung quanh trang hiện tại
    $range = [];
    for ($i = max(2, $currentPage - $delta); $i <= min($totalPages - 1, $currentPage + $delta); $i++) {
        $range[] = $i;
    }

    if ($currentPage - $delta > 2) {
        array_unshift($range, "...");
    }
    if ($currentPage + $delta < $totalPages - 1) {
        $range[] = "...";
    }

    array_unshift($range, 1); // Luôn hiện trang 1
    if ($totalPages > 1) {
        $range[] = $totalPages; // Luôn hiện trang cuối
    }
?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-people-fill me-2"></i>Quản lý tài khoản</h3>
        <button class="btn btn-primary px-3" data-bs-toggle="modal" data-bs-target="#createStudentModal">
            <i class="bi bi-plus-lg"></i> Tạo tài khoản
        </button>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <input type="hidden" name="tab" value="<?= $currentTab ?>">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Tìm theo tên, email hoặc số điện thoại..." 
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">Lọc kết quả</button>
                </div>
            </form>
        </div>
    </div>

    <ul class="nav nav-pills mb-3 p-2 bg-white rounded shadow-sm">
        <li class="nav-item">
            <a class="nav-link <?= $currentTab == 'admin' ? 'active' : '' ?>" 
               href="?tab=admin&search=<?= $search ?>"> Quản trị viên</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $currentTab == 'staff' ? 'active' : '' ?>" 
               href="?tab=staff&search=<?= $search ?>"> Nhân viên</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $currentTab == 'student' ? 'active' : '' ?>" 
               href="?tab=student&search=<?= $search ?>"> Học viên</a>
        </li>
    </ul>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Thông tin</th>
                        <th>Email & SĐT</th>
                        <th>Ngày đăng ký</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): foreach ($users as $user): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-3 bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <?= mb_strtoupper(mb_substr($user['name'], 0, 1, 'UTF-8'), 'UTF-8') ?>
                                </div>
                                <div>
                                    <div class="fw-bold"><?= htmlspecialchars($user['name']) ?></div>
                                    <span class="badge bg-<?= $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'staff' ? 'info' : 'success') ?>-subtle text-dark small">
                                        <?= strtoupper($user['role']) ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small text-muted"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($user['email']) ?></div>
                            <div class="small text-muted"><i class="bi bi-phone me-1"></i><?= htmlspecialchars($user['phone_number']) ?></div>
                        </td>
                        <td><?= date('d/m/Y', strtotime($user['registered_at'])) ?></td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick='openEditStudentModal(<?= json_encode($user) ?>)'>
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button onclick="confirmDelete(<?= $user['id'] ?>)" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">Không tìm thấy tài khoản nào trong nhóm này.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-4 p-3">
        <div class="text-muted small">
            Hiển thị trang <?= $currentPage ?> / <?= $totalPages ?> (Tổng <?= $totalUsers ?> học viên)
        </div>
        
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?tab=<?= $currentTab ?>&search=<?= $search ?>&page=1">Đầu</a>
                </li>

                <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?tab=<?= $currentTab ?>&search=<?= $search ?>&page=<?= $currentPage - 1 ?>">&laquo;</a>
                </li>

                <?php foreach ($range as $p): ?>
                    <?php if ($p === "..."): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php else: ?>
                        <li class="page-item <?= ($p == $currentPage) ? 'active' : '' ?>">
                            <a class="page-link" href="?tab=<?= $currentTab ?>&search=<?= $search ?>&page=<?= $p ?>"><?= $p ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>

                <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?tab=<?= $currentTab ?>&search=<?= $search ?>&page=<?= $currentPage + 1 ?>">&raquo;</a>
                </li>

                <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?tab=<?= $currentTab ?>&search=<?= $search ?>&page=<?= $totalPages ?>">Cuối</a>
                </li>
            </ul>
        </nav>
    </div>
    </div>
</div>
<div class="modal fade" id="createStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: #334155;">Tạo tài khoản</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/admin/students/store" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control py-2" placeholder="Nhập họ tên" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control py-2" placeholder="Nhập email học viên" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Số điện thoại</label>
                        <input type="text" name="phone_number" class="form-control py-2" placeholder="Nhập số điện thoại">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control py-2" placeholder="Nhập mật khẩu" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Gán khóa học (Click để chọn nhiều)</label>
                        <div class="course-selector">
                            <?php foreach ($courses as $course): ?>
                                <div class="course-card-item" onclick="toggleCourse(this, <?= $course['id'] ?>)">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold small"><?= $course['name'] ?></div>
                                        <div class="text-muted small"><?= number_format($course['price']) ?> VNĐ</div>
                                    </div>
                                    <i class="bi bi-check-circle-fill check-icon"></i>
                                    <input type="checkbox" name="course_ids[]" value="<?= $course['id'] ?>" class="d-none">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-text small">Mẹo: Click vào thẻ để chọn hoặc bỏ chọn khóa học.</div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 8px;">
                            Tạo mới
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: #334155;">Chỉnh sửa tài khoản</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditStudent">
                    <input type="hidden" name="id" id="edit_s_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Họ và tên</label>
                                <input type="text" name="name" id="edit_s_name" class="form-control py-2" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Email</label>
                                <input type="email" name="email" id="edit_s_email" class="form-control py-2" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Số điện thoại</label>
                                <input type="text" name="phone_number" id="edit_s_phone" class="form-control py-2">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-danger">Mật khẩu mới (Để trống nếu không đổi)</label>
                                <input type="password" name="password" class="form-control py-2">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Khóa học tham gia (Click để chọn)</label>
                            <div class="course-selector" id="edit_course_list">
                                <?php foreach ($courses as $course): ?>
                                    <div class="course-card-item" data-id="<?= $course['id'] ?>" onclick="toggleCourse(this, <?= $course['id'] ?>)">
                                        <div class="flex-grow-1">
                                            <div class="fw-bold" style="font-size: 0.8rem;"><?= $course['name'] ?></div>
                                        </div>
                                        <i class="bi bi-check-circle-fill check-icon"></i>
                                        <input type="checkbox" name="course_ids[]" value="<?= $course['id'] ?>" class="d-none">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-primary px-4 fw-bold" onclick="submitEditStudent()">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    function toggleCourse(element, courseId) {
        // Tìm checkbox ẩn bên trong phần tử vừa click
        const checkbox = element.querySelector('input[type="checkbox"]');
        
        // Đảo ngược trạng thái checkbox
        checkbox.checked = !checkbox.checked;
        
        // Thêm hoặc xóa class 'active' để đổi màu giao diện
        if (checkbox.checked) {
            element.classList.add('active');
        } else {
            element.classList.remove('active');
        }
    }

    // 1. Hàm mở Modal và nạp dữ liệu
    function openEditStudentModal(user) {
        document.getElementById('edit_s_id').value = user.id;
        document.getElementById('edit_s_name').value = user.name;
        document.getElementById('edit_s_email').value = user.email;
        document.getElementById('edit_s_phone').value = user.phone_number;

        // Reset tất cả thẻ khóa học về trạng thái chưa chọn
        const cards = document.querySelectorAll('#edit_course_list .course-card-item');
        cards.forEach(card => {
            card.classList.remove('active');
            card.querySelector('input[type="checkbox"]').checked = false;
        });

        // Gọi AJAX để lấy danh sách khóa học mà học viên ĐÃ THAM GIA
        fetch(`/admin/students/get-courses/${user.id}`)
            .then(response => response.json())
            .then(enrolledIds => {
                enrolledIds.forEach(courseId => {
                    const card = document.querySelector(`#edit_course_list .course-card-item[data-id="${courseId}"]`);
                    if (card) {
                        card.classList.add('active');
                        card.querySelector('input[type="checkbox"]').checked = true;
                    }
                });
            });

        const modal = new bootstrap.Modal(document.getElementById('editStudentModal'));
        modal.show();
    }

    // 2. Hàm gửi dữ liệu cập nhật
    function submitEditStudent() {
        const form = document.getElementById('formEditStudent');
        const formData = new FormData(form);

        fetch('/admin/students/update-ajax', {
            method: 'POST',
            body: new URLSearchParams(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Lỗi: ' + data.message);
            }
        });
    }


    window.confirmDelete = function(userId) {
        Swal.fire({
            title: 'Xác nhận xóa?',
            text: "Dữ liệu người dùng và tiến độ học tập sẽ bị xóa vĩnh viễn!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Vâng, xóa ngay!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                // Chuyển hướng đến route xóa của bạn
                // Ví dụ: /admin/users/delete/5
                window.location.href = '/admin/users/delete/' + userId;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['error_msg'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: '<?= $_SESSION['error_msg'] ?>',
                confirmButtonText: 'Đóng'
            });
            <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_msg'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: '<?= $_SESSION['success_msg'] ?>',
                timer: 3000, // Tự đóng sau 3 giây
                showConfirmButton: false
            });
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>
    });
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>