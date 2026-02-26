<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Gán Khóa Học - CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Gán Khóa Học Cho Học Viên</h4>
        </div>
        <div class="card-body">
            <form action="/assign-process" method="POST">
                
                <div class="mb-3">
                    <label class="form-label">1. Chọn học viên</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">-- Chọn học viên --</option>
                        <?php foreach($students as $student): ?>
                            <option value="<?= $student['id'] ?>"><?= $student['name'] ?> (<?= $student['email'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">2. Chọn khóa học</label>
                    <select name="course_id" id="course_select" class="form-select" required>
                        <option value="">-- Chọn khóa học --</option>
                        <?php foreach($courses as $course): ?>
                            <option value="<?= $course['id'] ?>" data-price="<?= $course['price'] ?>">
                                <?= $course['name'] ?> - <?= number_format($course['price']) ?>đ
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">3. Xác nhận giá tiền (VND)</label>
                    <input type="number" name="price" id="confirm_price" class="form-control" required>
                    <small class="text-muted">Nhân viên có thể chỉnh giá nếu có giảm giá riêng.</small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/dashboard" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-success">Xác nhận gán khóa học</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Tự động điền giá tiền khi chọn khóa học
    document.getElementById('course_select').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        document.getElementById('confirm_price').value = price;
    });
</script>

</body>
</html>