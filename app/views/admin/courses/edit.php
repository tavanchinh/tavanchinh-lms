<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-edit" style="display: flex; gap: 20px; padding: 20px; align-items: flex-start;">
    
    <div style="flex: 1; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h3 style="border-bottom: 2px solid #007bff; padding-bottom: 10px;">Cấu hình khóa học</h3>
            <form action="/admin/courses/update/<?= $course['id'] ?>" method="POST" enctype="multipart/form-data">
            
            <div style="margin-bottom: 15px; text-align: center;">
                <label class="fw-bold" style="display: block; text-align: left; margin-bottom: 8px;">Ảnh đại diện</label>
                <div style="position: relative; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                    <img id="course_preview" 
                         src="/uploads/<?= $course['image'] ?: 'default.jpg' ?>" 
                         style="width: 100%; height: auto; object-fit: cover; display: block;">
                    
                    <label for="course_image" style="position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.7); color: #fff; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                        <i class="bi bi-camera"></i> Thay đổi
                    </label>
                </div>
                <input type="file" name="image" id="course_image" hidden accept="image/*">
            </div>
            <div style="margin-bottom: 15px;">
                <label class="fw-bold">Tên khóa học</label>
                <input type="text" name="name" value="<?= $course['name'] ?>" class="form-control">
            </div>
            <div style="margin-bottom: 15px;">
                <label class="fw-bold">Giá bán (VNĐ)</label>
                <input type="text" 
                    id="price_format" 
                    class="form-control" 
                    value="<?= number_format($course['price'], 0, ',', '.') ?>" 
                    placeholder="Ví dụ: 5.000.000">
                
                <input type="hidden" name="price" id="price_raw" value="<?= $course['price'] ?>">
            </div>
            <button type="submit" class="btn btn-success w-100">Cập nhật chung</button>
        </form>

        <hr>
        <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#addChapterModal">
            <i class="bi bi-folder-plus"></i> + Tạo chương mới
        </button>
    </div>

    <div style="flex: 2; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Nội dung bài học</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLessonModal">
                <i class="bi bi-plus-circle"></i> + Thêm bài mới
            </button>
        </div>

        <?php if (!empty($chapters)): ?>
            <?php foreach ($chapters as $chapter): ?>
                <div class="chapter-item mb-4" style="border: 1px solid #eee; border-radius: 8px;">
                    <div style="background: #f8f9fa; padding: 10px 15px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; border-radius: 8px 8px 0 0;">
                        <span>📂 <?= $chapter['name'] ?></span>
                        <div>
                            <button type="button" 
                                    class="btn btn-sm btn-link text-primary" 
                                    onclick="openEditChapterModal(<?= $chapter['id'] ?>, '<?= htmlspecialchars($chapter['name']) ?>')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="/admin/chapter/delete/<?= $chapter['id'] ?>" 
                            class="btn btn-sm btn-link text-danger" 
                            onclick="return confirm('Xóa chương này sẽ mất hết bài học bên trong. Bạn chắc chưa?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="lesson-table p-2">
                        <table class="table table-hover mb-0" style="font-size: 14px;">
                            <tbody>
                                <?php if (!empty($chapter['lessons'])): ?>
                                    <?php foreach ($chapter['lessons'] as $lesson): ?>
                                        <tr>
                                            <td style="width: 40px; color: #999;"><?= $lesson['position'] ?></td>
                                            <td>
                                                <strong><?= $lesson['name'] ?></strong><br>
                                                <small class="text-muted"><?= $lesson['duration'] ?> phút | ID: <?= $lesson['link_video'] ?></small>
                                            </td>
                                            <td class="text-center">
                                                <?= $lesson['is_preview'] ? '<span class="badge bg-success">Học thử</span>' : '' ?>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary" 
                                                        onclick='openEditLessonModal(<?= json_encode($lesson) ?>)'>
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="/admin/lesson/delete/<?= $lesson['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa bài này?')"><i class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted">Chưa có bài học trong chương này.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">Khóa học này chưa có chương nào. Hãy tạo chương trước khi thêm bài học.</div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="addLessonModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="/admin/lesson/store" method="POST">
        <div class="modal-header">
            <h5 class="modal-title">Thêm bài học mới</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">

            <div class="mb-3">
                <label class="form-label fw-bold">Chọn chương</label>
                <select name="chapter_id" class="form-select" required>
                    <option value="">-- Chọn chương học --</option>
                    <?php foreach ($chapters as $chap): ?>
                        <option value="<?= $chap['id'] ?>"><?= $chap['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Tên bài học</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Google Drive ID</label>
                <input type="text" name="link_video" class="form-control" required>
            </div>

            <div class="row">
                <div class="col-6 mb-3"><label class="form-label">Thời lượng (phút)</label><input type="number" name="duration" class="form-control" value="0"></div>
                <div class="col-6 mb-3"><label class="form-label">Vị trí</label><input type="number" name="position" class="form-control" value="1"></div>
            </div>

            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_preview" id="isPreview"><label class="form-check-label" for="isPreview">Cho phép học thử</label></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary w-100">Lưu bài học</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editLessonModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formEditLesson">
        <div class="modal-header">
            <h5 class="modal-title">Chỉnh sửa bài học</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="edit_l_id">
            
            <div class="mb-3">
                <label class="form-label fw-bold">Chương</label>
                <select name="chapter_id" id="edit_l_chapter" class="form-select" required>
                    <?php foreach ($chapters as $chap): ?>
                        <option value="<?= $chap['id'] ?>"><?= $chap['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Tên bài học</label>
                <input type="text" name="name" id="edit_l_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Google Drive ID</label>
                <input type="text" name="link_video" id="edit_l_video" class="form-control" required>
            </div>

            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">Thời lượng (phút)</label>
                    <input type="number" name="duration" id="edit_l_duration" class="form-control">
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Vị trí</label>
                    <input type="number" name="position" id="edit_l_position" class="form-control">
                </div>
            </div>

            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_preview" id="edit_l_preview">
                <label class="form-check-label" for="edit_l_preview">Cho phép học thử</label>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="button" class="btn btn-primary" onclick="submitEditLesson()">Cập nhật</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="addChapterModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <form action="/admin/chapter/store" method="POST">
        <div class="modal-header"><h5>Tạo chương mới</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
            <div class="mb-3">
                <label class="form-label">Tên chương</label>
                <input type="text" name="name" class="form-control" required placeholder="Ví dụ: Chương 1: Căn bản">
            </div>
            <div class="mb-3">
                <label class="form-label">Vị trí hiển thị</label>
                <input type="number" name="position" class="form-control" value="<?= count($chapters) + 1 ?>">
            </div>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-primary w-100">Lưu chương</button></div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editChapterModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Chỉnh sửa tên chương</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="edit_chapter_id">
            <div class="mb-3">
                <label class="form-label fw-bold">Tên chương mới</label>
                <input type="text" id="edit_chapter_name" class="form-control" required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="button" class="btn btn-primary" onclick="submitEditChapter()">Lưu thay đổi</button>
        </div>
    </div>
  </div>
</div>


<script>

    // 1. Xem trước ảnh ngay khi chọn file
    document.getElementById('course_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('course_preview').setAttribute('src', event.target.result);
            }
            reader.readAsDataURL(file);
        }
    });
    const priceFormat = document.getElementById('price_format');
    const priceRaw = document.getElementById('price_raw');

    priceFormat.addEventListener('input', function(e) {
        // 1. Lấy giá trị chỉ bao gồm số
        let value = this.value.replace(/\D/g, '');
        
        // 2. Cập nhật vào input ẩn để gửi lên server (dạng số thuần 5000000)
        priceRaw.value = value;
        
        // 3. Định dạng lại hiển thị có dấu chấm (dạng 5.000.000)
        if (value !== "") {
            this.value = new Intl.NumberFormat('vi-VN').format(value);
        } else {
            this.value = "";
        }
    });


    // Hàm mở modal và gán dữ liệu
    function openEditChapterModal(id, name) {
        document.getElementById('edit_chapter_id').value = id;
        document.getElementById('edit_chapter_name').value = name;
        const modal = new bootstrap.Modal(document.getElementById('editChapterModal'));
        modal.show();
    }

    // Hàm gửi AJAX để cập nhật tên chương
    function submitEditChapter() {
        const id = document.getElementById('edit_chapter_id').value;
        const name = document.getElementById('edit_chapter_name').value;

        fetch('/admin/chapter/update-ajax', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&name=${encodeURIComponent(name)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Sửa xong reload lại để thấy thay đổi
            } else {
                alert('Lỗi: ' + data.message);
            }
        });
    }


    // Hàm mở modal sửa bài học và đổ dữ liệu
    function openEditLessonModal(lesson) {
        document.getElementById('edit_l_id').value = lesson.id;
        document.getElementById('edit_l_chapter').value = lesson.chapter_id;
        document.getElementById('edit_l_name').value = lesson.name;
        document.getElementById('edit_l_video').value = lesson.link_video;
        document.getElementById('edit_l_duration').value = lesson.duration;
        document.getElementById('edit_l_position').value = lesson.position;
        document.getElementById('edit_l_preview').checked = (lesson.is_preview == 1);

        const modal = new bootstrap.Modal(document.getElementById('editLessonModal'));
        modal.show();
    }

    // Gửi AJAX cập nhật bài học
    function submitEditLesson() {
        const form = document.getElementById('formEditLesson');
        const formData = new FormData(form);
        
        // Xử lý giá trị checkbox vì FormData không tự lấy nếu không checked
        const isPreview = document.getElementById('edit_l_preview').checked ? 1 : 0;
        formData.set('is_preview', isPreview);

        fetch('/admin/lesson/update-ajax', {
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