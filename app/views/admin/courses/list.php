<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="container-fluid">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Quản lý Khóa học</h2>
        <a href="/admin/courses/create" class="btn-add" style="padding: 10px 20px; background: #28a745; color: #fff; text-decoration: none; border-radius: 4px;">+ Thêm khóa học mới</a>
    </div>

    <table border="1" style="width: 100%; border-collapse: collapse; background: #fff;">
        <thead>
            <tr style="background: #f4f4f4;">
                <th style="padding: 12px;">ID</th>
                <th>Hình ảnh</th>
                <th>Tên khóa học</th>
                <th>Danh mục</th>
                <th>Giá</th>
                <th>Trình độ</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course): ?>
                <tr style="text-align: center; border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;"><?php echo $course['id']; ?></td>
                    <td>
                        <img src="/uploads/<?php echo $course['image']; ?>" alt="img" style="width: 80px; border-radius: 4px;">
                    </td>
                    <td style="text-align: left; padding-left: 10px;">
                        <strong><?php echo $course['name']; ?></strong>
                    </td>
                    <td><?php echo $course['category_name'] ?? 'Chưa phân loại'; ?></td>
                    <td style="color: red; font-weight: bold;">
                        <?php echo number_format($course['price'], 0, ',', '.'); ?>đ
                    </td>
                    <td><?php echo ucfirst($course['level']); ?></td>
                    <td>
                        <?php if ($course['status'] == 1): ?>
                            <span style="color: green;">Hiển thị</span>
                        <?php else: ?>
                            <span style="color: gray;">Ẩn</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="/admin/courses/edit/<?php echo $course['id']; ?>" style="color: blue;">Sửa</a> | 
                        <a href="/admin/courses/delete/<?php echo $course['id']; ?>" style="color: red;" onclick="return confirm('Xóa khóa học này?')">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="padding: 20px; text-align: center;">Chưa có khóa học nào được tạo.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>