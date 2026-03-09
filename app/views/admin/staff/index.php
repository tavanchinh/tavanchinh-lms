<?php include __DIR__ . '/../layouts/header.php'; ?>
<style>
    .course-selector {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #eee;
        padding: 10px;
        border-radius: 8px;
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
    .course-card-item:hover {
        background: #eef2f7;
    }
    /* Khi được chọn (Active) */
    .course-card-item.active {
        border-color: #0d6efd;
        background: #e7f1ff;
    }
    .course-card-item .check-icon {
        display: none;
        color: #0d6efd;
        font-size: 1.2rem;
    }
    .course-card-item.active .check-icon {
        display: block;
    }
    .course-card-item img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
    }
</style>
<div class="container-fluid py-4" style="background-color: #f0f2f5; min-height: 100vh;">
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <div class="list-group border-0 shadow-sm mb-4">
                    <a href="#" class="list-group-item list-group-item-action active border-0 py-3">
                        <i class="bi bi-person-badge me-2"></i> Tài khoản
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 py-3">
                        <i class="bi bi-people me-2"></i> Nhóm
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 py-3">
                        <i class="bi bi-book me-2"></i> Học tập
                    </a>
                </div>
            </div>

            <div class="col-md-10">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 12px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0 text-uppercase" style="color: #003366;">Danh sách tài khoản</h5>
                        
                        <div class="d-flex gap-2">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" class="form-control border-end-0" placeholder="Tìm kiếm theo họ tên, email...">
                                <span class="input-group-text bg-white border-start-0"><i class="bi bi-search text-muted"></i></span>
                            </div>
                            <button class="btn btn-outline-secondary"><i class="bi bi-filter"></i> Bộ lọc</button>
                            <button class="btn btn-outline-secondary"><i class="bi bi-download"></i> Export</button>
                            <button class="btn btn-primary px-3" data-bs-toggle="modal" data-bs-target="#createStudentModal">
                                <i class="bi bi-plus-lg"></i> Tài khoản
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="text-muted" style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="border-0 ps-3">Avatar</th>
                                    <th class="border-0">Họ tên</th>
                                    <th class="border-0">Liên hệ</th>
                                    <th class="border-0 text-center">Ngày tạo</th>
                                    <th class="border-0 text-center">Trạng thái</th>
                                    <th class="border-0 text-end pe-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr class="border-bottom" style="height: 80px;">
                                    <td class="ps-3">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-secondary" style="width: 45px; height: 45px; font-size: 0.8rem; border: 1px solid #dee2e6;">
                                            <?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold" style="color: #334155;"><?= $user['name'] ?></span>
                                    </td>
                                    <td>
                                        <div class="small text-muted"><?= $user['phone_number'] ?? 'Chưa cập nhật' ?></div>
                                        <div class="small text-primary d-flex align-items-center">
                                            <?= $user['email'] ?> <i class="bi bi-check-circle-fill ms-1 text-success" style="font-size: 0.7rem;"></i>
                                        </div>
                                    </td>
                                    <td class="text-center text-muted small">
                                        <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">Hoạt động</span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" 
                                                class="btn btn-sm text-primary" 
                                                onclick='openEditStudentModal(<?= json_encode($user) ?>)'>
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>