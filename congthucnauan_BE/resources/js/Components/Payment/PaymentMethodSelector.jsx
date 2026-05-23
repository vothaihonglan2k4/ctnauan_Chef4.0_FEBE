import React from 'react';

const PaymentMethodSelector = ({ selectedMethod, onMethodChange }) => {
    const paymentMethods = [
        {
            id: 'stripe',
            name: 'Stripe - Thẻ Visa/Mastercard',
            icon: 'fab fa-cc-stripe',
            iconColor: '#635BFF',
            description: '✨ Khuyến nghị - Test dễ dàng với thẻ ảo',
            info: {
                title: 'Thanh toán qua Stripe',
                content: (
                    <>
                        <p className="mb-2">🎉 <strong>Test dễ dàng với thẻ ảo!</strong></p>
                        <div className="card bg-white border-success">
                            <div className="card-body">
                                <strong>✅ Thẻ test - Thanh toán thành công:</strong><br />
                                <div className="row mt-2">
                                    <div className="col-md-6">
                                        Số thẻ: <code className="text-success">4242 4242 4242 4242</code><br />
                                        Ngày: <code>12/34</code> | CVC: <code>123</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </>
                ),
                alertClass: 'alert-success'
            }
        },
        {
            id: 'momo',
            name: 'Ví điện tử MoMo',
            icon: 'fas fa-wallet',
            iconColor: 'text-danger',
            description: 'Thanh toán qua ví MoMo - Nhanh chóng & Bảo mật',
            info: {
                title: 'Thanh toán qua MoMo',
                content: <p className="mb-0">Bạn sẽ được chuyển đến trang thanh toán của MoMo để hoàn tất giao dịch.</p>,
                alertClass: 'alert-info'
            }
        },
        {
            id: 'vnpay',
            name: 'VNPay',
            icon: 'fas fa-credit-card',
            iconColor: 'text-primary',
            description: 'Thanh toán qua thẻ ATM, Visa, MasterCard, QR Code',
            info: {
                title: 'Thanh toán qua VNPay',
                content: <p className="mb-0">Bạn sẽ được chuyển đến cổng thanh toán VNPay để hoàn tất giao dịch.</p>,
                alertClass: 'alert-info'
            }
        }
    ];

    const selectedPaymentInfo = paymentMethods.find(m => m.id === selectedMethod)?.info;

    return (
        <>
            <h5 className="mb-4">Chọn phương thức thanh toán</h5>

            <div className="payment-methods">
                {paymentMethods.map((method) => (
                    <div
                        key={method.id}
                        className={`form-check payment-method-item mb-3 p-3 border rounded ${selectedMethod === method.id ? 'border-primary bg-light' : ''
                            }`}
                    >
                        <input
                            className="form-check-input"
                            type="radio"
                            name="payment_method"
                            id={`payment_method_${method.id}`}
                            value={method.id}
                            checked={selectedMethod === method.id}
                            onChange={(e) => onMethodChange(e.target.value)}
                        />
                        <label
                            className="form-check-label d-flex align-items-center w-100"
                            htmlFor={`payment_method_${method.id}`}
                            style={{ cursor: 'pointer' }}
                        >
                            <i
                                className={`${method.icon} me-3 fs-3`}
                                style={method.iconColor.startsWith('#') ? { color: method.iconColor } : {}}
                                {...(method.iconColor.startsWith('text-') && { className: `${method.icon} me-3 fs-3 ${method.iconColor}` })}
                            ></i>
                            <div className="flex-grow-1">
                                <strong>{method.name}</strong>
                                <div className="text-muted small">{method.description}</div>
                            </div>
                        </label>
                    </div>
                ))}
            </div>

            {/* Payment Method Info */}
            {selectedPaymentInfo && (
                <div className={`alert ${selectedPaymentInfo.alertClass} mt-3`}>
                    <h6 className="alert-heading">
                        <i className="fas fa-info-circle"></i> {selectedPaymentInfo.title}
                    </h6>
                    {selectedPaymentInfo.content}
                </div>
            )}
        </>
    );
};

export default PaymentMethodSelector;
