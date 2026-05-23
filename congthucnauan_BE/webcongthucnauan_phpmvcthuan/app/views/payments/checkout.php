<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Thanh toán</h1>
        </div>
        <div class="col-auto">
            <a href="<?php echo URL_ROOT; ?>/payments" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Thông tin thanh toán</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo URL_ROOT; ?>/payments/checkout" method="post" id="payment-form">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Số tiền thanh toán <span class="text-danger">*</span></label>
                            <select class="form-select <?php echo (!empty($data['amount_err'])) ? 'is-invalid' : ''; ?>" id="amount" name="amount">
                                <option value="">-- Chọn gói --</option>
                                <option value="50000" <?php echo ($data['amount'] == '50000') ? 'selected' : ''; ?>>Gói Cơ Bản - 50,000₫/tháng</option>
                                <option value="100000" <?php echo ($data['amount'] == '100000') ? 'selected' : ''; ?>>Gói Tiêu Chuẩn - 100,000₫/tháng</option>
                                <option value="200000" <?php echo ($data['amount'] == '200000') ? 'selected' : ''; ?>>Gói Cao Cấp - 200,000₫/tháng</option>
                            </select>
                            <div class="invalid-feedback"><?php echo $data['amount_err']; ?></div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                            <div class="payment-methods">
                                <div class="form-check payment-method-item mb-3 p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_method_stripe" value="stripe" <?php echo ($data['payment_method'] == 'stripe') ? 'checked' : ''; ?>>
                                    <label class="form-check-label d-flex align-items-center" for="payment_method_stripe">
                                        <i class="fab fa-cc-stripe me-3 fs-3" style="color: #635BFF;"></i>
                                        <div>
                                            <strong>Stripe - Thẻ Visa/Mastercard</strong>
                                            <div class="text-muted small">✨ Khuyến nghị - Test dễ dàng với thẻ ảo</div>
                                        </div>
                                    </label>
                                </div>
                                <div class="form-check payment-method-item mb-3 p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_method_momo" value="momo" <?php echo ($data['payment_method'] == 'momo') ? 'checked' : ''; ?>>
                                    <label class="form-check-label d-flex align-items-center" for="payment_method_momo">
                                        <i class="fas fa-wallet me-3 fs-3 text-danger"></i>
                                        <div>
                                            <strong>Ví điện tử MoMo</strong>
                                            <div class="text-muted small">Thanh toán qua ví MoMo - Nhanh chóng & Bảo mật</div>
                                        </div>
                                    </label>
                                </div>
                                <div class="form-check payment-method-item mb-3 p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_method_vnpay" value="vnpay" <?php echo ($data['payment_method'] == 'vnpay') ? 'checked' : ''; ?>>
                                    <label class="form-check-label d-flex align-items-center" for="payment_method_vnpay">
                                        <i class="fas fa-credit-card me-3 fs-3 text-primary"></i>
                                        <div>
                                            <strong>VNPay</strong>
                                            <div class="text-muted small">Thanh toán qua thẻ ATM, Visa, MasterCard, QR Code</div>
                                        </div>
                                    </label>
                                </div>
                                <div class="form-check payment-method-item mb-3 p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_method_bank_transfer" value="bank_transfer" <?php echo ($data['payment_method'] == 'bank_transfer') ? 'checked' : ''; ?>>
                                    <label class="form-check-label d-flex align-items-center" for="payment_method_bank_transfer">
                                        <i class="fas fa-university me-3 fs-3 text-success"></i>
                                        <div>
                                            <strong>Chuyển khoản ngân hàng</strong>
                                            <div class="text-muted small">Chuyển khoản trực tiếp qua ngân hàng</div>
                                        </div>
                                    </label>
                                </div>
                                <div class="form-check payment-method-item mb-3 p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_method_credit_card" value="credit_card" <?php echo ($data['payment_method'] == 'credit_card') ? 'checked' : ''; ?>>
                                    <label class="form-check-label d-flex align-items-center" for="payment_method_credit_card">
                                        <i class="far fa-credit-card me-3 fs-3 text-info"></i>
                                        <div>
                                            <strong>Thẻ tín dụng/ghi nợ</strong>
                                            <div class="text-muted small">Thanh toán trực tiếp bằng thẻ</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <?php if(!empty($data['payment_method_err'])): ?>
                                <div class="text-danger mt-1"><?php echo $data['payment_method_err']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div id="credit-card-fields" class="<?php echo ($data['payment_method'] != 'credit_card') ? 'd-none' : ''; ?>">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="card_number" class="form-label">Số thẻ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php echo (!empty($data['card_number_err'])) ? 'is-invalid' : ''; ?>" id="card_number" name="card_number" value="<?php echo $data['card_number']; ?>" placeholder="1234 5678 9012 3456">
                                    <div class="invalid-feedback"><?php echo $data['card_number_err']; ?></div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="card_name" class="form-label">Tên chủ thẻ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php echo (!empty($data['card_name_err'])) ? 'is-invalid' : ''; ?>" id="card_name" name="card_name" value="<?php echo $data['card_name']; ?>">
                                    <div class="invalid-feedback"><?php echo $data['card_name_err']; ?></div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="card_expiry" class="form-label">Ngày hết hạn <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php echo (!empty($data['card_expiry_err'])) ? 'is-invalid' : ''; ?>" id="card_expiry" name="card_expiry" value="<?php echo $data['card_expiry']; ?>" placeholder="MM/YY">
                                    <div class="invalid-feedback"><?php echo $data['card_expiry_err']; ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="card_cvv" class="form-label">Mã CVV <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php echo (!empty($data['card_cvv_err'])) ? 'is-invalid' : ''; ?>" id="card_cvv" name="card_cvv" value="<?php echo $data['card_cvv']; ?>" placeholder="123">
                                    <div class="invalid-feedback"><?php echo $data['card_cvv_err']; ?></div>
                                </div>
                            </div>
                        </div>

                        <div id="bank-transfer-info" class="alert alert-info <?php echo ($data['payment_method'] != 'bank_transfer') ? 'd-none' : ''; ?>">
                            <h6 class="alert-heading">Thông tin chuyển khoản:</h6>
                            <p class="mb-0">Ngân hàng: <strong>VIETCOMBANK</strong></p>
                            <p class="mb-0">Số tài khoản: <strong>1234567890</strong></p>
                            <p class="mb-0">Chủ tài khoản: <strong>CÔNG TY ABC</strong></p>
                            <p class="mb-0">Nội dung chuyển khoản: <strong>THANHTOAN <?php echo $_SESSION['user_name']; ?></strong></p>
                            <p class="mt-2 mb-0">Sau khi chuyển khoản, nhấn nút "Xác nhận thanh toán" để hoàn tất.</p>
                        </div>

                        <div id="momo-info" class="alert alert-info <?php echo ($data['payment_method'] != 'momo') ? 'd-none' : ''; ?>">
                            <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Thanh toán qua MoMo</h6>
                            <p class="mb-2">Bạn sẽ được chuyển đến trang thanh toán của MoMo để hoàn tất giao dịch.</p>
                            <ul class="mb-0">
                                <li>Quét mã QR hoặc đăng nhập ví MoMo</li>
                                <li>Xác nhận thanh toán</li>
                                <li>Tự động quay lại trang web sau khi hoàn tất</li>
                            </ul>
                        </div>
                        
                        <div id="stripe-info" class="alert alert-success <?php echo ($data['payment_method'] != 'stripe') ? 'd-none' : ''; ?>">
                            <h6 class="alert-heading"><i class="fab fa-cc-stripe"></i> Thanh toán qua Stripe</h6>
                            <p class="mb-2">🎉 <strong>Test dễ dàng với thẻ ảo Stripe!</strong></p>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <strong>Thẻ test (Thành công):</strong><br>
                                    Số thẻ: <code>4242 4242 4242 4242</code><br>
                                    Ngày hết hạn: <code>12/34</code> (bất kỳ ngày tương lai)<br>
                                    CVC: <code>123</code> (bất kỳ 3 số)<br>
                                    <small class="text-muted">Tự động chuyển về sau khi thanh toán</small>
                                </div>
                            </div>
                        </div>
                        
                        <div id="vnpay-info" class="alert alert-info <?php echo ($data['payment_method'] != 'vnpay') ? 'd-none' : ''; ?>">
                            <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Thanh toán qua VNPay</h6>
                            <p class="mb-2">Bạn sẽ được chuyển đến cổng thanh toán VNPay để hoàn tất giao dịch.</p>
                            <ul class="mb-0">
                                <li>Hỗ trợ thẻ ATM nội địa (Internet Banking)</li>
                                <li>Hỗ trợ thẻ Visa, MasterCard, JCB</li>
                                <li>Thanh toán qua QR Code VNPAY-QR</li>
                                <li>Tự động quay lại trang web sau khi hoàn tất</li>
                            </ul>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Xác nhận thanh toán</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Thông tin gói dịch vụ</h5>
                </div>
                <div class="card-body">
                    <div class="payment-package" id="package-basic">
                        <h5>Gói Cơ Bản - 50.000₫/tháng</h5>
                        <ul>
                            <li>Lưu trữ tối đa 10 công thức</li>
                            <li>Truy cập các công thức cơ bản</li>
                            <li>Hỗ trợ qua email</li>
                        </ul>
                    </div>
                    <hr>
                    <div class="payment-package" id="package-standard">
                        <h5>Gói Tiêu Chuẩn - 100.000₫/tháng</h5>
                        <ul>
                            <li>Lưu trữ tối đa 50 công thức</li>
                            <li>Truy cập các công thức nâng cao</li>
                            <li>Tạo sách công thức cá nhân</li>
                            <li>Hỗ trợ qua email và điện thoại</li>
                        </ul>
                    </div>
                    <hr>
                    <div class="payment-package" id="package-premium">
                        <h5>Gói Cao Cấp - 200.000₫/tháng</h5>
                        <ul>
                            <li>Lưu trữ không giới hạn công thức</li>
                            <li>Truy cập tất cả công thức đặc biệt</li>
                            <li>Tạo và chia sẻ sách công thức</li>
                            <li>Không hiển thị quảng cáo</li>
                            <li>Hỗ trợ ưu tiên 24/7</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stripeRadio = document.getElementById('payment_method_stripe');
        const creditCardRadio = document.getElementById('payment_method_credit_card');
        const bankTransferRadio = document.getElementById('payment_method_bank_transfer');
        const momoRadio = document.getElementById('payment_method_momo');
        const vnpayRadio = document.getElementById('payment_method_vnpay');
        
        const stripeInfo = document.getElementById('stripe-info');
        const creditCardFields = document.getElementById('credit-card-fields');
        const bankTransferInfo = document.getElementById('bank-transfer-info');
        const momoInfo = document.getElementById('momo-info');
        const vnpayInfo = document.getElementById('vnpay-info');

        function updatePaymentMethod() {
            // Ẩn tất cả các phần thông tin
            stripeInfo.classList.add('d-none');
            creditCardFields.classList.add('d-none');
            bankTransferInfo.classList.add('d-none');
            momoInfo.classList.add('d-none');
            vnpayInfo.classList.add('d-none');
            
            // Hiển thị phần tương ứng
            if(stripeRadio.checked) {
                stripeInfo.classList.remove('d-none');
            } else if(creditCardRadio.checked) {
                creditCardFields.classList.remove('d-none');
            } else if(bankTransferRadio.checked) {
                bankTransferInfo.classList.remove('d-none');
            } else if(momoRadio.checked) {
                momoInfo.classList.remove('d-none');
            } else if(vnpayRadio.checked) {
                vnpayInfo.classList.remove('d-none');
            }
        }

        // Thêm event listeners
        stripeRadio.addEventListener('change', updatePaymentMethod);
        creditCardRadio.addEventListener('change', updatePaymentMethod);
        bankTransferRadio.addEventListener('change', updatePaymentMethod);
        momoRadio.addEventListener('change', updatePaymentMethod);
        vnpayRadio.addEventListener('change', updatePaymentMethod);
    });
</script>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>
