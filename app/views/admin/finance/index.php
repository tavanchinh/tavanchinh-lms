<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark"><i class="bi bi-pie-chart-fill text-primary"></i> Báo Cáo Tài Chính</h3>
        <a href="/admin/finance/create" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg"></i> Thêm giao dịch
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="/admin/finance" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Từ ngày</label>
                    <input type="date" name="from_date" class="form-control form-control-sm" value="<?= $fromDate ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Đến ngày</label>
                    <input type="date" name="to_date" class="form-control form-control-sm" value="<?= $toDate ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Loại giao dịch</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="all" <?= $currentType == 'all' ? 'selected' : '' ?>>Tất cả thu chi</option>
                        <option value="income" <?= $currentType == 'income' ? 'selected' : '' ?>>Chỉ khoản thu (Income)</option>
                        <option value="expense" <?= $currentType == 'expense' ? 'selected' : '' ?>>Chỉ khoản chi (Expense)</option>
                    </select>
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-dark btn-sm">
                        <i class="bi bi-filter"></i> Lọc dữ liệu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white border-start border-success border-4">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase">Doanh Thu</h6>
                    <h3 class="fw-bold text-success mb-0"><?= number_format($totalIncome) ?> <small>đ</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white border-start border-danger border-4">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase">Chi Phí</h6>
                    <h3 class="fw-bold text-danger mb-0"><?= number_format($totalExpense) ?> <small>đ</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white border-start border-primary border-4">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase">Lợi Nhuận</h6>
                    <h3 class="fw-bold text-primary mb-0"><?= number_format($profit) ?> <small>đ</small></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Ngày</th>
                        <th>Loại danh mục</th>
                        <th>Ghi chú</th>
                        <th class="text-end pe-4">Số tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">Không có dữ liệu trong khoảng thời gian này</td></tr>
                    <?php endif; ?>
                    
                    <?php foreach ($transactions as $item): ?>
                    <tr>
                        <td class="ps-4">
                            <span class="text-dark fw-medium"><?= date('d/m/Y', strtotime($item['transaction_date'])) ?></span>
                        </td>
                        <td>
                            <span class="badge rounded-pill <?= $item['type'] == 'income' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>">
                                <?= $item['category_name'] ?>
                            </span>
                        </td>
                        <td><small class="text-secondary"><?= $item['note'] ?></small></td>
                        <td class="text-end pe-4 fw-bold <?= $item['type'] == 'income' ? 'text-success' : 'text-danger' ?>">
                            <?= ($item['type'] == 'income' ? '+' : '-') . number_format($item['amount']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>