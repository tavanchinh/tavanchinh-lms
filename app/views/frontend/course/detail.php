<?php include __DIR__ . '/../layouts/header.php'; ?>

<nav class="bg-light py-3 border-bottom">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($course['name']) ?></li>
        </ol>
    </div>
</nav>

<main class="py-5 bg-white"> <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="p-4 p-md-5 border rounded-4 shadow-sm bg-white"> <h1 class="fw-bold mb-3 text-dark"><?= htmlspecialchars($course['name']) ?></h1>
                    
                    <div class="mb-4 text-secondary lead">
                        <?= $course['summary'] ?? '' ?>
                    </div>

                    <hr class="my-4 opacity-25">

                    <div class="course-description mb-5">
                        <h4 class="fw-bold mb-3">Mô tả khóa học</h4>
                        <div class="text-muted lh-lg">
                            <?= $course['description'] ?? 'Chào mừng bạn đến với khóa học này. Hãy cùng khám phá lộ trình học tập chi tiết bên dưới.' ?>
                        </div>
                    </div>

                    <div class="mb-0">
                        <h4 class="fw-bold mb-4">Nội dung khóa học</h4>
                        
                        <div class="accordion accordion-flush border rounded-3 overflow-hidden" id="accordionCourse">
                            <?php if (!empty($chapters)): ?>
                                <?php foreach ($chapters as $index => $chapter): ?>
                                    <div class="accordion-item border-bottom">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?> fw-bold bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#chapter-<?= $chapter['id'] ?>">
                                                <span class="text-primary me-2">Chương <?= $index + 1 ?>:</span> <?= htmlspecialchars($chapter['name']) ?>
                                            </button>
                                        </h2>
                                        <div id="chapter-<?= $chapter['id'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#accordionCourse">
                                            <div class="accordion-body p-0">
                                                <ul class="list-group list-group-flush">
                                                    <?php if (!empty($chapter['lessons'])): ?>
                                                        <?php foreach ($chapter['lessons'] as $lesson): ?>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 bg-white border-0">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="bi bi-play-circle text-primary me-3"></i>
                                                                    <span class="text-dark small fw-medium"><?= htmlspecialchars($lesson['name']) ?></span>
                                                                </div>
                                                                <?php if($lesson['is_preview']): ?>
                                                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">Học thử</span>
                                                                <?php else: ?>
                                                                    <i class="bi bi-lock-fill text-muted small"></i>
                                                                <?php endif; ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <li class="list-group-item text-muted small py-3 px-4 italic">Đang cập nhật bài học...</li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted italic p-4">Lộ trình học tập đang được cập nhật.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div> </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-lg sticky-top" style="top: 100px; border-radius: 20px; overflow: hidden;">
                    <img src="/uploads/<?= htmlspecialchars($course['image'] ?: 'default.jpg') ?>" class="card-img-top" alt="Course Image" >
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <span class="h2 fw-bold text-danger"><?= number_format($course['price'], 0, ',', '.') ?>đ</span>
                        </div>
                        
                        <?php if ($isOwned): ?>
                            <a href="/learning/<?= $course['slug'] ?>" class="btn btn-success btn-lg w-100 rounded-pill mb-3 fw-bold shadow">
                                <i class="bi bi-play-fill me-2"></i>VÀO HỌC NGAY
                            </a>
                        <?php else: ?>
                            <button class="btn btn-primary btn-lg w-100 rounded-pill mb-2 fw-bold shadow">
                                ĐĂNG KÝ NGAY
                            </button>

                            <a href="/learning/<?= $course['slug'] ?>?trial=1" class="btn btn-outline-secondary btn-lg w-100 rounded-pill mb-3 fw-bold border-2">
                                HỌC THỬ
                            </a>
                        <?php endif; ?>
                        
                        <div class="small text-muted">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-infinity me-2 text-primary"></i> Quyền truy cập trọn đời
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-phone me-2 text-primary"></i> Học trên mọi thiết bị
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-patch-check me-2 text-primary"></i> Hỗ trợ kỹ thuật 24/7
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    /* CSS để làm sạch nội dung từ Editor đổ ra */
    .course-description img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }
    .accordion-button:not(.collapsed) {
        color: #0d6efd;
        background-color: #f8f9fa;
        box-shadow: none;
    }
    .list-group-item:hover {
        background-color: #f8f9fa !important;
    }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>