<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="container mb-5" id="software-library">
    <h3 class="fw-bold mb-4">
        <i class="bi bi-box-seam me-2 text-primary"></i>Kho Phần mềm & Plugin
    </h3>

    <?php
    $softwares = [
        [
            "title"    => "RT Tool Plugin",
            "desc"     => "Công cụ xoay vân gỗ, xoay đối tượng nhanh chóng",
            "image"    => "/uploads/default-image.jpg",
            "version"  => "v1.1.0",
            "link"     => "URL_FILE_CUA_BAN_1",
            "ga_label" => "RT-Plugin"
        ],
        [
            "title"    => "FitC Plugin",
            "desc"     => "Tối ưu quy trình xuất file với ABF",
            "image"    => "/uploads/default-image.jpg",
            "version"  => "v1.0.8",
            "link"     => "https://fitc.vn/",
            "ga_label" => "FitC-Plugin"
        ],

        [
            "title"    => "SketchUp 2022",
            "desc"     => "Phần mêm vẽ 3D chuyên nghiệp, dễ học, dễ sử dụng",
            "image"    => "/uploads/sketchup-2022-7.jpg",
            "version"  => "2022",
            "link"     => "https://drive.google.com/file/d/1pjxERZMpZzutBVaP7FE19Q59uCX6gXGv/view?usp=sharing",
            "ga_label" => "SketchUp 2022"
        ],

        [
            "title"    => "SketchUp 2023",
            "desc"     => "Phần mêm vẽ 3D chuyên nghiệp, dễ học, dễ sử dụng",
            "image"    => "/uploads/su23.png",
            "version"  => "2023",
            "link"     => "https://drive.google.com/file/d/1XhEZgYbOCo2yiBJR-OIjz30_tcZ2rwSD/view?usp=sharing",
            "ga_label" => "SketchUp 2023"
        ],
        [
            "title"    => "Aspire Tiếng Việt",
            "desc"     => "Phần mêm setup dao cho máy CNC, hỗ trợ tiếng Việt, dễ sử dụng",
            "image"    => "/uploads/Aspire.jpg",
            "version"  => "9.5.14",
            "link"     => "https://drive.google.com/file/d/15lXvoJhcGmYIk-1yasiKDacmuHetvqcP/view?usp=sharing",
            "ga_label" => "Aspire Tiếng Việt"
        ],

        [
            "title"    => "Cimco Edit",
            "desc"     => "Phần mêm mô phỏng đường chạy dao",
            "image"    => "/uploads/cimco8.png",
            "version"  => "V8",
            "link"     => "https://drive.google.com/file/d/1338OEMY7sXhtVMEu5Nq4LH9-HdOpqknn/view?usp=sharing",
            "ga_label" => "Cimco Edit"
        ],

        [
            "title"    => "Cordel Draw 2022",
            "desc"     => "Phần mêm vẽ 2D chuyên nghiệp, hỗ trợ tốt cho thiết kế CNC",
            "image"    => "/uploads/coreldraw-2022.jpg",
            "version"  => "V22",
            "link"     => "https://drive.google.com/file/d/1fEdH8DKojAtZIIBC-ROI0s8g9j5sVe39/view",
            "ga_label" => "Cordel Draw 2022"
        ],
        [
            "title"    => "Vray 6 for SketchUp",
            "desc"     => "Phần mêm render hình ảnh chất lượng cao, hỗ trợ SketchUp 2022 và 2023",
            "image"    => "/uploads/v-ray-6-cho-sketchup-3.jpg",
            "version"  => "Vray 6",
            "link"     => "https://drive.google.com/file/d/1A4__PYmb72BHUOhS-1if2coeKe8DJkW7/view?usp=sharing",
            "ga_label" => "Vray 6 for SketchUp"
        ],
        [
            "title"    => "Driver máy in Xprinter",
            "desc"     => "Hỗ trợ in tem nhãn, mã vạch cho quản lý kho và sản phẩm",
            "image"    => "/uploads/1678955179thwHVflkXx.jpeg",
            "version"  => "#",
            "link"     => "https://drive.google.com/file/d/1A4__PYmb72BHUOhS-1if2coeKe8DJkW7/view?usp=sharing",
            "ga_label" => "Driver máy in Xprinter"
        ],
        // Bạn có thể thêm phần mềm thứ 3, 4 vào đây dễ dàng...
    ];
    ?>
    
    <div class="row g-4">
    <?php foreach ($softwares as $item): ?>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm course-card">
                <div class="text-decoration-none">
                    <img src="<?= $item['image'] ?>" class="card-img-top" alt="<?= $item['title'] ?>" style="height: 160px; object-fit: cover;">
                    <div class="card-body pb-0">
                        <h6 class="fw-bold mb-2 text-dark"><?= $item['title'] ?></h6>
                        <p class="text-muted small"><?= $item['desc'] ?></p>
                    </div>
                </div>

                <div class="card-body pt-0 mt-auto">
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="text-primary small"><?= $item['version'] ?></span>
                        <a href="<?= $item['link'] ?>" 
                        onclick="gtag('event', 'download', { 'event_category': 'Software', 'event_label': '<?= $item['ga_label'] ?>' });"
                        class="btn btn-sm btn-success rounded-pill px-3" 
                        target="_blank">
                        <i class="bi bi-download me-1"></i>Tải về
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    /* Đảm bảo màu nút Tải về đồng bộ với nhận diện thương hiệu của bạn */
    .btn-success {
        background-color: #78b328 ! exaggeration;
        border-color: #78b328 !important;
    }
    .btn-success:hover {
        background-color: #629420 !important;
    }
    .course-card {
        transition: transform 0.2s;
    }
    .course-card:hover {
        transform: translateY(-5px);
    }
</style>
<?php include __DIR__ . '/../layouts/footer.php'; ?>