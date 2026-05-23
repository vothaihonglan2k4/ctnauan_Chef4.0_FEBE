import React from 'react';
import { formatCurrency } from '../../utils/helpers';

const CheckoutOrderSummary = ({ course }) => {
    const getLevelLabel = (level) => {
        const labels = {
            'beginner': 'Cơ bản',
            'intermediate': 'Trung cấp',
            'advanced': 'Nâng cao'
        };
        return labels[level] || level;
    };

    return (
        <>
            {/* Order Details Card */}
            <div className="card shadow-sm position-sticky" style={{ top: '20px' }}>
                <div className="card-header bg-light">
                    <h5 className="mb-0">
                        <i className="fas fa-file-invoice me-2"></i> Thông tin đơn hàng
                    </h5>
                </div>
                <div className="card-body">
                    {/* Course Image */}
                    <img
                        src={course.image ? `/uploads/courses/${course.image}` : '/img/congthucnauan.jpg'}
                        alt={course.title}
                        className="img-fluid rounded mb-3"
                    />

                    {/* Course Title */}
                    <h6 className="fw-bold">{course.title}</h6>

                    {/* Order Details */}
                    <div className="mt-3">
                        <div className="d-flex justify-content-between mb-2">
                            <span className="text-muted">Giá khóa học:</span>
                            <span className="fw-bold">{formatCurrency(course.price)}</span>
                        </div>
                        <div className="d-flex justify-content-between mb-2">
                            <span className="text-muted">Thời lượng:</span>
                            <span>{course.duration} tuần</span>
                        </div>
                        <div className="d-flex justify-content-between mb-2">
                            <span className="text-muted">Trình độ:</span>
                            <span className="badge bg-info text-dark">
                                {getLevelLabel(course.level)}
                            </span>
                        </div>
                        <hr />
                        <div className="d-flex justify-content-between">
                            <strong>Tổng thanh toán:</strong>
                            <strong className="text-primary fs-5">{formatCurrency(course.price)}</strong>
                        </div>
                    </div>

                    {/* Lifetime Access Badge */}
                    <div className="alert alert-success mt-3 mb-0">
                        <small>
                            <i className="fas fa-check-circle me-1"></i> Truy cập khóa học trọn đời
                        </small>
                    </div>
                </div>
            </div>

            {/* Security Info Card */}
            <div className="card mt-3">
                <div className="card-body">
                    <h6 className="fw-bold mb-3">
                        <i className="fas fa-shield-alt me-2"></i> Thanh toán an toàn
                    </h6>
                    <p className="small text-muted mb-0">
                        Thông tin thanh toán của bạn được bảo vệ bởi MoMo và VNPay với
                        công nghệ mã hóa SSL tiêu chuẩn quốc tế.
                    </p>
                </div>
            </div>
        </>
    );
};

export default CheckoutOrderSummary;
