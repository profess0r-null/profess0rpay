<?php
if (!defined('Profess0rPay_INIT')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

$pdo = connectDatabase();
$stmt = $pdo->query("SELECT * FROM `{$db_prefix}update_logs` ORDER BY id DESC");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header d-print-none mb-3">
  <div class="row align-items-center">
    <div class="col">
      <h2 class="page-title"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-primary me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8l0 4l2 2" /><path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" /></svg> Update History</h2>
    </div>
    <div class="col-auto ms-auto d-print-none">
      <button class="btn btn-outline-secondary js-select" onclick="load_content('System Update','<?php echo $site_url.$path_admin ?>/system-settings/update','nav-item-system-settings')">Back to Updater</button>
    </div>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-vcenter card-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Version</th>
          <th>Status</th>
          <th>Log Details</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($logs)): ?>
          <tr><td colspan="4" class="text-center text-muted py-4">No updates found.</td></tr>
        <?php else: ?>
            <?php foreach($logs as $log): ?>
                <tr>
                    <td><?= date('d M Y, h:i A', strtotime($log['created_at'])) ?></td>
                    <td><span class="badge bg-primary-lt">v<?= htmlspecialchars($log['version']) ?></span></td>
                    <td>
                        <?php if($log['status'] === 'Success'): ?>
                            <span class="badge bg-success-lt">Success</span>
                        <?php else: ?>
                            <span class="badge bg-danger-lt">Failed</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted"><?= htmlspecialchars($log['log']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
