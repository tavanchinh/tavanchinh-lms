<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="text-primary mb-0"><i class="bi bi-cash-stack"></i> Ghi Nhận Giao Dịch Tài Chính</h3>
                    <a href="/admin/finance" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Quay lại danh sách
                    </a>
                </div>

                <form action="/admin/finance/store" method="POST">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Danh mục thu/chi <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select form-select-lg" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    
                                    <optgroup label="KHOẢN THU (INCOME)">
                                        <?php foreach ($incomeCategories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>

                                    <optgroup label="KHOẢN CHI (EXPENSE)">
                                        <?php foreach ($expenseCategories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Ghi chú chi tiết</label>
                                <textarea name="note" class="form-control" rows="5" placeholder="Ví dụ: Tiền mua linh kiện CNC, Học phí anh Chinh nộp cọc..."></textarea>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="card bg-light border-0 p-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Số tiền (VNĐ) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg">
                                        <input type="text" id="amount_display" class="form-control fw-bold text-primary" placeholder="0" required>
                                        <span class="input-group-text">đ</span>
                                    </div>
                                    <input type="hidden" name="amount" id="amount_actual">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ngày thực hiện</label>
                                    <input type="date" name="transaction_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Hình thức thanh toán</label>
                                    <div class="d-flex gap-2">
                                        <input type="radio" class="btn-check" name="payment_method" id="pay_transfer" value="transfer" checked>
                                        <label class="btn btn-outline-primary flex-fill" for="pay_transfer">Chuyển khoản</label>

                                        <input type="radio" class="btn-check" name="payment_method" id="pay_cash" value="cash">
                                        <label class="btn btn-outline-primary flex-fill" for="pay_cash">Tiền mặt</label>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary w-100 mb-2 py-2 fw-bold">
                                        <i class="bi bi-check-circle"></i> XÁC NHẬN LƯU
                                    </button>
                                    <button type="reset" class="btn btn-link w-100 text-secondary text-decoration-none">
                                        Nhập lại từ đầu
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mt-3 p-2 border-start border-warning border-4 bg-warning bg-opacity-10">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i> <b>Lưu ý:</b> Giao dịch sau khi lưu sẽ được tính trực tiếp vào báo cáo doanh thu và lợi nhuận của tháng.
                                </small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script>
document.getElementById('amount_display').addEventListener('input', function (e) {
    // 1. Lấy giá trị nhập vào, loại bỏ tất cả ký tự không phải số
    let value = e.target.value.replace(/\D/g, "");
    
    // 2. Lưu giá trị số thuần túy vào input ẩn để gửi lên Server
    document.getElementById('amount_actual').value = value;
    
    // 3. Định dạng lại số có dấu chấm phân cách hàng nghìn
    if (value !== "") {
        e.target.value = new Intl.NumberFormat('de-DE').format(value);
    } else {
        e.target.value = "";
    }
});
</script>