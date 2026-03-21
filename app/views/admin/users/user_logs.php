<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary">
        <h6 class="m-0 font-weight-bold text-white">Lịch sử truy cập chi tiết</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr class="text-dark">
                        <th width="15%">Thời gian</th>
                        <th width="10%">Sự kiện</th>
                        <th width="15%">Địa chỉ IP</th>
                        <th width="20%">Thiết bị</th>
                        <th width="40%">Thông tin gốc (User Agent)</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><span class="text-primary font-weight-bold"><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></span></td>
                        <td>
                            <span class="text text-<?= $log['event_type'] == 'login' ? 'success' : 'info' ?> px-3">
                                <?= strtoupper($log['event_type']) ?>
                            </span>
                        </td>
                        <td><code class="text-danger font-weight-bold" style="font-size: 1.1em;"><?= $log['ip_address'] ?></code></td>
                        <td>
                            <b class="text-success">
                                <?= DeviceHelper::parseUserAgent($log['user_agent']) ?>
                            </b>
                        </td>
                        <td>
                            <div class="text-muted" style="font-size: 0.85rem; line-height: 1.2; max-height: 40px; overflow-y: auto;">
                                <?= $log['user_agent'] ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>