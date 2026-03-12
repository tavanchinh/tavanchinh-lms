<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-edit" style="display: flex; gap: 20px; padding: 20px; align-items: flex-start;">
    
    <div style="flex: 1; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h3 style="border-bottom: 2px solid #007bff; padding-bottom: 10px;">Cấu hình khóa học</h3>
        
        <form action="/admin/courses/update/<?= $course['id'] ?>" method="POST" enctype="multipart/form-data">
            
            <div style="margin-bottom: 15px; text-align: center;">
                <label class="fw-bold" style="display: block; text-align: left; margin-bottom: 8px;">Ảnh đại diện</label>
                <div style="position: relative; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                    <img id="course_preview" 
                         src="/public/uploads/<?= $course['image'] ?: 'default.jpg' ?>" 
                         style="width: 100%; height: 180px; object-fit: cover; display: block;">
                    
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
                            <button type="button" class="btn btn-sm btn-link text-primary" onclick="openEditChapterModal(<?= $chapter['id'] ?>, '<?= htmlspecialchars($chapter['name']) ?>')"><i class="bi bi-pencil"></i></button>
                            <a href="/admin/chapter/delete/<?= $chapter['id'] ?>" class="btn btn-sm btn-link text-danger" onclick="return confirm('Xóa chương này?')"><i class="bi bi-trash"></i></a>
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
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick='openEditLessonModal(<?= json_encode($lesson) ?>)'><i class="bi bi-pencil"></i></button>
                                                <a href="/admin/lesson/delete/<?= $lesson['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa bài này?')"><i class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted">Chưa có bài học.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">Chưa có chương học nào.</div>
        <?php endif; ?>
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

// 2. Định dạng tiền tệ
const priceFormat = document.getElementById('price_format');
const priceRaw = document.getElementById('price_raw');
priceFormat.addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
    priceRaw.value = value;
    if (value !== "") {
        this.value = new Intl.NumberFormat('vi-VN').format(value);
    } else {
        this.value = "";
    }
});

// 3. Hàm mở Modal Sửa Chapter
function openEditChapterModal(id, name) {
    document.getElementById('edit_chapter_id').value = id;
    document.getElementById('edit_chapter_name').value = name;
    var myModal = new bootstrap.Modal(document.getElementById('editChapterModal'));
    myModal.show();
}

// 4. Hàm mở Modal Sửa Lesson
function openEditLessonModal(lesson) {
    document.getElementById('edit_l_id').value = lesson.id;
    document.getElementById('edit_l_chapter').value = lesson.chapter_id;
    document.getElementById('edit_l_name').value = lesson.name;
    document.getElementById('edit_l_video').value = lesson.link_video;
    document.getElementById('edit_l_duration').value = lesson.duration;
    document.getElementById('edit_l_position').value = lesson.position;
    document.getElementById('edit_l_preview').checked = (lesson.is_preview == 1);
    var myModal = new bootstrap.Modal(document.getElementById('editLessonModal'));
    myModal.show();
}

// 5. Submit sửa Lesson qua AJAX
function submitEditLesson() {
    const form = document.getElementById('formEditLesson');
    const formData = new FormData(form);
    const isPreview = document.getElementById('edit_l_preview').checked ? 1 : 0;
    formData.set('is_preview', isPreview);

    fetch('/admin/lesson/update-ajax', {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) { location.reload(); } else { alert('Lỗi: ' + data.message); }
    });
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>