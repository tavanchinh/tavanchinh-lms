<?php include __DIR__ . '/../layouts/header.php'; ?>

<main>
    <section class="py-5 text-center bg-white border-bottom mb-5">
        <div class="container py-4">
            <h1 class="display-5 fw-bold mb-3">Học tập không giới hạn</h1>
            <p class="lead text-muted mx-auto" style="max-width: 600px;">Rất nhiều bài học chất lượng đang chờ bạn khám phá. Hãy bắt đầu hành trình ngay hôm nay!</p>
        </div>
    </section>

    <div class="container mb-5">
        <h3 class="fw-bold mb-4"><i class="bi bi-mortarboard me-2 text-primary"></i>Khóa học nổi bật</h3>
        <div class="row g-4">
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm course-card">
                            <a href="/<?= htmlspecialchars($course['slug']) ?>" class="text-decoration-none">
                                <img src="/uploads/<?= htmlspecialchars($course['image'] ?: 'default.jpg') ?>" 
                                     class="card-img-top" alt="<?= htmlspecialchars($course['name']) ?>" 
                                     style="height: 160px; object-fit: cover;">
                                
                                <div class="card-body pb-0">
                                    <h6 class="fw-bold mb-2 text-dark" style="height: 40px; overflow: hidden;">
                                        <?= htmlspecialchars($course['name']) ?>
                                    </h6>
                                </div>
                            </a>

                            <div class="card-body pt-0">
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="text-danger fw-bold"><?= number_format($course['price'], 0, ',', '.') ?>đ</span>
                                    <a href="/course/<?= htmlspecialchars($course['slug']) ?>" class="btn btn-sm btn-primary rounded-pill px-3">Học ngay</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Hiện chưa có khóa học nào để hiển thị.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <section class="bg-light py-5 border-top">
        <div class="container py-4 text-center">
            <h3 class="fw-bold mb-5">Cảm nhận từ học viên</h3>
            <div class="row g-4">
                <?php 
                $feedbacks = [
                    ['name' => 'Nguyễn Văn A', 'job' => 'Web Developer', 'content' => 'Kiến thức rất thực tế, giúp tôi có thể đi làm ngay sau khi học.'],
                    ['name' => 'Trần Thị B', 'job' => 'Sinh viên', 'content' => 'Giảng viên hỗ trợ nhiệt tình, bài giảng dễ hiểu ngay cả với người mới.'],
                    ['name' => 'Lê Văn C', 'job' => 'Freelancer', 'content' => 'Nền tảng học rất mượt, video chất lượng cao, ID video load rất nhanh.'],
                ];
                foreach ($feedbacks as $fb): ?>
                <div class="col-md-4 text-start">
                    <div class="p-4 bg-white shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #0d6efd;">
                        <p class="font-italic text-muted mb-3">"<?= $fb['content'] ?>"</p>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold small"><?= $fb['name'] ?></h6>
                                <small class="text-muted"><?= $fb['job'] ?></small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../layouts/footer.php'; ?>