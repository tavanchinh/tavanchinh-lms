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
                        
                        <div class="accordion accordion-flush border overflow-hidden" style="border-radius: 13px;" id="accordionCourse">
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
                                                                    <a href="/learning/<?= $course['slug'] ?>?id=<?= $lesson['id'] ?>" class="badge rounded-pill text-decoration-none bg-success-subtle text-success border border-success-subtle">Học thử</a>
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
                            <button class="btn btn-primary btn-lg w-100 rounded-pill mb-2 fw-bold shadow"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#enrollModal">
                                ĐĂNG KÝ HỌC NGAY
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

<div class="modal fade" id="enrollModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body p-4">
                
                <div id="qr-result-area"></div>

                <div id="enrollForm">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold mb-1">Xác nhận đăng ký</h4>
                        <p class="text-muted small">Vui lòng kiểm tra thông tin để nhận mã QR học tập</p>
                    </div>

                    <div class="p-3 mb-3 border rounded-3 bg-light-subtle shadow-sm">
                        <div class="small text-muted mb-1">Khóa học đăng ký:</div>
                        <div class="fw-bold text-dark mb-2" id="display_course_name">
                            <?= $course['name'] ?? 'Khóa học thực chiến' ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-2">
                            <span class="small fw-bold text-muted">Tổng thanh toán:</span>
                            <span class="h5 mb-0 fw-bold text-danger">
                                <?= number_format($course['price'] ?? 5000000, 0, ',', '.') ?>đ
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1"><i class="bi bi-person me-1"></i>Họ và tên</label>
                        <input type="text" id="enroll_name" class="form-control rounded-3 py-2" 
                               value="<?= $_SESSION['user_name'] ?? '' ?>" 
                               placeholder="Ví dụ: Tạ Văn Chinh">
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1"><i class="bi bi-whatsapp me-1"></i>Số điện thoại Zalo</label>
                        <input type="tel" id="enroll_phone" class="form-control rounded-3 py-2" 
                               value="<?= $_SESSION['phone_number'] ?? '' ?>" 
                               placeholder="Nhập SĐT để kích hoạt khóa học">
                    </div>

                    <?php if (!isset($_SESSION['user_name'])): ?>
                    <div class="form-check form-switch mb-3 border p-2 ps-5 rounded-3 bg-light shadow-sm border-primary-subtle">
                        <input class="form-check-input" type="checkbox" role="switch" id="reg_switch" onchange="toggleRegisterFields()">
                        <label class="form-check-label small fw-bold text-primary" for="reg_switch" style="cursor:pointer">
                            <i class="bi bi-person-plus-fill me-1"></i> Tôi chưa có tài khoản, đăng ký mới
                        </label>
                    </div>

                    <div id="register_fields" style="display: none;" class="p-3 border rounded-3 mb-3 bg-white shadow-sm animate__animated animate__fadeInDown">
                        <div class="mb-2">
                            <label class="small fw-bold text-muted">Email đăng nhập</label>
                            <input type="email" id="reg_email" class="form-control form-control-sm" placeholder="email@gmail.com">
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="small fw-bold text-muted">Mật khẩu</label>
                                <input type="password" id="reg_pass" class="form-control form-control-sm" placeholder="******">
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted">Xác nhận</label>
                                <input type="password" id="reg_confirm" class="form-control form-control-sm" placeholder="******">
                            </div>
                        </div>
                        <div class="mt-2 text-center" style="font-size: 0.7rem; color: #ef4444;">
                            <i class="bi bi-info-circle me-1"></i>Tài khoản sẽ được kích hoạt sau khi thanh toán thành công.
                        </div>
                    </div>
                    <?php endif; ?>

                    <input type="hidden" id="course_id" value="<?= $course['id'] ?? '' ?>">
                    <input type="hidden" id="course_price" value="<?= $course['price'] ?? 5000000 ?>">

                    <button type="button" id="btn-submit-payment" onclick="handlePayOSPayment()" class="btn btn-primary btn-payment w-100 py-3 fw-bold rounded-pill shadow-sm transition">
                        LẤY MÃ THANH TOÁN <i class="bi bi-qr-code-scan ms-2"></i>
                    </button>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-shield-lock me-1"></i> Thanh toán an toàn qua cổng PayOS
                        </small>
                    </div>
                </div> </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    /**
     * Hàm đóng/mở vùng nhập liệu đăng ký tài khoản mới
     */
    function toggleRegisterFields() {
        const fields = document.getElementById('register_fields');
        const regSwitch = document.getElementById('reg_switch');
        if (fields && regSwitch) {
            fields.style.display = regSwitch.checked ? 'block' : 'none';
            if (regSwitch.checked) {
                fields.classList.add('animate__fadeInDown');
            }
        }
    }

    /**
     * Hàm xử lý thanh toán chính
     */
    async function handlePayOSPayment() {
        // 1. Lấy thông tin cơ bản
        const name = document.getElementById('enroll_name').value.trim();
        const phone = document.getElementById('enroll_phone').value.trim();
        const courseId = document.getElementById('course_id').value;
        const rawPrice = document.getElementById('course_price').value;
        
        // Ép kiểu số tiền về số nguyên (bỏ .00)
        const amount = Math.round(parseFloat(rawPrice));

        // 2. Kiểm tra dữ liệu đăng ký tài khoản (nếu có)
        const regSwitch = document.getElementById('reg_switch');
        const isRegister = regSwitch ? regSwitch.checked : false;
        let registerData = {};

        if (isRegister) {
            const email = document.getElementById('reg_email').value.trim();
            const pass = document.getElementById('reg_pass').value;
            const confirm = document.getElementById('reg_confirm').value;

            if (!email || !pass) {
                Swal.fire('Thông báo', 'Vui lòng nhập Email và Mật khẩu để tạo tài khoản!', 'warning');
                return;
            }
            if (pass !== confirm) {
                Swal.fire('Lỗi mật khẩu', 'Mật khẩu xác nhận không khớp, bạn vui lòng kiểm tra lại!', 'error');
                return;
            }
            registerData = { email: email, pass: pass };
        }

        // 3. Validate thông tin bắt buộc
        if (!name || !phone) {
            Swal.fire('Thông tin trống', 'Vui lòng nhập đầy đủ Họ tên và Số điện thoại!', 'info');
            return;
        }

        // 4. Hiệu ứng nút bấm khi đang xử lý
        const btn = document.getElementById('btn-submit-payment');
        const originalBtnText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang tạo mã QR...';

        try {
            // 5. Gửi AJAX đến route /thanh-toan
            const response = await fetch('/thanh-toan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: name,
                    phone: phone,
                    amount: amount,
                    courseId: courseId,
                    isRegister: isRegister,
                    ...registerData
                })
            });

            const result = await response.json();

            // 6. Xử lý kết quả trả về
            if (result.qrCode) {
                // Tạo link ảnh từ chuỗi VietQR (Sử dụng QuickChart ổn định)
                const qrImageUrl = `https://quickchart.io/qr?text=${encodeURIComponent(result.qrCode)}&size=300&margin=2`;

                // Thay thế nội dung Form bằng vùng hiển thị QR
                document.getElementById('enrollForm').style.display = 'none';
                const qrArea = document.getElementById('qr-result-area');
                qrArea.innerHTML = `
                    <div class="text-center animate__animated animate__fadeIn">
                        <div class="mb-3">
                            <i class="bi bi-check2-circle text-success" style="font-size: 3rem;"></i>
                            <h5 class="fw-bold mt-2">Mã QR Thanh Toán</h5>
                            <p class="text-muted small">Bạn quét mã dưới đây bằng ứng dụng Ngân hàng/MoMo</p>
                        </div>

                        <div class="p-2 d-inline-block bg-white rounded-3 shadow-sm border mb-3">
                            <img src="${qrImageUrl}" class="img-fluid" style="width: 250px; height: 250px;" alt="QR Code">
                        </div>

                        <div class="alert alert-light border small text-start mb-3 mx-auto" style="max-width: 320px;">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Số tiền:</span>
                                <span class="fw-bold text-danger">${amount.toLocaleString('vi-VN')}đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Nội dung:</span>
                                <span class="fw-bold text-primary">HOC CNC ${phone}</span>
                            </div>
                            <div class="text-center mt-2 pt-2 border-top">
                                <span class="badge bg-warning text-dark">Chờ thanh toán...</span>
                            </div>
                        </div>

                        <button class="btn btn-outline-secondary btn-sm w-100 rounded-pill" onclick="location.reload()">
                            <i class="bi bi-arrow-left me-1"></i> Quay lại
                        </button>
                    </div>
                `;
                
                
                // Kích hoạt bộ đếm kiểm tra tiền về
                startCheckingStatus(result.orderCode);

            } else if (result.error || result.message) {
                //alert("Lỗi: " + (result.error || result.message));
                // HIỂN THỊ LỖI TỪ SERVER (Ví dụ: Email đã tồn tại)
                Swal.fire({
                    icon: 'error',
                    title: 'Không thể đăng ký',
                    text: result.error, // Lỗi "Email này đã được đăng ký..." sẽ hiện ở đây
                    confirmButtonColor: '#3085d6'
                });
                resetButton(btn, originalBtnText);
            }

        } catch (error) {
            console.error("PayOS Error:", error);
            alert("Không thể kết nối đến máy chủ thanh toán. Bạn vui lòng thử lại sau!");
            resetButton(btn, originalBtnText);
        }
    }

    function resetButton(btn, text) {
        btn.disabled = false;
        btn.innerHTML = text;
    }

    // Hàm tự động kiểm tra trạng thái đơn hàng mỗi 3 giây
    function startCheckingStatus(orderCode) {
        const timer = setInterval(async () => {
            try {
                // Gọi đến route checkStatus trong PaymentController
                const response = await fetch(`/kiem-tra-trang-thai-don-hang?orderCode=${orderCode}`);
                const result = await response.json();

                if (result.status === 'completed') {
                    clearInterval(timer); // Dừng việc kiểm tra lại
                    
                    // Hiển thị thông báo thành công
                    const qrArea = document.getElementById('qr-result-area');
                    qrArea.innerHTML = `
                        <div class="text-center p-4 animate__animated animate__bounceIn">
                            <div class="mb-3">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="fw-bold">Thanh toán thành công!</h4>
                            <p class="text-muted">Hệ thống đang mở khóa học cho bạn...</p>
                            <div class="spinner-border text-primary mt-2" role="status"></div>
                        </div>
                    `;

                    // Tự động load lại trang sau 2 giây để khách vào học luôn
                    setTimeout(() => {
                        location.reload();
                    }, 2500);
                }
            } catch (error) {
                console.error("Đang chờ thanh toán...");
            }
        }, 3000); // 3000ms = 3 giây
    }
</script>

<style>
    /* Hiệu ứng mượt cho các ô input */
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
    }
    .btn-payment {
        transition: all 0.3s ease;
        background: linear-gradient(45deg, #0d6efd, #0b5ed7);
        border: none;
    }
    .btn-payment:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
    }
    /* Tối ưu cho mobile */
    @media (max-width: 576px) {
        .modal-dialog { margin: 10px; }
        .modal-body { padding: 1.5rem !important; }
    }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>