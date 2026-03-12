<footer class="pt-5 pb-3 mt-5">
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold text-uppercase tracking-wider">
                    <span style="color: var(--accent-color);">TAVANCHINH</span>.EDU
                </h5>
                <p class="mb-4">
                    Nền tảng đào tạo SketchUp, ABF và Aspire thực chiến. Chúng tôi giúp bạn rút ngắn thời gian thiết kế và làm chủ quy trình sản xuất CNC chuyên nghiệp.
                </p>
                <div class="social-icons">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-youtube"></i></a>
                    <a href="#"><i class="bi bi-messenger"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <h6 class="text-uppercase fw-bold">Khóa học</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#">SketchUp Nội thất</a></li>
                    <li class="mb-2"><a href="#">ABF Cutting Pro</a></li>
                    <li class="mb-2"><a href="#">Aspire CNC 2D/3D</a></li>
                    <li class="mb-2"><a href="#">V-Ray cho Kiến trúc</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6">
                <h6 class="text-uppercase fw-bold">Liên kết nhanh</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#">Về chúng tôi</a></li>
                    <li class="mb-2"><a href="#">Hướng dẫn mua hàng</a></li>
                    <li class="mb-2"><a href="#">Chính sách bảo mật</a></li>
                    <li class="mb-2"><a href="#">Hỏi đáp (FAQ)</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-6">
                <h6 class="text-uppercase fw-bold">Thông tin liên hệ</h6>
                <ul class="list-unstyled">
                    <li class="d-flex mb-3">
                        <i class="bi bi-geo-alt-fill me-3"></i>
                        <span>Thạch Thất, Hà Nội, Việt Nam</span>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="bi bi-envelope-fill me-3"></i>
                        <span>contact@tavanchinh.com</span>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="bi bi-telephone-fill me-3"></i>
                        <span>+84 123 456 789</span>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="bi bi-clock-fill me-3"></i>
                        <span>08:00 - 21:00 (Thứ 2 - Thứ 7)</span>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/js/bootstrap.bundle.min.js"></script>
</footer>
<style>
    :root {
        --footer-bg: #0f172a;       /* Xanh đen sâu cực sang */
        --footer-text: #94a3b8;     /* Xám xanh nhạt cho chữ */
        --footer-heading: #f8fafc;  /* Trắng tinh cho tiêu đề */
        --accent-color: #38bdf8;    /* Xanh Sky nhẹ nhàng, không bị chói */
        --border-color: #1e293b;    /* Màu đường kẻ ngăn cách */
    }

    footer {
        background-color: var(--footer-bg) !important;
        color: var(--footer-text);
        font-size: 0.95rem;
        line-height: 1.6;
    }

    footer h5, footer h6 {
        color: var(--footer-heading);
        margin-bottom: 1.5rem;
    }

    /* Hiệu ứng liên kết */
    footer a {
        color: var(--footer-text) !important;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    footer a:hover {
        color: var(--accent-color) !important;
        padding-left: 4px; /* Nhích nhẹ sang phải khi hover */
    }

    /* Icon liên hệ */
    footer .bi {
        color: var(--accent-color);
        font-size: 1.1rem;
    }

    /* Icon mạng xã hội */
    .social-icons a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        margin-right: 10px;
    }

    .social-icons a:hover {
        background: var(--accent-color);
        color: #fff !important;
        padding-left: 0; /* Reset hover padding cho icon */
    }

    /* Logo thanh toán */
    .payment-icons img {
        filter: grayscale(1) brightness(1.5);
        opacity: 0.5;
        transition: 0.3s;
        margin-left: 15px;
    }

    .payment-icons img:hover {
        filter: grayscale(0) brightness(1);
        opacity: 1;
    }
</style>