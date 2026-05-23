import { useState, useEffect } from 'react';
import { Link, useSearchParams, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

export default function PaymentSuccessPage() {
    const [searchParams] = useSearchParams();
    const [payment, setPayment] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    const { token, isAuthenticated } = useAuth();
    const navigate = useNavigate();
    const sessionId = searchParams.get('session_id');

    useEffect(() => {
        if (!isAuthenticated) {
            navigate('/login');
            return;
        }

        if (sessionId) {
            verifyPayment();
        } else {
            setError('Không tìm thấy thông tin thanh toán');
            setLoading(false);
        }
    }, [sessionId, isAuthenticated, navigate]);

    const verifyPayment = async () => {
        try {
            const response = await fetch('/api/v1/stripe/verify-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ session_id: sessionId })
            });

            const data = await response.json();

            if (data.success) {
                setPayment(data.payment);
            } else {
                setError(data.message || 'Không thể xác thực thanh toán');
            }
        } catch (err) {
            console.error('Error:', err);
            setError('Có lỗi xảy ra khi xác thực thanh toán');
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return (
            <div className="container mt-5">
                <div className="text-center py-5">
                    <div className="spinner-border text-primary" style={{ width: '3rem', height: '3rem' }}>
                        <span className="visually-hidden">Đang xác thực...</span>
                    </div>
                    <p className="mt-3">Đang xác thực thanh toán...</p>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="container mt-5">
                <div className="row justify-content-center">
                    <div className="col-md-6">
                        <div className="card border-danger">
                            <div className="card-body text-center py-5">
                                <i className="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                                <h3>Có lỗi xảy ra</h3>
                                <p className="text-muted">{error}</p>
                                <Link to="/payments" className="btn btn-primary mt-3">
                                    Quay lại danh sách thanh toán
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="container mt-5">
            <div className="row justify-content-center">
                <div className="col-md-6">
                    <div className="card border-success">
                        <div className="card-body text-center py-5">
                            <div className="mb-4">
                                <i className="fas fa-check-circle fa-5x text-success"></i>
                            </div>
                            <h2 className="text-success mb-3">Thanh toán thành công!</h2>
                            <p className="lead">Cảm ơn bạn đã thanh toán</p>

                            {payment && (
                                <div className="mt-4">
                                    <div className="card bg-light">
                                        <div className="card-body">
                                            <h5 className="card-title">Thông tin thanh toán</h5>
                                            <hr />
                                            <div className="row text-start">
                                                <div className="col-6">
                                                    <strong>Mã thanh toán:</strong>
                                                </div>
                                                <div className="col-6">
                                                    #{payment.id}
                                                </div>
                                            </div>
                                            <div className="row text-start mt-2">
                                                <div className="col-6">
                                                    <strong>Số tiền:</strong>
                                                </div>
                                                <div className="col-6">
                                                    {new Intl.NumberFormat('vi-VN').format(payment.amount)}₫
                                                </div>
                                            </div>
                                            <div className="row text-start mt-2">
                                                <div className="col-6">
                                                    <strong>Trạng thái:</strong>
                                                </div>
                                                <div className="col-6">
                                                    <span className="badge bg-success">Hoàn thành</span>
                                                </div>
                                            </div>
                                            <div className="row text-start mt-2">
                                                <div className="col-6">
                                                    <strong>Mã giao dịch:</strong>
                                                </div>
                                                <div className="col-6">
                                                    <code className="small">{payment.transaction_id}</code>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}

                            <div className="mt-4">
                                {payment && payment.course_id ? (
                                    <Link to="/my-courses" className="btn btn-primary me-2">
                                        <i className="fas fa-graduation-cap me-2"></i>
                                        Vào khóa học của tôi
                                    </Link>
                                ) : (
                                    <Link to="/payments" className="btn btn-primary me-2">
                                        <i className="fas fa-list me-2"></i>
                                        Xem lịch sử thanh toán
                                    </Link>
                                )}
                                <Link to="/" className="btn btn-outline-secondary">
                                    <i className="fas fa-home me-2"></i>
                                    Về trang chủ
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
