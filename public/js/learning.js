document.addEventListener('DOMContentLoaded', function() {
    // Lấy cấu hình từ cầu nối PHP
    const CONFIG = window.EduConfig;

    if (typeof videojs !== 'undefined') {
        const Button = videojs.getComponent('Button');

        // ✅ 1. Định nghĩa PrevButton
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
                if (typeof window.changeLesson === 'function') window.changeLesson('prev');
            }
        }
        videojs.registerComponent('PrevButton', PrevButton);

        // ✅ 2. Định nghĩa NextButton
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
                if (typeof window.changeLesson === 'function') window.changeLesson('next');
            }
        }
        videojs.registerComponent('NextButton', NextButton);

        // ✅ 3. Khởi tạo Player
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
                    'timeDivider', 'durationDisplay', 'playbackRateMenuButton', 'qualitySelector',
                    'fullscreenToggle',
                ],
            },
        });

        window.player.ready(function() {
            const player = this;

            // Kích hoạt chất lượng HLS
            if (typeof player.hlsQualitySelector === 'function') {
                player.hlsQualitySelector({ displayCurrentQuality: true });
            }

            // Ghi nhớ vị trí video
            player.on('loadedmetadata', function() {
                const currentId = document.querySelector('.lesson-item.active')?.getAttribute('data-id');
                if (currentId) {
                    const savedTime = localStorage.getItem('video_pos_' + currentId);
                    if (savedTime) player.currentTime(parseFloat(savedTime));
                }
            });

            // Theo dõi thời gian thực
            player.on('timeupdate', function() {
                const currentId = document.querySelector('.lesson-item.active')?.getAttribute('data-id');
                if (currentId && player.currentTime() > 0) {
                    localStorage.setItem('video_pos_' + currentId, player.currentTime());
                }

                // Kiểm tra giới hạn học thử
                if (!CONFIG.isOwned && player.currentTime() >= CONFIG.previewLimit) {
                    player.pause();
                    player.controls(false);
                    if (!document.getElementById('video-custom-overlay')) {
                        showVideoOverlay(currentId, 'preview');
                    }
                }
            });

            // Tự động chuyển bài khi kết thúc
            player.on('ended', function() {
                const activeLesson = document.querySelector('.lesson-item.active');
                if (!activeLesson) return;

                const currentId = activeLesson.getAttribute('data-id');
                
                fetch('/lesson/complete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ lesson_id: currentId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const icon = activeLesson.querySelector('i');
                        if (icon) icon.className = 'bi bi-check-circle-fill text-success';
                        
                        if (data.next_lesson_id) {
                            const nextLesson = document.querySelector(`.lesson-item[data-id="${data.next_lesson_id}"]`);
                            if (nextLesson) {
                                nextLesson.classList.remove('locked');
                                nextLesson.setAttribute('data-locked', 'false');
                                const lockIcon = nextLesson.querySelector('.bi-lock-fill');
                                if (lockIcon) lockIcon.className = 'bi bi-play-circle-fill';
                                nextLesson.setAttribute('onclick', 'loadLesson(this)');
                            }
                        }
                    }
                })
                .finally(() => {
                    setTimeout(() => window.changeLesson('next'), 8000);
                });
            });
        });

    } else {
        console.error('❌ Video.js chưa được tải!');
    }

    // --- LOGIC PLAYLIST & HÀM BỔ TRỢ ---

    window.loadLesson = function(element, shouldPlay = true) {
        if (!element) return;

        const lessonId = element.getAttribute('data-id');
        const lessonName = element.getAttribute('data-name');
        const isLocked = element.getAttribute('data-locked') === 'true';
        const lockType = element.getAttribute('data-lock-type');

        if (isLocked) {
            if (window.player) window.player.pause();
            showVideoOverlay(lessonId, lockType === 'debt' ? 'payment_required' : 'locked');
            return;
        }

        fetch('/course/getStreamToken/' + lessonId)
            .then(res => res.json())
            .then(data => {
                if (data.token) {
                    window.player.src({
                        src: `/course/stream/${lessonId}?token=${data.token}`,
                        type: 'application/x-mpegURL'
                    });

                    document.querySelectorAll('.lesson-item').forEach(el => el.classList.remove('active'));
                    element.classList.add('active');

                    const titleEl = document.getElementById('current-lesson-title');
                    if (titleEl) titleEl.innerText = lessonName;

                    const newUrl = window.location.pathname + '?id=' + lessonId;
                    window.history.pushState({ path: newUrl }, '', newUrl);

                    if (shouldPlay) {
                        window.player.ready(function() {
                            this.play().catch(() => console.log("Auto-play blocked"));
                        });
                    }
                    const oldOverlay = document.getElementById('video-custom-overlay');
                    if (oldOverlay) oldOverlay.remove();
                }
            });
    };

    window.changeLesson = function(direction) {
        const items = Array.from(document.querySelectorAll('.lesson-item'));
        const current = document.querySelector('.lesson-item.active');
        const index = items.indexOf(current);
        let target = (direction === 'next') ? items[index + 1] : items[index - 1];
        if (target) window.loadLesson(target);
    };

    // Xử lý click bài học
    document.addEventListener('click', function(e) {
        const item = e.target.closest('.lesson-item');
        if (item && !item.closest('.navbar') && !item.closest('.dropdown-menu')) {
            e.preventDefault();
            window.loadLesson(item);
        }
    });

    // Xử lý khi F5
    const urlParams = new URLSearchParams(window.location.search);
    const idFromUrl = urlParams.get('id');
    const startLesson = idFromUrl ? document.querySelector(`.lesson-item[data-id="${idFromUrl}"]`) : document.querySelector('.lesson-item');
    if (startLesson) window.loadLesson(startLesson, false);

    // Keep-alive
    setInterval(() => fetch('/user/keep-alive'), 120000);
});

/**
 * Các hàm xử lý Overlay & Thanh toán
 */

function showVideoOverlay(lessonId, type = 'preview') {
    const videoContainer = document.querySelector('.video-player-container');
    const CONFIG = window.EduConfig;
    if (!videoContainer) return;

    const oldOverlay = document.getElementById('video-custom-overlay');
    if (oldOverlay) oldOverlay.remove();

    const overlay = document.createElement('div');
    overlay.id = 'video-custom-overlay';
    overlay.style = `
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.9); color: white; display: flex;
        flex-direction: column; align-items: center; justify-content: center;
        z-index: 100; padding: 30px; text-align: center; border-radius: 12px;
        backdrop-filter: blur(10px); transition: all 0.3s ease;
    `;

    let content = '';
    if (type === 'preview') {
        content = `
            <div class="overlay-content">
                <i class="bi bi-clock-history" style="font-size: 4rem; color: #ffc107;"></i>
                <h2 class="mt-3 fw-bold">Hết thời gian học thử</h2>
                <p class="mb-4 opacity-75">Vui lòng đăng ký khóa học để xem trọn bộ kiến thức.</p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="/${CONFIG.courseSlug}" class="btn btn-warning btn-lg fw-bold px-5 rounded-pill shadow">ĐĂNG KÝ NGAY</a>
                    <button onclick="resetTrial('${lessonId}')" class="btn btn-outline-light rounded-pill px-4">Xem lại từ đầu</button>
                </div>
            </div>`;
    } else if (type === 'locked') {
        content = `
            <div class="overlay-content">
                <i class="bi bi-shield-lock" style="font-size: 4rem; color: #6c757d;"></i>
                <h2 class="mt-3 fw-bold">Bài học đang tạm khóa</h2>
                <p class="mb-4 opacity-75">Bạn cần hoàn thành bài học trước đó.</p>
                <div class="d-flex gap-3 justify-content-center">
                    <button onclick="goToCurrentLesson()" class="btn btn-primary btn-lg fw-bold px-5 rounded-pill shadow">QUAY LẠI BÀI ĐANG HỌC</button>
                </div>
            </div>`;
    } else if (type === 'payment_required') {
        content = `
            <div class="overlay-content">
                <i class="bi bi-qr-code-scan" style="font-size: 3.5rem; color: #00d2ff;"></i>
                <h2 class="fw-bold">Mở toàn bộ khóa học</h2>
                <p class="text-white-50">Hoàn tất học phí: <strong class="text-warning h4">${CONFIG.remainingAmount}đ</strong></p>
                <div id="qr-display-area" class="mb-4">
                    <button onclick="generatePaymentQR('${lessonId}', '${CONFIG.remainingAmount}')" id="btn-get-qr" class="btn btn-light btn-lg fw-bold px-5 rounded-pill shadow-lg">LẤY MÃ QR THANH TOÁN</button>
                </div>
                <button onclick="document.getElementById('video-custom-overlay').remove()" class="btn btn-link text-white-50 text-decoration-none">Học tiếp các bài cũ</button>
            </div>`;
    }

    overlay.innerHTML = content;
    videoContainer.appendChild(overlay);
    if (window.player) window.player.pause();
}

window.resetTrial = function(lessonId) {
    localStorage.removeItem('video_pos_' + lessonId);
    const overlay = document.getElementById('video-custom-overlay');
    if (overlay) overlay.remove();
    if (window.player) {
        window.player.currentTime(0);
        window.player.controls(true);
        window.player.play();
    }
};

window.goToCurrentLesson = function() {
    const firstAvailable = document.querySelector('.lesson-item.active:not(.locked)') || document.querySelector('.lesson-item:not(.locked)');
    if (firstAvailable) {
        window.loadLesson(firstAvailable);
        firstAvailable.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    const overlay = document.getElementById('video-custom-overlay');
    if (overlay) overlay.remove();
};

window.generatePaymentQR = function(lessonId, amount) {
    const displayArea = document.getElementById('qr-display-area');
    const btnGetQr = document.getElementById('btn-get-qr');
    const CONFIG = window.EduConfig;

    btnGetQr.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang tạo mã...';
    btnGetQr.disabled = true;

    fetch('/thanh-toan', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            courseId: CONFIG.courseId,
            amount: amount.replace(/,/g, ''),
            isRegister: false,
            phone: CONFIG.userPhone,
            isDebtPayment: true,
            lessonId: lessonId
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.qrCode) {
            const qrImageUrl = `https://quickchart.io/qr?text=${encodeURIComponent(data.qrCode)}&size=200&margin=2`;
            displayArea.innerHTML = `
                <div class="bg-white p-3 rounded-4 shadow-lg animate__animated animate__flipInY" style="max-width: 200px; margin: 0 auto;">
                    <img src="${qrImageUrl}" class="img-fluid rounded-3 mb-2" alt="QR Payment">
                    <p class="text-center small text-muted mb-0">Quét mã để thanh toán</p>
                </div>`;
            // ✅ BẮT ĐẦU KIỂM TRA TRẠNG THÁI (Long Polling)
            // Truyền orderCode hoặc lessonId tùy theo logic xử lý nợ của anh
            startCheckingPayment(data.orderCode || lessonId);
        }
    });
};

let paymentCheckInterval = null;

function startCheckingPayment(orderCode) {
    // Xóa interval cũ nếu có để tránh chạy chồng chéo
    if (paymentCheckInterval) clearInterval(paymentCheckInterval);

    paymentCheckInterval = setInterval(() => {
        fetch(`/thanh-toan/check-status/${orderCode}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'PAID' || data.success === true) {
                    // 1. Dừng kiểm tra
                    clearInterval(paymentCheckInterval);
                    
                    // 2. Thông báo thành công
                    const displayArea = document.getElementById('qr-display-area');
                    displayArea.innerHTML = `
                        <div class="text-success animate__animated animate__bounceIn">
                            <i class="bi bi-check-circle-fill" style="font-size: 3rem;"></i>
                            <h5 class="fw-bold mt-2">Thanh toán thành công!</h5>
                            <p class="small">Hệ thống đang mở khóa bài học...</p>
                        </div>`;
                    
                    // 3. Tự động tải lại bài học sau 2 giây
                    setTimeout(() => {
                        location.reload(); // Cách đơn giản nhất để cập nhật lại toàn bộ trạng thái khóa
                    }, 2000);
                }
            })
            .catch(err => console.error("Lỗi kiểm tra thanh toán:", err));
    }, 3000); // Kiểm tra mỗi 3 giây
}