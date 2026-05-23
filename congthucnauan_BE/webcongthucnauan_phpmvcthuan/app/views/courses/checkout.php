<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-shopping-cart"></i> Thanh toán khóa học</h4>
                </div>
                <div class="card-body">
                    <h5 class="mb-4">Chọn phương thức thanh toán</h5>
                    
                    <form action="<?php echo URL_ROOT; ?>/courses/checkout/<?php echo $data['course']->id; ?>" method="POST">
                        <div class="payment-methods">
                            <div class="form-check payment-method-item mb-3 p-3 border rounded">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_method_stripe" value="stripe" required checked>
                                <label class="form-check-label d-flex align-items-center" for="payment_method_stripe">
                                    <i class="fab fa-cc-stripe me-3 fs-3" style="color: #635BFF;"></i>
                                    <div class="flex-grow-1">
                                        <strong>Stripe - Thẻ Visa/Mastercard</strong>
                                        <div class="text-muted small">✨ Khuyến nghị - Test dễ dàng với thẻ ảo</div>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="form-check payment-method-item mb-3 p-3 border rounded">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_method_momo" value="momo" required>
                                <label class="form-check-label d-flex align-items-center" for="payment_method_momo">
                                    <i class="fas fa-wallet me-3 fs-3 text-danger"></i>
                                    <div class="flex-grow-1">
                                        <strong>Ví điện tử MoMo</strong>
                                        <div class="text-muted small">Thanh toán qua ví MoMo - Nhanh chóng & Bảo mật</div>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="form-check payment-method-item mb-3 p-3 border rounded">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_method_vnpay" value="vnpay" required>
                                <label class="form-check-label d-flex align-items-center" for="payment_method_vnpay">
                                    <i class="fas fa-credit-card me-3 fs-3 text-primary"></i>
                                    <div class="flex-grow-1">
                                        <strong>VNPay</strong>
                                        <div class="text-muted small">Thanh toán qua thẻ ATM, Visa, MasterCard, QR Code</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <div id="stripe-info" class="alert alert-success mt-3">
                            <h6 class="alert-heading"><i class="fab fa-cc-stripe"></i> Thanh toán qua Stripe</h6>
                            <p class="mb-2">🎉 <strong>Test dễ dàng với thẻ ảo!</strong></p>
                            <div class="card bg-white border-success">
                                <div class="card-body">
                                    <strong>✅ Thẻ test - Thanh toán thành công:</strong><br>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            Số thẻ: <code class="text-success">4242 4242 4242 4242</code><br>
                                            Ngày: <code>12/34</code> | CVC: <code>123</code>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                📝 Tên: Bất kỳ<br>
                                                🌍 ZIP: Bất kỳ
                                            </small>
                                        </div>
                                    </div>
                                    <hr>
                                    <strong>🔴 Test thẻ bị từ chối:</strong><br>
                                    Số thẻ: <code class="text-danger">4000 0000 0000 0002</code>
                                </div>
                            </div>
                        </div>
                        
                        <div id="momo-info" class="alert alert-info d-none mt-3">
                            <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Thanh toán qua MoMo</h6>
                            <p class="mb-2">Bạn sẽ được chuyển đến trang thanh toán của MoMo để hoàn tất giao dịch.</p>
                            <ul class="mb-0">
                                <li>Quét mã QR hoặc đăng nhập ví MoMo</li>
                                <li>Xác nhận thanh toán</li>
                                <li>Tự động đăng ký khóa học sau khi hoàn tất</li>
                            </ul>
                        </div>
                        
                        <div id="vnpay-info" class="alert alert-info d-none mt-3">
                            <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Thanh toán qua VNPay</h6>
                            <p class="mb-2">Bạn sẽ được chuyển đến cổng thanh toán VNPay để hoàn tất giao dịch.</p>
                            <ul class="mb-0">
                                <li>Hỗ trợ thẻ ATM nội địa (Internet Banking)</li>
                                <li>Hỗ trợ thẻ Visa, MasterCard, JCB</li>
                                <li>Thanh toán qua QR Code VNPAY-QR</li>
                                <li>Tự động đăng ký khóa học sau khi hoàn tất</li>
                            </ul>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-lock"></i> Thanh toán <?php echo number_format($data['course']->price); ?>₫
                            </button>
                            <a href="<?php echo URL_ROOT; ?>/courses/show/<?php echo $data['course']->id; ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <?php if($data['course']->image): ?>
                        <img src="<?php echo URL_ROOT; ?>/public/uploads/courses/<?php echo $data['course']->image; ?>" 
                             alt="<?php echo $data['course']->title; ?>" 
                             class="img-fluid rounded mb-3">
                    <?php endif; ?>
                    
                    <h6 class="fw-bold"><?php echo $data['course']->title; ?></h6>
                    
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Giá khóa học:</span>
                            <span class="fw-bold"><?php echo number_format($data['course']->price); ?>₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Thời lượng:</span>
                            <span><?php echo $data['course']->duration; ?> tuần</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Trình độ:</span>
                            <span class="badge bg-info">
                                <?php 
                                    switch($data['course']->level) {
                                        case 'beginner': echo 'Cơ bản'; break;
                                        case 'intermediate': echo 'Trung cấp'; break;
                                        case 'advanced': echo 'Nâng cao'; break;
                                        default: echo $data['course']->level;
                                    }
                                ?>
                            </span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Tổng thanh toán:</strong>
                            <strong class="text-primary fs-5"><?php echo number_format($data['course']->price); ?>₫</strong>
                        </div>
                    </div>
                    
                    <div class="alert alert-success mt-3 mb-0">
                        <small>
                            <i class="fas fa-check-circle"></i> Truy cập khóa học trọn đời
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-shield-alt"></i> Thanh toán an toàn</h6>
                    <p class="small text-muted mb-0">
                        Thông tin thanh toán của bạn được bảo vệ bởi MoMo và VNPay với 
                        công nghệ mã hóa SSL tiêu chuẩn quốc tế.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Courses checkout page loaded');
    
    const stripeRadio = document.getElementById('payment_method_stripe');
    const momoRadio = document.getElementById('payment_method_momo');
    const vnpayRadio = document.getElementById('payment_method_vnpay');
    
    const stripeInfo = document.getElementById('stripe-info');
    const momoInfo = document.getElementById('momo-info');
    const vnpayInfo = document.getElementById('vnpay-info');
    
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    function updatePaymentInfo() {
        stripeInfo.classList.add('d-none');
        momoInfo.classList.add('d-none');
        vnpayInfo.classList.add('d-none');
        
        if(stripeRadio.checked) {
            stripeInfo.classList.remove('d-none');
        } else if(momoRadio.checked) {
            momoInfo.classList.remove('d-none');
        } else if(vnpayRadio.checked) {
            vnpayInfo.classList.remove('d-none');
        }
    }
    
    // Update on load
    updatePaymentInfo();
    
    stripeRadio.addEventListener('change', updatePaymentInfo);
    momoRadio.addEventListener('change', updatePaymentInfo);
    vnpayRadio.addEventListener('change', updatePaymentInfo);
    
    // Handle form submit
    form.addEventListener('submit', function(e) {
        console.log('Form submitting...');
        
        // Get selected payment method
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
        
        if (!selectedMethod) {
            e.preventDefault();
            alert('Vui lòng chọn phương thức thanh toán!');
            return false;
        }
        
        console.log('Payment method selected:', selectedMethod.value);
        
        // Show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
        
        // Let form submit normally
        return true;
    });
});
</script>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>

