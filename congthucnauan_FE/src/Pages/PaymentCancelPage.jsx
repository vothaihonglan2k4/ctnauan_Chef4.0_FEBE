import { Link } from 'react-router-dom';

export default function PaymentCancelPage() {
    return (
        <div className="container mt-5">
            <div className="row justify-content-center">
                <div className="col-md-6">
                    <div className="card border-warning">
                        <div className="card-body text-center py-5">
                            <div className="mb-4">
                                <i className="fas fa-times-circle fa-5x text-warning"></i>
                            </div>
                            <h2 className="text-warning mb-3">Thanh toán đã bị hủy</h2>
                            <p className="lead">Bạn đã hủy quá trình thanh toán</p>
                            <p className="text-muted">
                                Không có khoản phí nào được tính. Bạn có thể thử lại bất cứ lúc nào.
                            </p>

                            <div className="mt-4">
                                <Link to="/payments/checkout" className="btn btn-primary me-2">
                                    <i className="fas fa-redo me-2"></i>
                                    Thử lại
                                </Link>
                                <Link to="/payments" className="btn btn-outline-secondary">
                                    <i className="fas fa-list me-2"></i>
                                    Xem lịch sử thanh toán
                                </Link>
                            </div>

                            <div className="mt-4">
                                <Link to="/" className="text-decoration-none">
                                    <i className="fas fa-home me-2"></i>
                                    Về trang chủ
                                </Link>
                            </div>
                        </div>
                    </div>

                    {/* Help Section */}
                    <div className="card mt-3">
                        <div className="card-body">
                            <h6 className="card-title">
                                <i className="fas fa-question-circle text-info me-2"></i>
                                Cần hỗ trợ?
                            </h6>
                            <p className="small text-muted mb-2">
                                Nếu bạn gặp vấn đề trong quá trình thanh toán, vui lòng liên hệ:
                            </p>
                            <ul className="small text-muted mb-0">
                                <li>Email: support@example.com</li>
                                <li>Hotline: 1900 xxxx</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
