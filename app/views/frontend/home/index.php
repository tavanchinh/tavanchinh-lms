<?php include __DIR__ . '/../layouts/header.php'; ?>

<main>
    <section class="hero-section py-5 mb-5" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-bottom: 1px solid #e2e8f0;">
        <div class="container text-center py-5">
            <h1 class="display-4 fw-bold text-dark mb-3">Tham gia khóa học ngay hôm nay</h1>
            <p class="lead text-muted mb-4 mx-auto" style="max-width: 600px;">Chủ động hơn trong việc vẽ và ra file CNC. Đồng hành trọn đời cùng đội ngũ chuyên nghiệp</p>
            <a href="#courses" class="btn btn-primary btn-lg px-5 rounded-pill shadow">Khám phá khóa học</a>
        </div>
    </section>

    <div class="container mb-5" id="courses">
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
                                    <a href="/<?= htmlspecialchars($course['slug']) ?>" class="btn btn-sm btn-primary rounded-pill px-3">Học ngay</a>
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
                    ['name' => 'Cao Ngọc Hải', 'job' => 'Chủ xưởng', 'content' => 'Cảm ơn thầy Chinh đã đào tạo và tạo ra 1 sân chơi chung cho anh em trong nghề có thể kết nối giao lưu'],
                    ['name' => 'Đức Nhật', 'job' => 'Chủ xưởng', 'content' => 'Mình tham gia khóa Aspire bên thầy Chinh nhưng thực sự được hỗ trợ rất nhiệt tình, được học thêm cả Corel'],
                    ['name' => 'Xuân Ninh', 'job' => 'Chủ xưởng', 'content' => 'Cảm ơn thầy rất nhiều, mùa hè này hẹn thầy xuống Hải Phòng nhé thầy'],
                    ['name' => 'Đức Hùng', 'job' => 'CTV Ra file', 'content' => 'Thầy Chinh dạy rất dễ hiểu, nhiệt tình hỗ trợ học viên. Mình đã học được rất nhiều kiến thức bổ ích từ thầy'],
                    ['name' => 'Hữu Phước', 'job' => 'Chủ xưởng', 'content' => 'Khóa học của thầy Chinh rất thực tế, giúp mình nắm vững quy trình làm việc và áp dụng ngay vào xưởng của mình'],
                    ['name' => 'Văn Hùng', 'job' => 'Chủ xưởng', 'content' => 'Mình đã tham gia khóa học của thầy Chinh và thực sự ấn tượng với cách thầy truyền đạt kiến thức một cách dễ hiểu và chi tiết']
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