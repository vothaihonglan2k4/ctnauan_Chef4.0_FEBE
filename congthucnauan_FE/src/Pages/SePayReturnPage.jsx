import { useState, useEffect } from 'react';
import { Link, useSearchParams, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

export default function SePayReturnPage() {
    const [searchParams] = useSearchParams();
    const [result, setResult] = useState(null);
    const [loading, setLoading] = useState(true);

    const { isAuthenticated } = useAuth();
    const navigate = useNavigate();

    useEffect(() => {
        if (!isAuthenticated) {
            navigate('/login');
            return;
        }

        handleReturn();
    }, [isAuthenticated, navigate]);

    const handleReturn = async () => {
        try {
            const params = {};
            for (const [key, value] of searchParams.entries()) {
                params[key] = value;
            }

            const queryString = new URLSearchParams(params).toString();
            const response = await fetch(`/api/v1/sepay/return?${queryString}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            setResult(data);
        } catch (error) {
            console.error('Error:', error);
            setResult({
                success: false,
                message: 'Có lỗi xảy ra khi xác thực thanh toán SePay'
            });
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
                    <p className="mt-3">Đang xác thực thanh toán SePay...</p>
                </div>
            </div>
        );
    }

    if (!result) {
        return null;
    }

    if (result.success) {
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
                                <p className="lead">Cảm ơn bạn đã thanh toán qua SePay</p>

                                {result.payment && (
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
                                                        #{result.payment.id}
                                                    </div>
                                                </div>
                                                <div className="row text-start mt-2">
                                                    <div className="col-6">
                                                        <strong>Số tiền:</strong>
                                                    </div>
                                                    <div className="col-6">
                                                        {new Intl.NumberFormat('vi-VN').format(result.payment.amount)}₫
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
                                                        <code className="small">{result.payment.transaction_id}</code>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                )}

                                <div className="mt-4">
                                    {result.payment && result.payment.course_id ? (
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

    return (
        <div className="container mt-5">
            <div className="row justify-content-center">
                <div className="col-md-6">
                    <div className="card border-danger">
                        <div className="card-body text-center py-5">
                            <div className="mb-4">
                                <i className="fas fa-times-circle fa-5x text-danger"></i>
                            </div>
                            <h2 className="text-danger mb-3">Thanh toán thất bại</h2>
                            <p className="lead">{result.message}</p>

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
                </div>
            </div>
        </div>
    );
}
