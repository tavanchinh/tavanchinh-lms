<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-edit" style="display: flex; gap: 20px; padding: 20px; align-items: flex-start;">
    
    <div style="flex: 6; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h3 style="border-bottom: 2px solid #007bff; padding-bottom: 10px;">Cấu hình khóa học</h3>
        <form action="/admin/courses/update/<?= $course['id'] ?>" method="POST" enctype="multipart/form-data">
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0 fw-bold text-primary"><i class="bi bi-info-circle-fill me-2"></i>Thông tin cơ bản</h6>
            <span class="badge <?= $course['status'] == 1 ? 'bg-success' : 'bg-secondary' ?>">
                <?= $course['status'] == 1 ? 'Đang hiển thị' : 'Đang ẩn' ?>
            </span>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Ảnh đại diện</label>
                    <div class="position-relative rounded-3 overflow-hidden border bg-light" style="aspect-ratio: 16/9;">
                        <img id="course_preview" src="/uploads/<?= $course['image'] ?: 'default.jpg' ?>" 
                             class="w-100 h-100" style="object-fit: cover;">
                        <label for="course_image" class="btn btn-dark btn-sm position-absolute bottom-0 end-0 m-2 shadow">
                            <i class="bi bi-camera"></i> Thay đổi
                        </label>
                    </div>
                    <input type="file" name="image" id="course_image" hidden accept="image/*">
                </div>
                
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên khóa học</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($course['name']) ?>" class="form-control form-control-lg border-primary-subtle" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giá bán (VNĐ)</label>
                            <div class="input-group input-group-lg">
                                <input type="text" id="price_format" class="form-control fw-bold text-danger" value="<?= number_format($course['price'], 0, ',', '.') ?>">
                                <span class="input-group-text text-danger fw-bold">₫</span>
                            </div>
                            <input type="hidden" name="price" id="price_raw" value="<?= $course['price'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select name="status" class="form-select form-select-lg">
                                <option value="1" <?= $course['status'] == 1 ? 'selected' : '' ?>>✅ Hiển thị</option>
                                <option value="0" <?= $course['status'] == 0 ? 'selected' : '' ?>>🚫 Tạm ẩn</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Vị trí</label>
                            <input type="number" name="position" class="form-control form-control-lg" value="<?= $course['position'] ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="card-title mb-0 fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Nội dung bài giảng</h6>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <label class="form-label fw-bold">Tóm tắt ngắn (Summary)</label>
                <textarea name="summary" id="editor_summary" class="form-control"><?= $course['summary'] ?? '' ?></textarea>
            </div>
            <div class="mb-0">
                <label class="form-label fw-bold">Mô tả chi tiết (Description)</label>
                <textarea name="description" id="editor_description" class="form-control"><?= $course['description'] ?? '' ?></textarea>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="card-title mb-0 fw-bold text-primary"><i class="bi bi-paperclip me-2"></i>Tài liệu đính kèm</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 border-end">
                    <label class="form-label fw-bold">Thêm tài liệu mới</label>
                    <input type="file" name="documents[]" class="form-control mb-2" multiple>
                    <small class="text-muted italic">Bạn có thể chọn nhiều file cùng lúc (.zip, .pdf, .rar, .xlsx)</small>
                </div>
                <div class="col-md-6 ps-md-4">
                    <label class="form-label fw-bold">Danh sách tài liệu hiện có</label>
                    <div class="list-group list-group-flush border rounded">
                        <?php if (!empty($documents)): ?>
                            <?php foreach ($documents as $doc): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2" id="doc-item-<?= $doc['id'] ?>">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark-arrow-down text-primary me-2"></i>
                                        <span class="small fw-bold text-truncate" style="max-width: 250px;"><?= $doc['id'] ?> - <?= $doc['file_name'] ?></span>
                                        <span class="badge bg-light text-dark ms-2" style="font-size: 0.65rem;"><?= $doc['file_size'] ?></span>
                                    </div>
                                    <button type="button" class="btn btn-link text-danger p-0 btn-delete-doc" data-id="<?= $doc['id'] ?>" data-name="<?= $doc['file_name'] ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-3 text-center text-muted small">Chưa có tài liệu đính kèm</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sticky-bottom bg-white border-top p-3 shadow-lg d-flex justify-content-end gap-3 rounded-top">
        <a href="/admin/courses" class="btn btn-light border px-4">Quay lại</a>
        <button type="submit" class="btn btn-success px-5 fw-bold">
            <i class="bi bi-save me-2"></i> CẬP NHẬT KHÓA HỌC
        </button>
    </div>

</form>

<style>
    /* Laptop 14 inch tối ưu CKEditor */
    .ck-editor__editable { 
        min-height: 300px !important; 
        max-height: 600px;
    }
    /* Đảm bảo sticky bottom không che khuất nội dung cuối cùng */
    body { padding-bottom: 80px; }
    .sticky-bottom { 
        position: fixed; 
        bottom: 0; 
        left: 260px; /* Thay đổi theo chiều rộng sidebar admin của bạn */
        right: 0; 
        z-index: 1000; 
    }
</style>

        <hr>
        <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#addChapterModal">
            <i class="bi bi-folder-plus"></i> + Tạo chương mới
        </button>
    </div>

    <div style="flex: 4; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Bài học</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLessonModal">
                <i class="bi bi-plus-circle"></i> Thêm
            </button>
        </div>

        <?php if (!empty($chapters)): ?>
            <?php foreach ($chapters as $chapter): ?>
                <div class="chapter-item mb-4" style="border: 1px solid #eee; border-radius: 8px;">
                    <div style="background: #f8f9fa; padding: 10px 15px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; border-radius: 8px 8px 0 0;">
                        <span style="font-size: 14px;">📂 <?= $chapter['name'] ?></span>
                        <div>
                            <button type="button" class="btn btn-sm btn-link text-primary p-0" onclick="openEditChapterModal(<?= $chapter['id'] ?>, '<?= htmlspecialchars($chapter['name']) ?>')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="/admin/chapter/delete/<?= $chapter['id'] ?>" class="btn btn-sm btn-link text-danger p-0" onclick="return confirm('Xóa chương này?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="lesson-table p-1">
                        <table class="table table-hover mb-0" style="font-size: 13px;">
                            <tbody>
                                <?php if (!empty($chapter['lessons'])): ?>
                                    <?php foreach ($chapter['lessons'] as $lesson): ?>
                                        <tr>
                                            <td style="width: 20px; color: #999;"><?= $lesson['position'] ?></td>
                                            <td>
                                                <strong><?= $lesson['name'] ?></strong>
                                                <div>
                                                    <i class="bi bi-link-45deg"></i>
                                                    <?php if (!empty($lesson['link_video'])): ?>
                                                        <a href="https://drive.google.com/file/d/<?= $lesson['link_video'] ?>/view" 
                                                        target="_blank" 
                                                        class="text-decoration-none text-primary fw-medium" 
                                                        title="Nhấp để kiểm tra trên Drive">
                                                            <?= substr($lesson['link_video'], 0, 10) . '...' ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <small class="text-danger italic">Chưa có link</small>
                                                    <?php endif; ?>
                                                    
                                                    <span class="mx-2">|</span>

                                                    <?php if (isset($lesson['is_preview']) && $lesson['is_preview'] == 1): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Học thử</span>                 
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-muted border rounded-pill">Không</span>    
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm text-primary p-0" onclick='openEditLessonModal(<?= json_encode($lesson) ?>)'>
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <a href="/admin/lesson/delete/<?= $lesson['id'] ?>" class="btn btn-sm text-danger p-0" onclick="return confirm('Xóa bài học này?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td class="text-center text-muted small">Trống</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
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
                <label class="form-label fw-bold">Link Video</label>
                <input type="text" name="link_video" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-6 mb-3"><label class="form-label">Thời lượng</label><input type="number" name="duration" class="form-control" value="0"></div>
                <div class="col-6 mb-3"><label class="form-label">Vị trí</label><input type="number" name="position" class="form-control" value="1"></div>
            </div>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_preview" id="isPreview"><label class="form-check-label" for="isPreview">Học thử</label></div>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-primary w-100">Lưu bài học</button></div>
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
                <label class="form-label fw-bold">Link Video</label>
                <input type="text" name="link_video" id="edit_l_video" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-6 mb-3"><label class="form-label">Thời lượng</label><input type="number" name="duration" id="edit_l_duration" class="form-control"></div>
                <div class="col-6 mb-3"><label class="form-label">Vị trí</label><input type="number" name="position" id="edit_l_position" class="form-control"></div>
            </div>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_preview" id="edit_l_preview"><label class="form-check-label" for="edit_l_preview">Học thử</label></div>
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
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Vị trí</label>
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

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/super-build/ckeditor.js"></script>


<script>

    CKEDITOR.ClassicEditor.create(document.querySelector('#editor_summary'), {
        // 1. Toolbar có nút Source
        toolbar: {
            items: [
                'heading', '|',
                'bold', 'italic', 'link',
                'bulletedList', 'numberedList', 'insertTable', 'blockQuote',
                'undo', 'redo'
            ]
        },
        // 2. CHẶN TRIỆT ĐỂ: Phải xóa cả cụm liên quan đến nhau
        removePlugins: [
            // Nhóm Slash Command & Mention (Gây lỗi anh vừa gặp)
            'SlashCommand', 'Mention','Emoji',
            
            // Nhóm Collaboration (Cộng tác)
            'RealTimeCollaborativeComments',
            'RealTimeCollaborativeTrackChanges',
            'RealTimeCollaborativeRevisionHistory',
            'PresenceList',
            'Comments',
            'TrackChanges',
            'TrackChangesData',
            'RevisionHistory',

            // Nhóm Office & PDF (Đòi License)
            'PasteFromOfficeEnhanced',
            'ExportPdf',
            'ExportWord',
            'Pagination',
            
            // Nhóm File Management
            'CKBox',
            'CKFinder',
            'EasyImage',
            
            // Nhóm AI & Trợ lý
            'WProofreader',
            'MathType',
            'FormatPainter',
            'TableOfContents',
            'DocumentOutline',
            'Template',
            'CaseChange',
            'MultiLevelList'
        ],
        // 3. Cho phép HTML tự do
        htmlSupport: {
            allow: [
                {
                    name: /.*/,
                    attributes: true,
                    classes: true,
                    styles: true
                }
            ]
        }
    }).then(editor => {
        editor.editing.view.change(writer => {
            writer.setStyle('height', '200px', editor.editing.view.document.getRoot());
        });
    }).catch(error => {
        console.error('Lỗi khởi tạo CKEditor:', error);
    });


    CKEDITOR.ClassicEditor.create(document.querySelector('#editor_description'), {
        // 1. Toolbar có nút Source
        toolbar: {
            items: [
                'sourceEditing', '|',
                'heading', '|',
                'bold', 'italic', 'link', 'imageUpload', 'mediaEmbed',
                'bulletedList', 'numberedList', 'insertTable', 'blockQuote',
                'undo', 'redo'
            ]
        },
        // 2. CHẶN TRIỆT ĐỂ: Phải xóa cả cụm liên quan đến nhau
        removePlugins: [
            // Nhóm Slash Command & Mention (Gây lỗi anh vừa gặp)
            'SlashCommand', 'Mention','Emoji',
            
            // Nhóm Collaboration (Cộng tác)
            'RealTimeCollaborativeComments',
            'RealTimeCollaborativeTrackChanges',
            'RealTimeCollaborativeRevisionHistory',
            'PresenceList',
            'Comments',
            'TrackChanges',
            'TrackChangesData',
            'RevisionHistory',

            // Nhóm Office & PDF (Đòi License)
            'PasteFromOfficeEnhanced',
            'ExportPdf',
            'ExportWord',
            'Pagination',
            
            // Nhóm File Management
            'CKBox',
            'CKFinder',
            'EasyImage',
            
            // Nhóm AI & Trợ lý
            'WProofreader',
            'MathType',
            'FormatPainter',
            'TableOfContents',
            'DocumentOutline',
            'Template',
            'CaseChange',
            'MultiLevelList'
        ],
        // 3. Cho phép HTML tự do
        htmlSupport: {
            allow: [
                {
                    name: /.*/,
                    attributes: true,
                    classes: true,
                    styles: true
                }
            ]
        }
    }).catch(error => {
        console.error('Lỗi khởi tạo CKEditor:', error);
    });

    

    // Preview Image
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

    // Price Format
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

    // Chapter AJAX
    function openEditChapterModal(id, name) {
        document.getElementById('edit_chapter_id').value = id;
        document.getElementById('edit_chapter_name').value = name;
        new bootstrap.Modal(document.getElementById('editChapterModal')).show();
    }

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
            if (data.success) location.reload();
            else alert('Lỗi: ' + data.message);
        });
    }

    // Lesson AJAX
    function openEditLessonModal(lesson) {
        document.getElementById('edit_l_id').value = lesson.id;
        document.getElementById('edit_l_chapter').value = lesson.chapter_id;
        document.getElementById('edit_l_name').value = lesson.name;
        document.getElementById('edit_l_video').value = lesson.link_video;
        document.getElementById('edit_l_duration').value = lesson.duration;
        document.getElementById('edit_l_position').value = lesson.position;
        document.getElementById('edit_l_preview').checked = (lesson.is_preview == 1);
        new bootstrap.Modal(document.getElementById('editLessonModal')).show();
    }

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
            if (data.success) location.reload();
            else alert('Lỗi: ' + data.message);
        });
    }
    document.querySelectorAll('.btn-delete-doc').forEach(button => {
        button.addEventListener('click', function() {
            const docId = this.getAttribute('data-id');
            const fileName = this.getAttribute('data-name');

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có chắc chắn muốn xóa tài liệu: ${fileName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6e7881',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Gửi yêu cầu xóa qua AJAX (Fetch API)
                    fetch(`/admin/courses/delete-doc/${docId}`, {
                        method: 'POST', // Hoặc GET tùy theo route của bạn
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Đã xóa!', 'Tài liệu đã được gỡ bỏ thành công.', 'success');
                            // Xóa dòng tài liệu khỏi giao diện mà không load lại trang
                            const item = document.getElementById(`doc-item-${docId}`);
                            if (item) item.remove();
                        } else {
                            Swal.fire('Lỗi!', data.message || 'Không thể xóa tài liệu này.', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Lỗi kết nối!', 'Không thể gửi yêu cầu đến máy chủ.', 'error');
                    });
                }
            });
        });
    });
</script>

<style>
    .ck-editor__editable { min-height: 150px; }
    #editor_summary + .ck-editor .ck-editor__editable { min-height: 100px; }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>