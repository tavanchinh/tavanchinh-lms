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

                <form action="/courses/store" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tên khóa học <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Nhập tên khóa học..." required>
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
                                    <label class="form-label fw-bold">Ảnh đại diện</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <div class="form-text">Kích thước gợi ý: 800x450px.</div>
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

<?php include __DIR__ . '/../layouts/footer.php'; ?>