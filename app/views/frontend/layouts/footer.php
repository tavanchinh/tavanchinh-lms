<footer class="pt-5 pb-3 mt-5">
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold text-uppercase tracking-wider">
                    <span style="color: var(--accent-color);">CHINH</span>.EDU.VN
                </h5>
                <p class="mb-4">
                    Nền tảng đào tạo SketchUp, ABF và Aspire thực chiến. Đồng hành trọn đời cùng anh em học viên, giúp anh em làm chủ quy trình và yên tâm sản xuất.
                </p>
                <div class="social-icons">
                    <a href="https://www.facebook.com/tavanchinh.cnc/"><i class="bi bi-facebook"></i></a>
                    <a href="https://www.youtube.com/@tavanchinh"><i class="bi bi-youtube"></i></a>
                    <a href="https://m.me/tavanchinh.cnc"><i class="bi bi-messenger"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <h6 class="text-uppercase fw-bold">Khóa học</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="/tron-bo-quy-trinh-ra-file-cnc-voi-sketchup-abf-va-aspire">Trọn bộ quy trình ra file</a></li>
                    <li class="mb-2"><a href="/ky-nang-abf-va-aspire">Kỹ năng ABF & Aspire</a></li>
                    <li class="mb-2"><a href="/ky-nang-aspire">Kỹ năng Aspire độc lập</a></li>
                    <li class="mb-2"><a href="/dung-hinh-noi-that-co-ban-voi-sketchup">Dựng hình nội thất cơ bản</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-6">
                <h6 class="text-uppercase fw-bold">Thông tin liên hệ</h6>
                <ul class="list-unstyled">
                    <li class="d-flex mb-3">
                        <i class="bi bi-geo-alt-fill me-3"></i>
                        <span>SN 17 Đường Cần Kiệm, Thạch Thất, Hà Nội</span>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="bi bi-envelope-fill me-3"></i>
                        <span>chinh.tv91@gmail.com</span>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="bi bi-telephone-fill me-3"></i>
                        <span>0972 808 368</span>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="bi bi-clock-fill me-3"></i>
                        <span>08:00 - 22:00 (Thứ 2 - Thứ 7)</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div style="border-top: 1px solid var(--border-color);" class="pt-4 mt-4">
        <div class="container d-flex flex-wrap justify-content-between align-items-center">
            <p class="small mb-0">&copy; 2026 <strong>Tạ Văn Chinh</strong>. Toàn bộ nội dung đã được bảo hộ.</p>
            <div class="payment-icons d-none d-md-block">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" width="55" alt="PayPal">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" width="45" alt="Visa">
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" width="40" alt="MasterCard">
            </div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<?php if (isset($_SESSION['user_id'])): ?>
    <script>
        /**
         * KIỂM TRA ĐĂNG NHẬP ĐA THIẾT BỊ TRÊN TOÀN TRANG
         */
        setInterval(function() {
            // Chỉ chạy nếu người dùng đã đăng nhập (kiểm tra qua biến toàn cục hoặc session)
            fetch('/user/keep-alive')
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'warning_multi_device') {
                        // 1. Dừng video nếu đang ở trong trang bài học
                        if (window.player && typeof window.player.pause === 'function') {
                            window.player.pause();
                        }

                        // 2. Hiện thông báo cảnh báo toàn màn hình
                        showGlobalSecurityModal();
                    }
                })
                .catch(err => console.error("Security check error:", err));
        }, 60000);

        function showGlobalSecurityModal() {
            if (document.getElementById('global-security-overlay')) return;

            const overlay = document.createElement('div');
            overlay.id = 'global-security-overlay';
            overlay.style = `
                position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
                background: rgba(0, 0, 0, 0.9); color: white; display: flex;
                flex-direction: column; align-items: center; justify-content: center;
                z-index: 999999; padding: 20px; text-align: center; backdrop-filter: blur(10px);
            `;

            overlay.innerHTML = `
                <div style="max-width: 500px; background: #1a1a1a; padding: 40px; border-radius: 20px; border: 2px solid #ff4757; box-shadow: 0 0 30px rgba(255, 71, 87, 0.3);">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 5rem; color: #ff4757;"></i>
                    <h2 style="margin-top: 20px; font-weight: 800; color: #ff4757;">CẢNH BÁO TRUY CẬP</h2>
                    <p style="font-size: 1.1rem; margin: 20px 0; opacity: 0.9;">
                        Tài khoản của bạn vừa được đăng nhập từ một thiết bị khác.
                    </p>
                    <div style="background: rgba(255, 71, 87, 0.1); color: #ff4757; padding: 15px; border-radius: 10px; font-size: 0.9rem; text-align: left; margin-bottom: 30px;">
                        <strong>LƯU Ý:</strong> Chúng tôi nghiêm cấm chia sẻ tài khoản. Hệ thống sẽ <b>TỰ ĐỘNG KHÓA</b> nếu phát hiện vi phạm nhiều lần.
                    </div>
                    <div style="display: flex; gap: 15px; justify-content: center;">
                        <button onclick="location.reload()" style="background: white; color: black; border: none; padding: 12px 30px; border-radius: 50px; font-weight: bold; cursor: pointer;">TÔI ĐÃ HIỂU</button>
                        <a href="/dang-xuat" style="color: white; text-decoration: none; padding: 12px 30px; border: 1px solid white; border-radius: 50px; font-weight: bold;">Đăng xuất</a>
                    </div>
                </div>
            `;

            document.body.appendChild(overlay);
            document.body.style.overflow = 'hidden'; // Ngăn cuộn trang khi hiện cảnh báo
        }
    </script>
<?php endif; ?>