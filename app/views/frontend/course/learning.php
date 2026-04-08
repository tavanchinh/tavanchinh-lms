<?php include __DIR__ . '/../layouts/header.php'; ?>
<link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
<link href="https://unpkg.com/@videojs/themes@1.0.1/dist/city/index.css" rel="stylesheet">
<link rel="stylesheet" href="/css/learning.css?v=1.0.3">
<div class="learning-layout">
<div class="container">
    <div class="video-section">
        <div class="video-player-container">
            <?php 
                $firstLesson = !empty($chapters[0]['lessons'][0]) ? $chapters[0]['lessons'][0] : null;
                $videoSrc = $firstLesson ? "https://drive.google.com/file/d/" . $firstLesson['link_video'] . "/preview" : "";
                //var_dump($firstLesson);die();
            ?>
            <video id="my-player" class="video-js vjs-theme-city" controls preload="auto" >
                <p class="vjs-no-js"> Để xem video này, vui lòng bật JavaScript.</p>
            </video>
        </div>
        
        <div class="video-info">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h1 class="fw-bold h4 mb-0" id="current-lesson-title"><?= htmlspecialchars($firstLesson['name'] ?? 'Bài học chưa được xác định') ?></h1>
                <span id="finish-badge" class="badge bg-secondary d-none">Đã hoàn thành</span>
            </div>
            <hr>
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-3">
                    <ul class="nav nav-pills nav-fill custom-tabs" id="lessonTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active fw-bold" data-bs-toggle="tab" href="#tab-overview">
                                <i class="bi bi-info-circle me-2"></i>Tổng quan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-bold" data-bs-toggle="tab" href="#tab-docs">
                                <i class="bi bi-cloud-arrow-down me-2"></i>Tài liệu <span class="badge bg-danger ms-1">Mới</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-bold" data-bs-toggle="tab" href="#tab-qa">
                                <i class="bi bi-chat-dots me-2"></i>Hỏi đáp
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-overview">
                            <h6 class="fw-bold text-dark">Nội dung chính bài học:</h6>
                            <p class="text-muted" style="line-height: 1.8;">
                                <?= nl2br($course['summary'] ?? 'Chào mừng bạn đến với bài giảng của Tạ Văn Chinh. Trong bài này chúng ta sẽ đi sâu vào thực hành quy trình ra file CNC thực tế.') ?>
                            </p>
                            <hr class="opacity-10">
                            <div class="bg-primary-subtle p-3 rounded-3 d-flex align-items-center">
                                <i class="bi bi-lightbulb text-primary fs-4 me-3"></i>
                                <small class="text-primary-emphasis">Mẹo: Bạn nên vừa xem video vừa mở phần mềm SketchUp để thực hành song song các thao tác trên bài giảng.</small>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-docs" role="tabpanel">
                            <div class="row g-3">
                                <?php if (!empty($documents)): ?>
                                    <?php foreach ($documents as $doc): ?>
                                        <?php 
                                            $fileExt = strtolower($doc['file_type']);
                                            $icon = 'bi-file-earmark-arrow-down'; 
                                            $colorClass = 'text-primary';

                                            if (in_array($fileExt, ['zip', 'rar', '7z'])) {
                                                $icon = 'bi-file-earmark-zip';
                                                $colorClass = 'text-warning';
                                            } elseif ($fileExt === 'pdf') {
                                                $icon = 'bi-file-earmark-pdf';
                                                $colorClass = 'text-danger';
                                            } elseif (in_array($fileExt, ['xls', 'xlsx', 'csv'])) {
                                                $icon = 'bi-file-earmark-excel';
                                                $colorClass = 'text-success';
                                            } elseif ($fileExt === 'skp') {
                                                // LỰA CHỌN CHO SKETCHUP
                                                $icon = 'bi-box'; // Icon hình hộp đại diện cho 3D
                                                $colorClass = 'text-info'; // Màu xanh lơ hoặc màu đỏ đặc trưng của SketchUp
                                            }
                                        ?>
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-white hover-shadow-sm transition">
                                                <div class="d-flex align-items-center overflow-hidden">
                                                    <i class="bi <?= $icon ?> fs-2 <?= $colorClass ?> me-3"></i>
                                                    <div class="overflow-hidden">
                                                        <div class="fw-bold small text-truncate" title="<?= htmlspecialchars($doc['file_name']) ?>">
                                                            <?= htmlspecialchars($doc['file_name']) ?>
                                                        </div>
                                                        <div class="text-muted" style="font-size: 0.75rem;">
                                                            Dung lượng: <?= $doc['file_size'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <a href="/<?= $doc['file_path'] ?>" 
                                                download="<?= $doc['file_name'] ?>" 
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3 ms-2">
                                                    <i class="bi bi-download"></i> Tải về
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12 text-center py-5">
                                        <i class="bi bi-folder2-open display-4 text-muted"></i>
                                        <p class="mt-2 text-muted small">Khóa học này chưa có tài liệu đính kèm.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <style>
                            /* Hiệu ứng hover cho đẹp hơn trên laptop */
                            .hover-shadow-sm { transition: 0.3s; }
                            .hover-shadow-sm:hover { 
                                box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important; 
                                border-color: #0d6efd!important;
                            }
                        </style>

                        <div class="tab-pane fade" id="tab-qa">
                            <form action="/learning/comment" method="POST" class="mb-4">
                                <textarea name="content" class="form-control rounded-4 border-light-subtle bg-light mb-2" rows="3" placeholder="Bạn gặp vướng mắc ở thao tác nào trong bài này?"></textarea>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold">Gửi câu hỏi</button>
                            </form>
                            
                            <div class="d-flex gap-3 mb-3 pb-3 border-bottom border-light">
                                <img src="https://ui-avatars.com/api/?name=Hoc+Vien&background=random" class="rounded-circle" width="40" height="40">
                                <div>
                                    <div class="fw-bold small">Nguyễn Văn An <span class="text-muted fw-normal ms-2 small">2 giờ trước</span></div>
                                    <div class="small text-secondary mt-1">Thầy cho em hỏi bài này mình dùng dao mấy mm để phá thô ạ?</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="playlist-sidebar">
        <div class="playlist-header">
            <div class="course-progress-container mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="fw-bold">Tiến độ học tập</small>
                    <small class="text-primary fw-bold"><?= $progressPercent ?>%</small>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                        role="progressbar" 
                        style="width: <?= $progressPercent ?>%" 
                        aria-valuenow="<?= $progressPercent ?>" 
                        aria-valuemin="0" 
                        aria-valuemax="100">
                    </div>
                </div>
                <p class="text-muted mt-1" style="font-size: 11px;">
                    Đã hoàn thành <?= count($completedLessonIds) ?> / <?= $totalLessons ?> bài giảng
                </p>
            </div>
            <h6 class="mb-2 fw-bold">Nội dung khóa học</h6>
            
        </div>
        <div class="playlist-body" id="playlist-body">
            <?php if (!empty($chapters)): ?>
                <?php 
                // 1. Khởi tạo biến cờ: Bài đầu tiên luôn được mở
                $canViewNext = true; 
                // Mảng chứa ID các bài đã hoàn thành (Bạn cần truyền mảng này từ Controller sang View)
                $completedIds = $completedLessonIds ?? []; 
                ?>

                <?php foreach ($chapters as $chapter): ?>
                    <div class="chapter-title text-uppercase bg-light p-2 fw-bold small">
                        <?= htmlspecialchars($chapter['name']) ?>
                    </div>
                    <div class="lesson-list">
                        <?php if (!empty($chapter['lessons'])): ?>
                            <?php 
                            // Khởi tạo lại biến cờ cho mỗi chương nếu cần, hoặc dùng biến từ vòng lặp ngoài
                            $canViewNext = isset($canViewNext) ? $canViewNext : true; 
                            $completedIds = $completedLessonIds ?? []; 
                            ?>
                            <?php 
                            //echo '<pre>';
                            //print_r($chapter['lessons']);  die();
                            ?>
                            <?php foreach ($chapter['lessons'] as $lesson): ?>
                                <?php 
                                // 1. Kiểm tra khóa theo trình tự (Phải xong bài trước mới mở bài sau)
                                $isLockedByOrder = ($isOwned && !$canViewNext); 

                                // 2. Kiểm tra khóa theo nợ phí (Lấy từ logic Controller anh em mình đã làm)
                                $isLockedByDebt = (isset($lesson['is_locked_by_debt']) && $lesson['is_locked_by_debt'] === true);
                                //var_dump($isLockedByDebt); // Debug xem có đúng là khóa do nợ không
                                // Tổng hợp trạng thái khóa
                                $isLocked = ($isLockedByOrder || $isLockedByDebt);

                                $isCompleted = in_array($lesson['id'], $completedIds);
                                
                                // Cập nhật biến cờ cho bài kế tiếp trong danh sách
                                $canViewNext = $isCompleted;
                                ?>

                                <a href="javascript:void(0)" 
                                class="lesson-item js-lesson-item <?= ($firstLesson && $lesson['id'] == $firstLesson['id']) ? 'active' : '' ?> <?= $isLocked ? 'locked' : '' ?>"
                                data-id="<?= $lesson['id'] ?>"
                                data-name="<?= htmlspecialchars($lesson['name']) ?>"
                                data-locked="<?= $isLocked ? 'true' : 'false' ?>"
                                data-lock-type="<?= $isLockedByDebt ? 'debt' : 'order' ?>"
                                onclick="loadLesson(this)"
                                >
                                    <div class="lesson-thumbnail">
                                        <?php if ($isLocked): ?>
                                            <i class="bi bi-lock-fill"></i>
                                        <?php elseif ($isCompleted): ?>
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        <?php else: ?>
                                            <i class="bi bi-play-circle-fill"></i>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="lesson-info">
                                        <div class="fw-bold small"><?= htmlspecialchars($lesson['name']) ?></div>
                                        <span class="lesson-duration text-muted" style="font-size: 11px;">
                                            <i class="bi bi-clock me-1"></i><?= $lesson['duration'] ?> phút
                                            <?php if ($isLockedByDebt): ?>
                                                <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 9px;">Đóng phí để mở</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-3 small text-muted fst-italic">Đang cập nhật bài học...</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-4 text-center text-muted">Chưa có bài học nào trong khóa học này.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>
<script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>
<script src="https://unpkg.com/videojs-hls-quality-selector@2.0.0/dist/videojs-hls-quality-selector.min.js"></script>
<script>
    window.EduConfig = {
        isOwned: <?= json_encode((bool)$isOwned) ?>,
        courseId: <?= json_encode($course['id']) ?>,
        courseSlug: <?= json_encode($course['slug'] ?? '') ?>,
        remainingAmount: <?= json_encode(number_format($remainingAmount ?? 0)) ?>,
        userPhone: '<?= $_SESSION['user_phone'] ?? "" ?>',
        previewLimit: 900 // Giới hạn 15 phút học thử
    };
</script>
<script src="/js/learning.js?v=<?= time() ?>"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>