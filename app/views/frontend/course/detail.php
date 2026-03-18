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
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body p-4">
                
                <div id="qr-result-area"></div>

                <div id="enrollForm">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold">Xác nhận đăng ký</h4>
                        <p class="text-muted small">Vui lòng nhập thông tin để nhận mã QR học tập</p>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Họ và tên</label>
                        <input type="text" id="enroll_name" class="form-control rounded-3" placeholder="Ví dụ: Nguyễn Văn An">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Số điện thoại Zalo</label>
                        <input type="tel" id="enroll_phone" class="form-control rounded-3" placeholder="Để kích hoạt khóa học">
                    </div>

                    <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3 mb-4">
                        <span class="small fw-bold">Tổng thanh toán:</span>
                        <span class="h4 mb-0 fw-bold text-danger">5.000.000đ</span>
                    </div>

                    <button type="button" onclick="handlePayOSPayment()" class="btn btn-primary btn-payment w-100 py-3 fw-bold rounded-pill shadow">
                        LẤY MÃ THANH TOÁN <i class="bi bi-qr-code-scan ms-2"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    async function handlePayOSPayment() {
        const name = document.getElementById('enroll_name').value;
        const phone = document.getElementById('enroll_phone').value;
        const btn = document.querySelector('.btn-payment');

        if (!phone) { alert("Vui lòng nhập số điện thoại!"); return; }

        btn.disabled = true;
        btn.innerHTML = 'Đang tạo mã QR...';

        try {
            const response = await fetch('/thanh-toan', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, phone })
            });

            const result = await response.json();

            if (result.qrCode) {
                // QUAN TRỌNG: Biến chuỗi text thành Link ảnh QR
                // Chúng ta dùng API của QuickChart hoặc QRServer (Miễn phí)
                const qrText = result.qrCode;
                const qrImageUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qrText)}`;
                const qrArea = document.getElementById('qr-result-area');
                if (qrArea) {
                    qrArea.innerHTML = `
                        <div class="text-center p-3 animate__animated animate__zoomIn">
                            <h5 class="fw-bold mb-3 text-primary">Quét mã để kích hoạt khóa học</h5>
                            <div class="bg-white p-3 d-inline-block rounded-4 shadow-sm mb-3 border">
                                <img src="${qrImageUrl}" class="img-fluid" style="width: 250px; height: 250px;" alt="QR Thanh Toan">
                            </div>
                            <div class="alert alert-warning py-2 small border-0 shadow-sm">
                                <strong>Nội dung:</strong> HOC CNC ${document.getElementById('enroll_phone').value}
                            </div>
                            <p class="text-muted mb-3" style="font-size: 0.8rem;">Mã đơn: #${result.orderCode}</p>
                            <button class="btn btn-outline-secondary btn-sm w-100 rounded-pill" onclick="location.reload()">Quay lại</button>
                        </div>
                    `;
                    document.getElementById('enrollForm').style.display = 'none';
                }
            } else {
                alert("Lỗi: " + result.error);
                btn.disabled = false;
                btn.innerHTML = 'THANH TOÁN AN TOÀN';
            }
        } catch (e) {
            //alert("Không thể kết nối máy chủ!");
            console.log("Lỗi chi tiết:", e); // Nhấn F12 trên trình duyệt để xem cái này
            //alert("Lỗi: " + e.message);
            btn.disabled = false;
            btn.disabled = false;
        }
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>