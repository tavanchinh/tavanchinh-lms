<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="text-primary mb-0"><i class="bi bi-plus-circle"></i> Thêm Khóa Học Mới</h3>
                    <a href="/dashboard" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>

                <form action="/admin/courses/store" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tên khóa học <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Nhập tên khóa học..." required>
                            </div>
                            
                            <div class="mb-3" style="margin-bottom: 15px; text-align: center;">
                                <label class="fw-bold" style="display: block; text-align: left; margin-bottom: 8px;">Ảnh đại diện</label>
                                <div style="position: relative; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                                    <img id="course_preview" 
                                        src="/uploads/<?= $course['image'] ?: 'default.jpg' ?>" 
                                        style="width: 100%; height: auto; min-height: 150px; object-fit: cover; display: block;">
                                    
                                    <label for="course_image" style="position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.7); color: #fff; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                        <i class="bi bi-camera"></i> Thay đổi
                                    </label>
                                </div>
                                <input type="file" name="image" id="course_image" hidden accept="image/*">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Mô tả ngắn (Summary)</label>
                                <textarea name="summary" class="form-control" rows="3" placeholder="Tóm tắt nội dung để hiển thị ở danh sách..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nội dung chi tiết (Description)</label>
                                <textarea name="description" class="form-control" rows="8" placeholder="Mô tả chi tiết nội dung học tập..."></textarea>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="card bg-light border-0 p-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        <?php foreach($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Giá bán</label>
                                        <div class="input-group">
                                            <input type="number" name="price" class="form-control" value="0">
                                            <span class="input-group-text">đ</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Giá giảm</label>
                                        <div class="input-group">
                                            <input type="number" name="sale_price" class="form-control" value="0">
                                            <span class="input-group-text">đ</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Trình độ</label>
                                    <select name="level" class="form-select">
                                        <option value="beginner">Cơ bản (Beginner)</option>
                                        <option value="intermediate">Trung cấp (Intermediate)</option>
                                        <option value="advanced">Nâng cao (Advanced)</option>
                                    </select>
                                </div>


                                <div class="mb-3">
                                    <label class="form-label fw-bold">Vị trí hiển thị</label>
                                    <input type="number" name="position" class="form-control" value="0">
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary w-100 mb-2 py-2">
                                        <i class="bi bi-save"></i> LƯU KHÓA HỌC
                                    </button>
                                    <button type="reset" class="btn btn-outline-danger w-100 py-2">
                                        <i class="bi bi-arrow-counterclockwise"></i> Nhập lại
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // Xử lý preview ảnh khi chọn file mới
    document.getElementById('course_image').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            document.getElementById('course_preview').src = URL.createObjectURL(file);
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
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>