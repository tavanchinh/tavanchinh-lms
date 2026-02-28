<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Học trực tuyến | TAVANCHINH</title>
    
    <link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
    <link href="https://unpkg.com/@videojs/themes@1.0.1/dist/city/index.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root { 
            --header-height: 70px; 
            --sidebar-width: 400px;
            --primary-color: #0d6efd;
            --main-bg: #f1f3f4; 
            --card-radius: 16px;
            --yt-red: #ff0000;
            --yt-dark: #030303;
        }
        
        body { 
            background-color: var(--main-bg); 
            overflow-y: auto; 
            overflow-x: hidden;
            height: auto; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }

        .navbar-frontend { 
            height: var(--header-height); 
            background: #fff; 
            border-bottom: 1px solid #e0e0e0;
            position: sticky;
            top: 0;
            z-index: 1050;
        }

        .main-container {
            max-width: 1600px; 
            margin: 0 auto;
            padding: 20px;
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        /* --- CỘT TRÁI --- */
        .video-section { 
            flex: 1; 
            min-width: 0;
        }

        .video-info {
            background: #fff;
            padding: 30px;
            border-radius: var(--card-radius);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        /* --- SKIN CHO VIDEO.JS (YOUTUBE STYLE) --- */
        .video-player-container {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            background: #000;
            position: relative;
            margin-bottom: 20px;
        }

        .video-js {
            width: 100% !important;
            height: auto !important;
            aspect-ratio: 16/9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        /* ===== NÚT PLAY LỚN GiỮA (YouTube Style) ===== */
        .video-js .vjs-big-play-button {
            background: rgba(255, 255, 255, 0.95) !important;
            border: none !important;
            border-radius: 50% !important;
            width: 96px !important;
            height: 96px !important;
            line-height: 96px !important;
            margin-top: -48px !important;
            margin-left: -48px !important;
            font-size: 2.8em !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 1, 1) !important;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3) !important;
            color: var(--yt-red) !important;
            cursor: pointer !important;
        }
        .vjs-fullscreen-control .vjs-control-text{
            
        }

        .video-js:hover .vjs-big-play-button {
            background: rgba(255, 255, 255, 0.98) !important;
            transform: scale(1.05);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.4) !important;
        }

        .video-js.vjs-playing .vjs-big-play-button {
            display: none !important;
        }

        /* ===== THANH ĐIỀU KHIỂN (Control Bar) ===== */
        .video-js .vjs-control-bar {
            background: linear-gradient(to top, 
                rgba(0, 0, 0, 1) 0%,
                rgba(0, 0, 0, 0.8) 30%,
                rgba(0, 0, 0, 0.3) 70%,
                rgba(0, 0, 0, 0) 100%) !important;
            height: 65px !important;
            padding: 10px 12px 0 12px !important;
            transition: all 0.3s ease !important;
        }

        .video-js:hover .vjs-control-bar {
            background: linear-gradient(to top, 
                rgba(0, 0, 0, 1) 0%,
                rgba(0, 0, 0, 0.85) 20%,
                rgba(0, 0, 0, 0) 100%) !important;
            
        }

        /* ===== THANH TIẾN TRÌNH (Progress Bar) ===== */
        .video-js .vjs-progress-control {
            position: absolute !important;
            width: 100% !important;
            height: 4px !important;
            top: -4px !important;
            left: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .video-js .vjs-progress-holder {
            background: rgba(255, 255, 255, 0.3) !important;
            height: 4px !important;
            border-radius: 2px !important;
            top:-4px !important;
        }

        .video-js .vjs-play-progress {
            background: var(--yt-red) !important;
            box-shadow: 0 0 6px rgba(255, 0, 0, 0.6) !important;
        }

        .video-js .vjs-play-progress:before {
            background: rgba(255, 255, 255, 0) !important;
            box-shadow: 0 0 4px 2px rgba(255, 0, 0, 0.7) !important;
            width: 12px !important;
            height: 12px !important;
            top: -4px !important;
            border-radius: 50% !important;
            opacity: 0 !important;
            transition: opacity 0.2s ease !important;
        }

        .video-js .vjs-progress-control:hover .vjs-play-progress:before {
            opacity: 1 !important;
            background: rgba(255, 255, 255, 0.9) !important;
        }

        .video-js .vjs-progress-control:hover .vjs-progress-holder {
            height: 6px !important;
        }

        /* ===== CÁC NÚT ĐIỀU KHIỂN (Buttons) ===== */
        /* Nút chung */

        .vjs-playback-rate .vjs-playback-rate-value{
            line-height: 3.3em;
            
        }
        .vjs-icon-play:before, .video-js .vjs-play-control .vjs-icon-placeholder:before, .video-js .vjs-big-play-button .vjs-icon-placeholder:before{
            font-size:2.5em;
        }
        .vjs-menu-button-popup .vjs-menu .vjs-menu-content{
            overflow: hidden !important; 
        }
        .video-js .vjs-volume-panel .vjs-volume-control.vjs-volume-horizontal {
            transition: none !important;
            transition: width 0.1s;
            height: 3em;
            margin-right: 0;
        }
        .video-js .vjs-button {
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 50px !important;
            height: 50px !important;
            padding: 0 !important;
            margin: 0 4px !important;
        }

        .video-js .vjs-button:hover {
            border-radius: 4px !important;
        }

        .video-js .vjs-button:active {
            background: rgba(255, 255, 255, 0.2) !important;
        }

        /* Icon trong nút */
        .video-js .vjs-button > .vjs-icon-placeholder:before {
            font-size: 1.6em !important;
            color: #fff !important;
            text-shadow: 0 0 4px rgba(0, 0, 0, 0.5) !important;
        }

        /* Nút Play/Pause */
        .video-js .vjs-play-control {
            order: 0 !important;
            flex:none;
            width: 50px !important;
            height: 50px !important;
            padding: 0 !important;
            margin: 0 4px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .video-js .vjs-play-control .vjs-icon-placeholder:before {
            font-size: 1.6em !important;
            line-height: 50px !important;
        }

        .video-js .vjs-play-control:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            border-radius: 4px !important;
        }

        /* Volume Control */
        /* Volume Control */
        .video-js .vjs-volume-panel {
            order: 3 !important;
            display: flex !important;
            align-items: center !important;
            margin: 0 4px !important;
            width: 50px !important;              /* ← DÒNG NÀY */
            height: 50px !important;             /* ← VÀ DÒNG NÀY */
            position: relative !important;
            padding-top:0;
        }

        .video-js .vjs-volume-control {
            width: auto !important;
        }

        .video-js .vjs-volume-control.vjs-volume-horizontal {
            width: 50px !important;
        }

        .video-js .vjs-volume-menu-button.vjs-volume-0 ~ .vjs-volume-control {
            display: none !important;
        }

        /* Time Display */
        .video-js .vjs-time-control {
            display: flex !important;
            align-items: center !important;
            font-size: 13px !important;
            color: #fff !important;
            margin: 0 8px 0 0 !important;
            font-weight: 500 !important;
            font-family: 'Arial', sans-serif !important;
            order:9;
            padding: 0;
        }

        .video-js .vjs-current-time-display:before,
        .video-js .vjs-duration-display:before {
            content: '';
            display: none;
        }

        .video-js .vjs-time-divider {
            padding: 0 4px !important;
            color: #fff !important;
            margin: 0 !important;
            min-width: 10px !important;
        }

        /* Thứ tự các nút */
        .video-js .vjs-prev-button {
            order: 1 !important;
        }

        .video-js .vjs-next-button {
            order: 2 !important;
        }

        /* Spacer */
        .video-js .vjs-spacer {
            
        }

        /* Nút Settings/Gear */
        .video-js .vjs-playback-rate,
        .video-js .vjs-settings-button {
            order: 10 !important;
        }

        .video-js .vjs-playback-rate .vjs-menu-button-inline .vjs-menu,
        .video-js .vjs-settings-button .vjs-menu {
            right: 0 !important;
            left: auto !important;
        }

        .video-js .vjs-fullscreen-control .vjs-icon-placeholder:before
        {
            font-size:3em !important;
        }

        /* Menu items */
        .video-js .vjs-menu {
            background: rgba(28, 28, 28, 0.95) !important;
            border-radius: 4px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4) !important;
            backdrop-filter: blur(10px) !important;
        }

        .video-js .vjs-menu-item {
            color: #fff !important;
            padding: 10px 16px !important;
            font-size: 14px !important;
            transition: background 0.15s ease !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .video-js .vjs-menu-item:last-child {
            border-bottom: none !important;
        }

        .video-js .vjs-menu-item:hover {
            background: rgba(255, 255, 255, 0.15) !important;
        }

        .video-js .vjs-menu-item.vjs-selected {
            background: rgba(255, 0, 0, 0.3) !important;
            color: var(--yt-red) !important;
            font-weight: 600 !important;
        }

        /* Fullscreen button */
        .video-js .vjs-fullscreen-control {
            order: 11 !important;
            margin-right: 0 !important;
            flex:none;
        }

        /* ===== CỘT PHẢI (SIDEBAR) ===== */
        .playlist-sidebar { 
            width: var(--sidebar-width); 
            background: #fff; 
            border-radius: var(--card-radius);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            position: sticky;
            top: calc(var(--header-height) + 20px);
            max-height: calc(100vh - var(--header-height) - 40px);
            display: flex;
            flex-direction: column;
        }

        .playlist-header { 
            padding: 20px; 
            border-bottom: 1px solid #f1f1f1; 
        }
        
        .playlist-body { 
            overflow-y: auto; 
            padding: 10px; 
        }

        .lesson-item { 
            display: flex; 
            align-items: center; 
            padding: 12px; 
            text-decoration: none; 
            color: #0f0f0f; 
            border-radius: 12px; 
            margin-bottom: 6px;
            transition: all 0.2s ease !important;
        }
        
        .lesson-item:hover { 
            background: #f2f2f2; 
            transform: translateX(2px);
        }
        
        .lesson-item.active { 
            background: #eff6ff; 
            color: var(--primary-color); 
            font-weight: 600; 
            border-left: 4px solid var(--primary-color);
            padding-left: 8px;
        }
        
        .lesson-thumbnail {
            width: 100px; 
            aspect-ratio: 16/9; 
            background: #e9ecef;
            border-radius: 8px; 
            margin-right: 12px; 
            flex-shrink: 0;
            display: flex; 
            align-items: center; 
            justify-content: center;
            overflow: hidden;
        }

        .playlist-body::-webkit-scrollbar { 
            width: 6px; 
        }
        
        .playlist-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .playlist-body::-webkit-scrollbar-thumb { 
            background: #cbd5e1; 
            border-radius: 3px;
        }
        
        .playlist-body::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        @media (max-width: 992px) {
            .main-container { flex-direction: column; }
            .playlist-sidebar { width: 100%; position: static; max-height: none; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-frontend">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold text-dark d-flex align-items-center" href="#">
            <i class="bi bi-play-btn-fill text-danger fs-3 me-2"></i> TAVANCHINH
        </a>
        <div class="d-flex align-items-center">
            <span class="me-3 d-none d-sm-inline small">Chào, <strong>Tạ Văn Chỉnh</strong></span>
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">T</div>
        </div>
    </div>
</nav>

<div class="main-container">
    <div class="video-section">
        <div class="video-player-container">
            <!-- ✅ QUAN TRỌNG: Xóa data-setup='{}' để tránh xung đột khởi tạo -->
            <video
                id="my-player"
                class="video-js vjs-theme-city"
                controls
                preload="auto"
            >
                <source src="/course/stream/123" type="video/mp4" />
                <p class="vjs-no-js">
                    Để xem video này, vui lòng bật JavaScript.
                </p>
            </video>
        </div>
        
        <div class="video-info">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h1 class="fw-bold h4 mb-0">Bài 1: Giới thiệu cấu trúc MVC trong PHP</h1>
                <span id="finish-badge" class="badge bg-secondary d-none">Đã hoàn thành</span>
            </div>
            <p class="text-muted small">1,234 lượt xem • 26/02/2026</p>
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
            <!-- ✅ JavaScript sẽ render các bài học vào đây -->
        </div>
    </div>
</div>

<script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>

<script>
    // ✅ FIX 1: Dùng DOMContentLoaded để đảm bảo tất cả DOM đã sẵn sàng
    document.addEventListener('DOMContentLoaded', function() {
        
        // ✅ Render playlist items (thay thế document.write)
        const playlistBody = document.getElementById('playlist-body');
        for(let i = 1; i <= 15; i++) {
            let activeClass = (i === 1) ? 'active' : '';
            let icon = (i < 1) ? 'bi-check-circle-fill text-success' : 'bi-play-circle';
            
            const lessonItem = document.createElement('a');
            lessonItem.href = '#';
            lessonItem.className = `lesson-item ${activeClass}`;
            lessonItem.innerHTML = `
                <div class="lesson-thumbnail"><i class="bi ${icon}"></i></div>
                <div class="small">Bài học số ${i}: Tiêu đề bài học ${i}</div>
            `;
            playlistBody.appendChild(lessonItem);
        }

        // ✅ Kiểm tra xem Video.js đã tải
        if (typeof videojs !== 'undefined') {
            const Button = videojs.getComponent('Button');

            // ✅ FIX 2: Class PrevButton - thiết lập controlText đúng cách
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
                    console.log("Quay lại bài trước");
                    // TODO: Thêm logic chuyển bài trước
                }
            }
            videojs.registerComponent('PrevButton', PrevButton);

            // ✅ FIX 3: Class NextButton - thiết lập controlText đúng cách
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
                    console.log("Chuyển bài tiếp theo");
                    // TODO: Thêm logic chuyển bài tiếp theo
                }
            }
            videojs.registerComponent('NextButton', NextButton);

            // ✅ FIX 4: Khởi tạo Player (chỉ một lần sau khi registerComponent)
            const player = videojs('my-player', {
                playbackRates: [0.5, 1, 1.5, 2],
                controlBar: {
                    currentTimeDisplay: true,
                    timeDivider: true,
                    durationDisplay: true,
                    remainingTimeDisplay: false,
                    children: [
                        'playToggle',
                        'PrevButton',
                        'NextButton',
                        'volumePanel',
                        'progressControl',
                        'spacer',
                        'currentTimeDisplay',
                        'timeDivider',
                        'durationDisplay',
                        'playbackRateMenuButton',
                        'fullscreenToggle',
                    ],
                },
            });
            
            // Optionally log khi player sẵn sàng
            player.ready(function() {
                console.log('✅ Video player đã khởi tạo thành công!');
            });
        } else {
            console.error('❌ Video.js chưa được tải!');
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>