<?php require_once APPROOT . '/views/manager/includes/header.php'; ?>

<!-- Flash Messages -->
<?php if(isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>
                <i class="fas fa-envelope text-warning me-2"></i>
                Quản Lý Liên Hệ
            </h1>
            <p class="page-description">Xem và trả lời các tin nhắn từ khách hàng</p>
        </div>
        <div>
            <a href="<?php echo URLROOT; ?>/manager" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Về Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Stats Row -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-left-info">
            <div class="card-body text-center">
                <i class="fas fa-envelope fa-2x text-info mb-2"></i>
                <div class="h4 mb-0">
                    <?php echo count($data['contacts'] ?? []); ?>
                </div>
                <div class="small text-muted">Tổng tin nhắn</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-left-danger">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-circle fa-2x text-danger mb-2"></i>
                <div class="h4 mb-0">
                    <?php 
                    $newCount = 0;
                    if(isset($data['contacts'])) {
                        foreach($data['contacts'] as $contact) {
                            if($contact->status == 'new') $newCount++;
                        }
                    }
                    echo $newCount;
                    ?>
                </div>
                <div class="small text-muted">Chưa đọc</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-left-success">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <div class="h4 mb-0">
                    <?php 
                    $readCount = 0;
                    if(isset($data['contacts'])) {
                        foreach($data['contacts'] as $contact) {
                            if($contact->status == 'read') $readCount++;
                        }
                    }
                    echo $readCount;
                    ?>
                </div>
                <div class="small text-muted">Đã đọc</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-left-warning">
            <div class="card-body text-center">
                <i class="fas fa-reply fa-2x text-warning mb-2"></i>
                <div class="h4 mb-0">0</div>
                <div class="small text-muted">Đã trả lời</div>
            </div>
        </div>
    </div>
</div>

<!-- Contacts List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Danh Sách Liên Hệ
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if(isset($data['contacts']) && !empty($data['contacts'])): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%">
                                        <i class="fas fa-circle"></i>
                                    </th>
                                    <th width="20%">Người gửi</th>
                                    <th width="25%">Tiêu đề</th>
                                    <th width="30%">Nội dung</th>
                                    <th width="15%">Thời gian</th>
                                    <th width="5%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['contacts'] as $contact): ?>
                                    <tr class="<?php echo $contact->status == 'new' ? 'table-light' : ''; ?>">
                                        <td class="text-center">
                                            <i class="fas fa-circle text-<?php echo $contact->status == 'new' ? 'danger' : 'success'; ?>" 
                                               style="font-size: 8px;" title="<?php echo $contact->status == 'new' ? 'Chưa đọc' : 'Đã đọc'; ?>"></i>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($contact->name); ?></div>
                                            <div class="text-muted small"><?php echo htmlspecialchars($contact->email); ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($contact->subject); ?></div>
                                            <span class="badge bg-<?php echo $contact->status == 'new' ? 'danger' : 'success'; ?> badge-sm">
                                                <?php echo $contact->status == 'new' ? 'Mới' : 'Đã đọc'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;">
                                                <?php echo htmlspecialchars($contact->message); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo date('d/m/Y', strtotime($contact->created_at)); ?>
                                            </div>
                                            <div class="small text-muted">
                                                <?php echo date('H:i', strtotime($contact->created_at)); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                        data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <button class="dropdown-item" 
                                                                onclick="showContactDetail(<?php echo htmlspecialchars(json_encode($contact)); ?>)">
                                                            <i class="fas fa-eye me-2"></i> Xem chi tiết
                                                        </button>
                                                    </li>
                                                    <?php if($contact->status == 'new'): ?>
                                                        <li>
                                                            <a class="dropdown-item" 
                                                               href="<?php echo URLROOT; ?>/manager/markContactRead/<?php echo $contact->id; ?>">
                                                                <i class="fas fa-check me-2"></i> Đánh dấu đã đọc
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-primary" 
                                                           href="mailto:<?php echo $contact->email; ?>?subject=Re: <?php echo urlencode($contact->subject); ?>">
                                                            <i class="fas fa-reply me-2"></i> Trả lời qua email
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Chưa có liên hệ nào</h4>
                        <p class="text-muted">Các tin nhắn liên hệ từ khách hàng sẽ hiển thị tại đây.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Contact Detail Modal -->
<div class="modal fade" id="contactDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-envelope me-2"></i>
                    Chi Tiết Liên Hệ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold text-muted">Người gửi:</label>
                        <div id="modal-name"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-muted">Email:</label>
                        <div id="modal-email"></div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold text-muted">Tiêu đề:</label>
                        <div id="modal-subject"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-muted">Thời gian:</label>
                        <div id="modal-created"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="fw-bold text-muted">Nội dung:</label>
                    <div class="bg-light p-3 rounded" id="modal-message"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="modal-reply-btn">
                    <i class="fas fa-reply me-1"></i> Trả lời
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showContactDetail(contact) {
    // Populate modal with contact data
    document.getElementById('modal-name').textContent = contact.name;
    document.getElementById('modal-email').innerHTML = '<a href="mailto:' + contact.email + '">' + contact.email + '</a>';
    document.getElementById('modal-subject').textContent = contact.subject;
    document.getElementById('modal-created').textContent = new Date(contact.created_at).toLocaleString('vi-VN');
    document.getElementById('modal-message').textContent = contact.message;
    
    // Set reply button action
    document.getElementById('modal-reply-btn').onclick = function() {
        window.open('mailto:' + contact.email + '?subject=Re: ' + encodeURIComponent(contact.subject));
    };
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('contactDetailModal'));
    modal.show();
}
</script>

<style>
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.badge-sm {
    font-size: 0.7em;
}

tr.table-light {
    font-weight: 500;
}
</style>

<?php require_once APPROOT . '/views/manager/includes/footer.php'; ?> 