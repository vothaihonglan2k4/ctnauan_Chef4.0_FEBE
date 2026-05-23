import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

export default function PaymentsPage() {
    const [payments, setPayments] = useState([]);
    const [loading, setLoading] = useState(true);
    
    const { token, isAuthenticated } = useAuth();
    const navigate = useNavigate();

    useEffect(() => {
        if (!isAuthenticated) {
            navigate('/login');
            return;
        }

        fetchPayments();
    }, [isAuthenticated, navigate]);

    const fetchPayments = async () => {
        try {
            const response = await fetch('/api/v1/payments', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (response.ok) {
                setPayments(data.payments || []);
            }
        } catch (error) {
            console.error('Error fetching payments:', error);
        } finally {
            setLoading(false);
        }
    };

    const getPaymentMethodBadge = (method) => {
        const badges = {
            'credit_card': <span className="badge bg-info">Thẻ tín dụng</span>,
            'bank_transfer': <span className="badge bg-primary">Chuyển khoản</span>,
            'momo': <span className="badge bg-danger">MoMo</span>,
            'vnpay': <span className="badge bg-primary">VNPay</span>,
            'stripe': <span className="badge" style={{backgroundColor: '#635BFF'}}>Stripe</span>
        };
        return badges[method] || <span className="badge bg-secondary">{method}</span>;
    };

    const getStatusBadge = (status) => {
        const badges = {
            'completed': <span className="badge bg-success">Hoàn thành</span>,
            'pending': <span className="badge bg-warning">Đang xử lý</span>,
            'failed': <span className="badge bg-danger">Thất bại</span>,
            'cancelled': <span className="badge bg-secondary">Đã hủy</span>
        };
        return badges[status] || <span className="badge bg-secondary">{status}</span>;
    };

    return (
        <div className="container mt-4">
            <div className="row mb-4">
                <div className="col">
                    <h1>
                        <i className="fas fa-receipt me-2"></i>
                        Lịch sử thanh toán
                    </h1>
                </div>
                <div className="col-auto">
                    <Link to="/payments/checkout" className="btn btn-primary">
                        <i className="fas fa-plus me-2"></i>
                        Thanh toán mới
                    </Link>
                </div>
            </div>

            <div className="card">
                <div className="card-header bg-light">
                    <h5 className="mb-0">Thanh toán của bạn</h5>
                </div>
                <div className="card-body">
                    {loading ? (
                        <div className="text-center py-5">
                            <div className="spinner-border text-primary" role="status">
                                <span className="visually-hidden">Đang tải...</span>
                            </div>
                        </div>
                    ) : payments.length > 0 ? (
                        <div className="table-responsive">
                            <table className="table table-hover">
                                <thead className="table-light">
                                    <tr>
                                        <th>Mã thanh toán</th>
                                        <th>Số tiền</th>
                                        <th>Phương thức</th>
                                        <th>Trạng thái</th>
                                        <th>Mã giao dịch</th>
                                        <th>Thời gian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {payments.map(payment => (
                                        <tr key={payment.id}>
                                            <td>#{payment.id}</td>
                                            <td>{new Intl.NumberFormat('vi-VN').format(payment.amount)}₫</td>
                                            <td>{getPaymentMethodBadge(payment.payment_method)}</td>
                                            <td>{getStatusBadge(payment.status)}</td>
                                            <td>
                                                <code className="small">{payment.transaction_id || 'N/A'}</code>
                                            </td>
                                            <td>
                                                {new Date(payment.created_at).toLocaleString('vi-VN')}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <div className="text-center p-5">
                            <i className="fas fa-receipt fa-4x text-muted mb-3"></i>
                            <p className="lead">Bạn chưa có thanh toán nào.</p>
                            <Link to="/payments/checkout" className="btn btn-primary">
                                <i className="fas fa-plus me-2"></i>
                                Thanh toán mới
                            </Link>
                        </div>
                    )}
                </div>
            </div>

            {/* Membership Info */}
            <div className="row mt-4">
                <div className="col-md-8 mx-auto">
                    <div className="card">
                        <div className="card-header bg-primary text-white">
                            <h5 className="mb-0">
                                <i className="fas fa-crown me-2"></i>
                                Thông tin thành viên
                            </h5>
                        </div>
                        <div className="card-body">
                            <p className="lead">
                                Chúng tôi cung cấp nhiều phương thức thanh toán để bạn có thể ủng hộ website 
                                và mở khóa các tính năng cao cấp.
                            </p>
                            
                            <h6 className="mt-4 mb-3">
                                <i className="fas fa-star text-warning me-2"></i>
                                Quyền lợi thành viên trả phí:
                            </h6>
                            <ul className="list-unstyled">
                                <li className="mb-2">
                                    <i className="fas fa-check-circle text-success me-2"></i>
                                    Truy cập các công thức đặc biệt
                                </li>
                                <li className="mb-2">
                                    <i className="fas fa-check-circle text-success me-2"></i>
                                    Tạo sách công thức cá nhân
                                </li>
                                <li className="mb-2">
                                    <i className="fas fa-check-circle text-success me-2"></i>
                                    Lưu trữ công thức không giới hạn
                                </li>
                                <li className="mb-2">
                                    <i className="fas fa-check-circle text-success me-2"></i>
                                    Không hiển thị quảng cáo
                                </li>
                                <li className="mb-2">
                                    <i className="fas fa-check-circle text-success me-2"></i>
                                    Hỗ trợ ưu tiên
                                </li>
                            </ul>

                            <div className="mt-4">
                                <Link to="/payments/checkout" className="btn btn-primary btn-lg">
                                    <i className="fas fa-crown me-2"></i>
                                    Đăng ký gói thành viên
                                </Link>
                            </div>
                        </div>
                    </div>

                    {/* Payment Methods Info */}
                    <div className="card mt-4">
                        <div className="card-header bg-light">
                            <h6 className="mb-0">
                                <i className="fas fa-credit-card me-2"></i>
                                Phương thức thanh toán được hỗ trợ
                            </h6>
                        </div>
                        <div className="card-body">
                            <div className="row text-center">
                                <div className="col-6 col-md-3 mb-3">
                                    <i className="fab fa-cc-stripe fa-3x mb-2" style={{color: '#635BFF'}}></i>
                                    <p className="small mb-0">Stripe</p>
                                </div>
                                <div className="col-6 col-md-3 mb-3">
                                    <i className="fas fa-wallet fa-3x text-danger mb-2"></i>
                                    <p className="small mb-0">MoMo</p>
                                </div>
                                <div className="col-6 col-md-3 mb-3">
                                    <i className="fas fa-credit-card fa-3x text-primary mb-2"></i>
                                    <p className="small mb-0">VNPay</p>
                                </div>
                                <div className="col-6 col-md-3 mb-3">
                                    <i className="fas fa-university fa-3x text-success mb-2"></i>
                                    <p className="small mb-0">Chuyển khoản</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
