<?php include __DIR__ . '/../layouts/header.php'; ?>
<link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
<link href="https://unpkg.com/@videojs/themes@1.0.1/dist/city/index.css" rel="stylesheet">
<link rel="stylesheet" href="/css/learning.css?v=1.0.3">
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
                            <?php foreach ($chapter['lessons'] as $lesson): ?>
                                <?php 
                                // 2. Kiểm tra trạng thái khóa
                                // Nếu là học viên đã mua khóa học thì mới áp dụng logic khóa bài theo trình tự
                                $isLocked = ($isOwned && !$canViewNext); 
                                $isCompleted = in_array($lesson['id'], $completedIds);
                                
                                // 3. Cập nhật biến cờ cho bài tiếp theo: 
                                // Bài kế tiếp chỉ mở nếu bài hiện tại ĐÃ HOÀN THÀNH
                                $canViewNext = $isCompleted;
                                ?>

                                <a href="javascript:void(0)" 
                                class="lesson-item js-lesson-item <?= ($firstLesson && $lesson['id'] == $firstLesson['id']) ? 'active' : '' ?> <?= $isLocked ? 'locked' : '' ?>"
                                data-id="<?= $lesson['id'] ?>"
                                data-name="<?= htmlspecialchars($lesson['name']) ?>"
                                data-locked="<?= $isLocked ? 'true' : 'false' ?>"
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
<script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>
<script src="https://unpkg.com/videojs-hls-quality-selector@2.0.0/dist/videojs-hls-quality-selector.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        if (typeof videojs !== 'undefined') {
            const Button = videojs.getComponent('Button');

            // ✅ Class PrevButton
            class PrevButton extends Button {
                constructor(player, options = {}) {
                    super(player, options);
                    this.controlText('Bài trước');
                }
                createEl() {
                    const button = super.createEl();
                    button.className = 'vjs-prev-button vjs-control vjs-button';
                    button.innerHTML = '<i class="bi bi-skip-backward-fill" style="line-height: 50px; font-size: 1.2rem;"></i>';
                    return button;
                }
                handleClick() {
                    // --- SỬA Ở ĐÂY ---
                    if (typeof window.changeLesson === 'function') window.changeLesson('prev');
                }
            }
            videojs.registerComponent('PrevButton', PrevButton);

            // ✅ Class NextButton
            class NextButton extends Button {
                constructor(player, options = {}) {
                    super(player, options);
                    this.controlText('Bài tiếp theo');
                }
                createEl() {
                    const button = super.createEl();
                    button.className = 'vjs-next-button vjs-control vjs-button';
                    button.innerHTML = '<i class="bi bi-skip-forward-fill" style="line-height: 50px; font-size: 1.2rem;"></i>';
                    return button;
                }
                handleClick() {
                    // --- SỬA Ở ĐÂY ---
                    if (typeof window.changeLesson === 'function') window.changeLesson('next');
                }
            }
            videojs.registerComponent('NextButton', NextButton);

            // ✅ Khởi tạo Player
            window.player = videojs('my-player', {
                playbackRates: [0.5, 1, 1.5, 2],
                controlBar: {
                    playToggle: true,
                    PrevButton: true,
                    NextButton: true,
                    currentTimeDisplay: true,
                    timeDivider: true,
                    durationDisplay: true,
                    remainingTimeDisplay: false,
                    children: [
                        'playToggle', 'PrevButton', 'NextButton', 'volumePanel',
                        'progressControl', 'spacer', 'currentTimeDisplay',
                        'timeDivider', 'durationDisplay', 'playbackRateMenuButton','qualitySelector',
                        'fullscreenToggle',
                    ],
                },
            });            
            
            player.ready(function() {
                //console.log('✅ Video player đã khởi tạo thành công!');

                // 1. Kích hoạt plugin chọn chất lượng
                if (typeof player.hlsQualitySelector === 'function') {
                    player.hlsQualitySelector({ displayCurrentQuality: true });
                }

                // --- THÊM LOGIC GHI NHỚ VỊ TRÍ & TỰ CHUYỂN BÀI Ở ĐÂY ---
                this.on('loadedmetadata', function() {
                    // Khi video đã sẵn sàng, kiểm tra nếu có vị trí đã lưu cho bài học này
                    const currentId = document.querySelector('.lesson-item.active')?.getAttribute('data-id');
                    if (currentId) {
                        const savedTime = localStorage.getItem('video_pos_' + currentId);
                        if (savedTime) this.currentTime(parseFloat(savedTime));
                    }
                });

                this.on('timeupdate', function() {
                    const currentId = document.querySelector('.lesson-item.active')?.getAttribute('data-id');
                    if (currentId && this.currentTime() > 0) {
                        localStorage.setItem('video_pos_' + currentId, this.currentTime());
                    }
                    // ============================================================
                    // ✅ THÊM ĐOẠN NÀY VÀO ĐÂY: KIỂM TRA GIỚI HẠN HỌC THỬ
                    // ============================================================
                    const IS_OWNED = <?php echo json_encode((bool)$isOwned); ?>;
                    const PREVIEW_LIMIT = 900; 

                    // Nếu chưa mua khóa học mới thực hiện giới hạn
                    if (!IS_OWNED) {
                        if (this.currentTime() >= PREVIEW_LIMIT) {
                            this.pause();
                            this.controls(false);
                            //this.currentTime(PREVIEW_LIMIT); // Giữ kim ở mốc 10:00

                            if (!document.getElementById('limit-overlay')) {
                                showVideoOverlay(currentId); // Gọi hàm hiển thị thông báo
                            }
                        }
                    }
                });

                this.on('ended', function() {
                    // 1. Lấy ID bài học hiện tại từ phần tử đang active
                    const activeLesson = document.querySelector('.lesson-item.active');
                    
                    if (activeLesson) {
                        const currentId = activeLesson.getAttribute('data-id');
                        console.log("Đang lưu tiến độ cho bài học ID:", currentId);

                        // 2. Gọi AJAX gửi lên Server (LessonController)
                        fetch('/lesson/complete', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ lesson_id: currentId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log("Đã cập nhật DB thành công!");
                                
                                // Cập nhật giao diện: Đổi icon khóa/play thành tích xanh (tùy chọn)
                                const icon = activeLesson.querySelector('i');
                                if (icon) icon.className = 'bi bi-check-circle-fill text-success';
                                
                                // Mở khóa bài tiếp theo trên giao diện để có thể click được
                                if (data.next_lesson_id) {
                                    const nextLesson = document.querySelector(`.lesson-item[data-id="${data.next_lesson_id}"]`);
                                    if (nextLesson) {
                                        nextLesson.classList.remove('locked');
                                        nextLesson.setAttribute('data-locked', 'false');
                                        const icon = nextLesson.querySelector('.bi-lock-fill');
                                        if (icon) {
                                            icon.classList.remove('bi-lock-fill');
                                            icon.classList.add('bi-play-circle-fill');
                                            icon.style.color = ''; 
                                        }
                                        // Cập nhật lại onclick nếu cần
                                        nextLesson.setAttribute('onclick', 'loadLesson(this)');
                                    }
                                }
                            }
                        })
                        .catch(err => console.error("Lỗi khi lưu tiến độ:", err))
                        .finally(() => {
                            // 3. Sau khi xong xuôi (hoặc kể cả lỗi), mới chuyển bài tiếp theo
                            setTimeout(() => {
                                if (typeof window.changeLesson === 'function') window.changeLesson('next');
                            }, 1500);
                        });
                    } else {
                        // Trường hợp phòng hờ không tìm thấy activeLesson
                        if (typeof window.changeLesson === 'function') window.changeLesson('next');
                    }
                });
            });
            

            // =========================================================
            // ✅ HÀM BỔ TRỢ (Định nghĩa dưới Player để điều khiển AJAX/URL)
            // =========================================================
            
            window.loadLesson = function(element, shouldPlay = true) {
                if (!element) return;
                const lessonId = element.getAttribute('data-id');
                const lessonName = element.getAttribute('data-name');
                const isLocked = element.getAttribute('data-locked') === 'true';

                if (isLocked) {
                    if (window.player) window.player.pause();
                    showVideoOverlay(lessonId, 'locked'); // Gọi hàm dùng chung với type 'locked'
                    return;
                }
                
                // --- BẮT ĐẦU GỌI getStreamToken TẠI ĐÂY ---
                fetch('/course/getStreamToken/' + lessonId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.token) {
                            // Nếu lấy được token, nạp video kèm token đó
                            player.src({
                                src: '/course/stream/' + lessonId + '?token=' + data.token,
                                type: 'application/x-mpegURL'
                            });

                            // Cập nhật giao diện (Active bài học)
                            document.querySelectorAll('.lesson-item').forEach(el => el.classList.remove('active'));
                            element.classList.add('active');

                            // Đổi tiêu đề và URL
                            const titleEl = document.getElementById('current-lesson-title');
                            if (titleEl) titleEl.innerText = lessonName;
                            
                            const newUrl = window.location.pathname + '?id=' + lessonId;
                            window.history.pushState({ path: newUrl }, '', newUrl);

                            if (shouldPlay) {
                                player.ready(function() {
                                    this.play().catch(e => console.log("Auto-play blocked"));
                                });
                            }
                        } else {
                            alert("Lỗi: " + (data.error || "Không thể lấy mã truy cập video"));
                        }
                    })
                    .catch(err => console.error("Lỗi kết nối Server:", err));
            };

            window.changeLesson = function(direction) {
                const items = Array.from(document.querySelectorAll('.lesson-item'));
                const current = document.querySelector('.lesson-item.active');
                const index = items.indexOf(current);
                let target = (direction === 'next') ? items[index + 1] : items[index - 1];
                if (target) window.loadLesson(target);
            };

            // Gán sự kiện click cho danh sách bài học
            document.addEventListener('click', function(e) {
                // Chỉ xử lý nếu click vào .lesson-item hoặc con của nó
                const item = e.target.closest('.lesson-item');
                
                // KIỂM TRA: Chỉ chạy logic nếu phần tử click nằm TRONG playlist 
                // (giả sử playlist của bạn nằm trong thẻ có class .playlist-body hoặc .sidebar-column)
                if (item && item.classList.contains('lesson-item')) {
                    // Nếu item này thuộc dropdown hoặc navbar thì bỏ qua
                    if (item.closest('.navbar') || item.closest('.dropdown-menu')) return;

                    e.preventDefault();
                    window.loadLesson(item);
                }
            }, false);

            // Xử lý khi F5 hoặc vào link có sẵn ID
            const urlParams = new URLSearchParams(window.location.search);
            const idFromUrl = urlParams.get('id');
            const startLesson = idFromUrl ? document.querySelector(`.lesson-item[data-id="${idFromUrl}"]`) : document.querySelector('.lesson-item');
            if (startLesson) window.loadLesson(startLesson, false);

        } else {
            console.error('❌ Video.js chưa được tải!');
        }
    });

    function showVideoOverlay(lessonId, type = 'preview') {
        const videoContainer = document.querySelector('.video-player-container');
        if (!videoContainer) return;

        // Xóa overlay cũ nếu đang tồn tại
        const oldOverlay = document.getElementById('video-custom-overlay');
        if (oldOverlay) oldOverlay.remove();

        const overlay = document.createElement('div');
        overlay.id = 'video-custom-overlay';
        overlay.style = `
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.85); color: white; display: flex;
            flex-direction: column; align-items: center; justify-content: center;
            z-index: 10; padding: 20px; text-align: center; border-radius: 12px;
        `;

        // Cấu hình nội dung dựa trên loại (type)
        let content = '';
        if (type === 'preview') {
            content = `
                <i class="bi bi-clock-history" style="font-size: 3.5rem; color: #ffc107;"></i>
                <h3 class="mt-3 fw-bold">Hết thời gian học thử</h3>
                <p class="mb-4">Vui lòng đăng ký khóa học để xem trọn bộ kiến thức và ủng hộ tác giả.</p>
                <div class="d-flex gap-2">
                    <a href="/<?= $course['slug'] ?>" class="btn btn-warning btn-lg fw-bold px-4">ĐĂNG KÝ NGAY</a>
                    <button onclick="resetTrial('${lessonId}')" class="btn btn-outline-light">Xem lại từ đầu</button>
                </div>
            `;
        } else if (type === 'locked') {
            content = `
                <i class="bi bi-shield-lock-fill" style="font-size: 3.5rem; color: #dc3545;"></i>
                <h3 class="mt-3 fw-bold">Nội dung chưa mở khóa</h3>
                <p class="mb-4">Bạn cần hoàn thành bài học trước đó để tiếp tục bài học này.</p>
                <div class="d-flex gap-2">
                    <button onclick="this.parentElement.parentElement.remove()" class="btn btn-warning fw-bold px-4">ĐÃ HIỂU</button>
                    <button onclick="goToCurrentLesson()" class="btn btn-outline-light">Quay lại bài đang học</button>
                </div>
            `;
        }

        overlay.innerHTML = content;
        videoContainer.appendChild(overlay);
    }

    window.resetTrial = function(lessonId) {
        // 1. Xóa ghi nhớ vị trí trong localStorage của bài học này
        localStorage.removeItem('video_pos_' + lessonId);

        // 2. Ẩn thông báo khóa (overlay)
        const overlay = document.getElementById('video-custom-overlay');
        if (overlay) overlay.remove();

        // 3. Đưa video về giây đầu tiên và phát lại
        if (player) {
            player.currentTime(0);
            player.controls(true); // Hiển thị lại controls nếu bị ẩn
            player.play();
        }
    };

    // Đảm bảo hàm này nằm ở phạm vi toàn cục (Global Scope)
    window.goToCurrentLesson = function() {
        // 1. Tìm bài học đang 'active' mà KHÔNG bị khóa (đây là bài người dùng đang học dở)
        const currentLesson = document.querySelector('.lesson-item.active:not(.locked)');
        
        // 2. Nếu không tìm thấy bài active hợp lệ, tìm bài đầu tiên không bị khóa
        const firstAvailable = currentLesson || document.querySelector('.lesson-item:not(.locked)');

        if (firstAvailable) {
            // Tải lại bài học hợp lệ
            window.loadLesson(firstAvailable);
            
            // Cuộn sidebar đến bài học đó để người dùng thấy
            firstAvailable.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // 3. Xóa bỏ lớp phủ (overlay)
        const overlay = document.getElementById('video-custom-overlay');
        if (overlay) {
            overlay.remove();
        }
    };

    // Hàm hỗ trợ mở khóa bài học trên giao diện
    function unlockNextLesson(nextId) {
        const nextLessonEl = document.querySelector(`.lesson-item[data-id="${nextId}"]`);
        
        if (nextLessonEl && nextLessonEl.classList.contains('locked')) {
            // Gỡ bỏ class locked
            nextLessonEl.classList.remove('locked');
            
            // Cập nhật lại icon từ khóa sang play
            const icon = nextLessonEl.querySelector('.bi-lock-fill');
            if (icon) {
                icon.className = 'bi bi-play-circle';
            }

            // Cập nhật lại sự kiện onclick để cho phép click bài đó
            nextLessonEl.setAttribute('onclick', 'loadLesson(this)');
            
            // (Tùy chọn) Tự động chuyển sang bài tiếp theo sau 3 giây
            setTimeout(() => {
                if (confirm("Bài học tiếp theo đã mở. Bạn có muốn chuyển bài ngay không?")) {
                    window.loadLesson(nextLessonEl);
                }
            }, 3000);
        }
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>