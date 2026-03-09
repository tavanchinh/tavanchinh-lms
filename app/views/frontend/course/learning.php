<?php include __DIR__ . '/../layouts/header.php'; ?>
<link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
<link href="https://unpkg.com/@videojs/themes@1.0.1/dist/city/index.css" rel="stylesheet">
<link rel="stylesheet" href="/css/learning.css">
<div class="main-container">
    <div class="video-section">
        <div class="video-player-container">
            <?php 
                $firstLesson = !empty($chapters[0]['lessons'][0]) ? $chapters[0]['lessons'][0] : null;
                $videoSrc = $firstLesson ? "https://drive.google.com/file/d/" . $firstLesson['link_video'] . "/preview" : "";
                //var_dump($firstLesson);die();
            ?>
            <video
                id="my-player"
                class="video-js vjs-theme-city"
                controls
                preload="auto"
            >
                <source src="/course/stream/<?= $firstLesson ? $firstLesson['id'] : ''?>" type="video/mp4" />
                <p class="vjs-no-js">
                    Để xem video này, vui lòng bật JavaScript.
                </p>
            </video>
        </div>
        
        <div class="video-info">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h1 class="fw-bold h4 mb-0" id="current-lesson-title">Bài 1: Giới thiệu cấu trúc MVC trong PHP</h1>
                <span id="finish-badge" class="badge bg-secondary d-none">Đã hoàn thành</span>
            </div>
            <hr>
            <div class="lesson-content">
                <h5 class="fw-bold mb-3">Nội dung bài học</h5>
                <div style="line-height: 2;">
                    <p>Mô hình MVC (Model-View-Controller) giúp tách biệt logic nghiệp vụ khỏi giao diện người dùng...</p>
                    <div style="height: 600px; background: #f8f9fa; border-radius: 12px; padding: 20px; border: 1px dashed #ccc;" class="mt-4 text-center">
                        Nội dung bài học chi tiết...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="playlist-sidebar">
        <div class="playlist-header">
            <h6 class="mb-2 fw-bold">Nội dung khóa học</h6>
            <div class="progress rounded-pill" style="height: 6px;">
                <div id="course-progress" class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: 20%"></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <small class="text-muted" style="font-size: 11px;">Tiến độ bài học</small>
                <small id="progress-text" class="text-primary fw-bold" style="font-size: 11px;">20%</small>
            </div>
        </div>
        <div class="playlist-body" id="playlist-body">
            <?php if (!empty($chapters)): ?>
                <?php foreach ($chapters as $chapter): ?>
                    <div class="chapter-title text-uppercase bg-light p-2 fw-bold small">
                        <?= htmlspecialchars($chapter['name']) ?>
                    </div>
                    <div class="lesson-list">
                        <?php if (!empty($chapter['lessons'])): ?>
                            <?php foreach ($chapter['lessons'] as $lesson): ?>
                                <a href="#" 
                                class="lesson-item js-lesson-item <?= ($firstLesson && $lesson['id'] == $firstLesson['id']) ? 'active' : '' ?>"
                                data-id="<?= $lesson['id'] ?>"
                                data-name="<?= htmlspecialchars($lesson['name']) ?>"
                                >
                                    
                                    <div class="lesson-thumbnail">
                                        <i class="bi bi-play-circle-fill"></i>
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
            const player = videojs('my-player', {
                playbackRates: [0.5, 1, 1.5, 2],
                controlBar: {
                    currentTimeDisplay: true,
                    timeDivider: true,
                    durationDisplay: true,
                    remainingTimeDisplay: false,
                    children: [
                        'playToggle', 'PrevButton', 'NextButton', 'volumePanel',
                        'progressControl', 'spacer', 'currentTimeDisplay',
                        'timeDivider', 'durationDisplay', 'playbackRateMenuButton',
                        'fullscreenToggle',
                    ],
                },
            });
            
            player.ready(function() {
                console.log('✅ Video player đã khởi tạo thành công!');

                // --- THÊM LOGIC GHI NHỚ VỊ TRÍ & TỰ CHUYỂN BÀI Ở ĐÂY ---
                this.on('loadedmetadata', function() {
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
                            this.currentTime(PREVIEW_LIMIT); // Giữ kim ở mốc 10:00

                            if (!document.getElementById('limit-overlay')) {
                                showLimitOverlay(); // Gọi hàm hiển thị thông báo
                            }
                        }
                    }
                });

                this.on('ended', function() {
                    if (typeof window.changeLesson === 'function') window.changeLesson('next');
                });
            });

            // =========================================================
            // ✅ HÀM BỔ TRỢ (Định nghĩa dưới Player để điều khiển AJAX/URL)
            // =========================================================
            
            window.loadLesson = function(element, shouldPlay = true) {
                if (!element) return;
                const lessonId = element.getAttribute('data-id');
                const lessonName = element.getAttribute('data-name');

                // --- BẮT ĐẦU GỌI getStreamToken TẠI ĐÂY ---
                fetch('/course/getStreamToken/' + lessonId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.token) {
                            // Nếu lấy được token, nạp video kèm token đó
                            player.src({
                                src: '/course/stream/' + lessonId + '?token=' + data.token,
                                type: 'video/mp4'
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

    function showLimitOverlay() {
        const videoContainer = document.querySelector('.video-js');
        if (!videoContainer) return;

        const overlay = document.createElement('div');
        overlay.id = 'limit-overlay';
        overlay.style = `
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.85); color: white; display: flex;
            flex-direction: column; align-items: center; justify-content: center;
            z-index: 10; padding: 20px; text-align: center;
        `;
        overlay.innerHTML = `
            <i class="bi bi-lock-fill" style="font-size: 3.5rem; color: #ffc107;"></i>
            <h3 class="mt-3 fw-bold">Hết thời gian học thử</h3>
            <p class="mb-4">Vui lòng đăng ký khóa học để xem trọn bộ kiến thức và ủng hộ tác giả.</p>
            <div class="d-flex gap-2">
                <a href="/<?= $course['slug'] ?>" class="btn btn-warning btn-lg fw-bold px-4">ĐĂNG KÝ NGAY</a>
                <button onclick="location.reload()" class="btn btn-outline-light">Xem lại</button>
            </div>
        `;
        videoContainer.appendChild(overlay);
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>