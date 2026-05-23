import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

export default function CheckoutPage() {
    const [formData, setFormData] = useState({
        amount: '',
        payment_method: 'stripe'
    });
    const [cardData, setCardData] = useState({
        card_number: '',
        card_name: '',
        card_expiry: '',
        card_cvv: ''
    });
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    
    const { token, isAuthenticated } = useAuth();
    const navigate = useNavigate();

    useEffect(() => {
        if (!isAuthenticated) {
            navigate('/login');
        }
    }, [isAuthenticated, navigate]);

    const packages = [
        { value: '50000', label: 'Gói Cơ Bản - 50,000₫/tháng', features: ['Truy cập công thức cơ bản', 'Lưu 10 công thức'] },
        { value: '100000', label: 'Gói Tiêu Chuẩn - 100,000₫/tháng', features: ['Truy cập tất cả công thức', 'Lưu không giới hạn', 'Không quảng cáo'] },
        { value: '200000', label: 'Gói Cao Cấp - 200,000₫/tháng', features: ['Tất cả tính năng Tiêu Chuẩn', 'Khóa học miễn phí', 'Hỗ trợ ưu tiên'] }
    ];

    const paymentMethods = [
        { 
            value: 'stripe', 
            icon: 'fab fa-cc-stripe', 
            color: '#635BFF',
            title: 'Stripe - Thẻ Visa/Mastercard',
            description: '✨ Khuyến nghị - Test dễ dàng với thẻ ảo'
        },
        { 
            value: 'momo', 
            icon: 'fas fa-wallet', 
            color: '#d82d8b',
            title: 'Ví điện tử MoMo',
            description: 'Thanh toán qua ví MoMo - Nhanh chóng & Bảo mật'
        },
        { 
            value: 'vnpay', 
            icon: 'fas fa-credit-card', 
            color: '#0066cc',
            title: 'VNPay',
            description: 'Thanh toán qua thẻ ATM, Visa, MasterCard, QR Code'
        },
        { 
            value: 'bank_transfer', 
            icon: 'fas fa-university', 
            color: '#28a745',
            title: 'Chuyển khoản ngân hàng',
            description: 'Chuyển khoản trực tiếp qua ngân hàng'
        },
        { 
            value: 'credit_card', 
            icon: 'far fa-credit-card', 
            color: '#17a2b8',
            title: 'Thẻ tín dụng/ghi nợ',
            description: 'Thanh toán trực tiếp bằng thẻ'
        }
    ];

    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
        if (errors[e.target.name]) {
            setErrors({ ...errors, [e.target.name]: '' });
        }
    };

    const handleCardChange = (e) => {
        let value = e.target.value;
        
        // Format card number
        if (e.target.name === 'card_number') {
            value = value.replace(/\s/g, '').replace(/(\d{4})/g, '$1 ').trim();
        }
        
        // Format expiry
        if (e.target.name === 'card_expiry') {
            value = value.replace(/\D/g, '').replace(/(\d{2})(\d)/, '$1/$2').substr(0, 5);
        }
        
        // Limit CVV
        if (e.target.name === 'card_cvv') {
            value = value.replace(/\D/g, '').substr(0, 4);
        }
        
        setCardData({
            ...cardData,
            [e.target.name]: value
        });
    };

    const validateForm = () => {
        const newErrors = {};

        if (!formData.amount) {
            newErrors.amount = 'Vui lòng chọn gói thanh toán';
        }

        if (!formData.payment_method) {
            newErrors.payment_method = 'Vui lòng chọn phương thức thanh toán';
        }

        // Validate card if credit_card method
        if (formData.payment_method === 'credit_card') {
            if (!cardData.card_number) {
                newErrors.card_number = 'Vui lòng nhập số thẻ';
            }
            if (!cardData.card_name) {
                newErrors.card_name = 'Vui lòng nhập tên chủ thẻ';
            }
            if (!cardData.card_expiry) {
                newErrors.card_expiry = 'Vui lòng nhập ngày hết hạn';
            }
            if (!cardData.card_cvv) {
                newErrors.card_cvv = 'Vui lòng nhập mã CVV';
            }
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!validateForm()) {
            return;
        }

        setLoading(true);

        try {
            // If Stripe payment, use Stripe Checkout
            if (formData.payment_method === 'stripe') {
                const response = await fetch('/api/v1/stripe/create-checkout-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: parseInt(formData.amount),
                        description: `Thanh toán gói ${selectedPackage?.label.split(' - ')[0]}`
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Redirect to Stripe Checkout
                    window.location.href = data.checkout_url;
                } else {
                    setErrors({ general: data.message || 'Có lỗi xảy ra' });
                    setLoading(false);
                }
            } 
            // If VNPay payment
            else if (formData.payment_method === 'vnpay') {
                const response = await fetch('/api/v1/vnpay/create-payment-url', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: parseInt(formData.amount),
                        order_info: `Thanh toán gói ${selectedPackage?.label.split(' - ')[0]}`
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Redirect to VNPay
                    window.location.href = data.payment_url;
                } else {
                    setErrors({ general: data.message || 'Có lỗi xảy ra' });
                    setLoading(false);
                }
            } 
            else {
                // Other payment methods
                const response = await fetch('/api/v1/payments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: parseInt(formData.amount),
                        payment_method: formData.payment_method
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    alert('Thanh toán đã được tạo thành công! Mã giao dịch: ' + data.payment.transaction_id);
                    navigate('/payments');
                } else {
                    if (data.errors) {
                        setErrors(data.errors);
                    } else {
                        setErrors({ general: data.message || 'Có lỗi xảy ra' });
                    }
                }
                setLoading(false);
            }
        } catch (error) {
            console.error('Error:', error);
            setErrors({ general: 'Có lỗi xảy ra khi xử lý thanh toán' });
            setLoading(false);
        }
    };

    const selectedPackage = packages.find(p => p.value === formData.amount);

    return (
        <div className="container mt-4">
            <div className="row mb-3">
                <div className="col">
                    <h1>
                        <i className="fas fa-shopping-cart me-2"></i>
                        Thanh toán
                    </h1>
                </div>
                <div className="col-auto">
                    <Link to="/payments" className="btn btn-secondary">
                        <i className="fas fa-arrow-left me-2"></i>
                        Quay lại
                    </Link>
                </div>
            </div>

            <div className="row">
                <div className="col-md-8">
                    <div className="card mb-4">
                        <div className="card-header bg-primary text-white">
                            <h5 className="mb-0">Thông tin thanh toán</h5>
                        </div>
                        <div className="card-body">
                            {errors.general && (
                                <div className="alert alert-danger">
                                    <i className="fas fa-exclamation-circle me-2"></i>
                                    {errors.general}
                                </div>
                            )}

                            <form onSubmit={handleSubmit}>
                                {/* Package Selection */}
                                <div className="mb-3">
                                    <label htmlFor="amount" className="form-label">
                                        Chọn gói <span className="text-danger">*</span>
                                    </label>
                                    <select
                                        className={`form-select ${errors.amount ? 'is-invalid' : ''}`}
                                        id="amount"
                                        name="amount"
                                        value={formData.amount}
                                        onChange={handleChange}
                                    >
                                        <option value="">-- Chọn gói --</option>
                                        {packages.map(pkg => (
                                            <option key={pkg.value} value={pkg.value}>
                                                {pkg.label}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.amount && (
                                        <div className="invalid-feedback">{errors.amount}</div>
                                    )}
                                </div>

                                {/* Payment Methods */}
                                <div className="mb-4">
                                    <label className="form-label">
                                        Phương thức thanh toán <span className="text-danger">*</span>
                                    </label>
                                    <div className="payment-methods">
                                        {paymentMethods.map(method => (
                                            <div key={method.value} className="form-check payment-method-item mb-3 p-3 border rounded">
                                                <input
                                                    className="form-check-input"
                                                    type="radio"
                                                    name="payment_method"
                                                    id={`payment_method_${method.value}`}
                                                    value={method.value}
                                                    checked={formData.payment_method === method.value}
                                                    onChange={handleChange}
                                                />
                                                <label 
                                                    className="form-check-label d-flex align-items-center w-100" 
                                                    htmlFor={`payment_method_${method.value}`}
                                                    style={{ cursor: 'pointer' }}
                                                >
                                                    <i className={`${method.icon} me-3 fs-3`} style={{ color: method.color }}></i>
                                                    <div>
                                                        <strong>{method.title}</strong>
                                                        <div className="text-muted small">{method.description}</div>
                                                    </div>
                                                </label>
                                            </div>
                                        ))}
                                    </div>
                                    {errors.payment_method && (
                                        <div className="text-danger mt-1">{errors.payment_method}</div>
                                    )}
                                </div>

                                {/* Credit Card Fields */}
                                {formData.payment_method === 'credit_card' && (
                                    <div id="credit-card-fields">
                                        <div className="row mb-3">
                                            <div className="col-md-12">
                                                <label htmlFor="card_number" className="form-label">
                                                    Số thẻ <span className="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    className={`form-control ${errors.card_number ? 'is-invalid' : ''}`}
                                                    id="card_number"
                                                    name="card_number"
                                                    value={cardData.card_number}
                                                    onChange={handleCardChange}
                                                    placeholder="1234 5678 9012 3456"
                                                    maxLength="19"
                                                />
                                                {errors.card_number && (
                                                    <div className="invalid-feedback">{errors.card_number}</div>
                                                )}
                                            </div>
                                        </div>

                                        <div className="row mb-3">
                                            <div className="col-md-12">
                                                <label htmlFor="card_name" className="form-label">
                                                    Tên chủ thẻ <span className="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    className={`form-control ${errors.card_name ? 'is-invalid' : ''}`}
                                                    id="card_name"
                                                    name="card_name"
                                                    value={cardData.card_name}
                                                    onChange={handleCardChange}
                                                    placeholder="NGUYEN VAN A"
                                                />
                                                {errors.card_name && (
                                                    <div className="invalid-feedback">{errors.card_name}</div>
                                                )}
                                            </div>
                                        </div>

                                        <div className="row mb-3">
                                            <div className="col-md-6">
                                                <label htmlFor="card_expiry" className="form-label">
                                                    Ngày hết hạn <span className="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    className={`form-control ${errors.card_expiry ? 'is-invalid' : ''}`}
                                                    id="card_expiry"
                                                    name="card_expiry"
                                                    value={cardData.card_expiry}
                                                    onChange={handleCardChange}
                                                    placeholder="MM/YY"
                                                />
                                                {errors.card_expiry && (
                                                    <div className="invalid-feedback">{errors.card_expiry}</div>
                                                )}
                                            </div>
                                            <div className="col-md-6">
                                                <label htmlFor="card_cvv" className="form-label">
                                                    Mã CVV <span className="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    className={`form-control ${errors.card_cvv ? 'is-invalid' : ''}`}
                                                    id="card_cvv"
                                                    name="card_cvv"
                                                    value={cardData.card_cvv}
                                                    onChange={handleCardChange}
                                                    placeholder="123"
                                                />
                                                {errors.card_cvv && (
                                                    <div className="invalid-feedback">{errors.card_cvv}</div>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                )}

                                {/* Payment Method Info */}
                                {formData.payment_method === 'bank_transfer' && (
                                    <div className="alert alert-info">
                                        <h6 className="alert-heading">Thông tin chuyển khoản:</h6>
                                        <p className="mb-0">Ngân hàng: <strong>VIETCOMBANK</strong></p>
                                        <p className="mb-0">Số tài khoản: <strong>1234567890</strong></p>
                                        <p className="mb-0">Chủ tài khoản: <strong>CÔNG TY ABC</strong></p>
                                        <p className="mb-0">Nội dung: <strong>THANHTOAN [Tên của bạn]</strong></p>
                                        <p className="mt-2 mb-0">Sau khi chuyển khoản, nhấn "Xác nhận thanh toán".</p>
                                    </div>
                                )}

                                {formData.payment_method === 'momo' && (
                                    <div className="alert alert-info">
                                        <h6 className="alert-heading">
                                            <i className="fas fa-info-circle me-2"></i>
                                            Thanh toán qua MoMo
                                        </h6>
                                        <p className="mb-2">Bạn sẽ được chuyển đến trang thanh toán MoMo.</p>
                                        <ul className="mb-0">
                                            <li>Quét mã QR hoặc đăng nhập ví MoMo</li>
                                            <li>Xác nhận thanh toán</li>
                                            <li>Tự động quay lại sau khi hoàn tất</li>
                                        </ul>
                                    </div>
                                )}

                                {formData.payment_method === 'stripe' && (
                                    <div className="alert alert-success">
                                        <h6 className="alert-heading">
                                            <i className="fab fa-cc-stripe me-2"></i>
                                            Thanh toán qua Stripe
                                        </h6>
                                        <p className="mb-2">🎉 <strong>Test dễ dàng với thẻ ảo!</strong></p>
                                        <div className="card bg-light">
                                            <div className="card-body">
                                                <strong>Thẻ test (Thành công):</strong><br />
                                                Số thẻ: <code>4242 4242 4242 4242</code><br />
                                                Ngày hết hạn: <code>12/34</code><br />
                                                CVV: <code>123</code>
                                            </div>
                                        </div>
                                    </div>
                                )}

                                {/* Submit Button */}
                                <div className="d-grid gap-2">
                                    <button
                                        type="submit"
                                        className="btn btn-primary btn-lg"
                                        disabled={loading}
                                    >
                                        {loading ? (
                                            <>
                                                <span className="spinner-border spinner-border-sm me-2"></span>
                                                Đang xử lý...
                                            </>
                                        ) : (
                                            <>
                                                <i className="fas fa-check-circle me-2"></i>
                                                Xác nhận thanh toán
                                            </>
                                        )}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {/* Order Summary */}
                <div className="col-md-4">
                    <div className="card">
                        <div className="card-header bg-light">
                            <h5 className="mb-0">Tóm tắt đơn hàng</h5>
                        </div>
                        <div className="card-body">
                            {selectedPackage ? (
                                <>
                                    <h6>{selectedPackage.label.split(' - ')[0]}</h6>
                                    <ul className="list-unstyled">
                                        {selectedPackage.features.map((feature, index) => (
                                            <li key={index} className="mb-2">
                                                <i className="fas fa-check-circle text-success me-2"></i>
                                                {feature}
                                            </li>
                                        ))}
                                    </ul>
                                    <hr />
                                    <div className="d-flex justify-content-between">
                                        <strong>Tổng cộng:</strong>
                                        <strong className="text-primary">
                                            {new Intl.NumberFormat('vi-VN').format(selectedPackage.value)}₫
                                        </strong>
                                    </div>
                                </>
                            ) : (
                                <p className="text-muted">Vui lòng chọn gói thanh toán</p>
                            )}
                        </div>
                    </div>

                    {/* Security Info */}
                    <div className="card mt-3">
                        <div className="card-body">
                            <h6 className="card-title">
                                <i className="fas fa-shield-alt text-success me-2"></i>
                                Thanh toán an toàn
                            </h6>
                            <ul className="small text-muted mb-0">
                                <li>Mã hóa SSL 256-bit</li>
                                <li>Bảo mật thông tin thẻ</li>
                                <li>Không lưu trữ thông tin thẻ</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
