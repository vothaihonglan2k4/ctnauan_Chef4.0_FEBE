import { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import LoadingSpinner from '../Components/Common/LoadingSpinner';
import PaymentMethodSelector from '../Components/Payment/PaymentMethodSelector';
import CheckoutOrderSummary from '../Components/Payment/CheckoutOrderSummary';
import { formatCurrency } from '../utils/helpers';

export default function CourseCheckoutPage() {
    const { id } = useParams();
    const navigate = useNavigate();
    const { isAuthenticated, token } = useAuth();

    const [course, setCourse] = useState(null);
    const [loading, setLoading] = useState(true);
    const [processing, setProcessing] = useState(false);
    const [error, setError] = useState(null);
    const [paymentMethod, setPaymentMethod] = useState('stripe');

    useEffect(() => {
        if (!isAuthenticated) {
            navigate('/login', { state: { from: `/courses/${id}/checkout` } });
            return;
        }
        fetchCourse();
    }, [id, isAuthenticated]);

    const fetchCourse = async () => {
        try {
            const response = await fetch(`/api/v1/courses/${id}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch course');
            }

            const data = await response.json();
            if (data.is_enrolled) {
                navigate(`/courses/learn/${id}`);
                return;
            }
            if (data.course) {
                setCourse(data.course);
            } else {
                throw new Error('Course data not found');
            }
        } catch (err) {
            console.error('Error:', err);
            setError('Không thể tải thông tin khóa học');
        } finally {
            setLoading(false);
        }
    };

    const handlePayment = async (e) => {
        e.preventDefault();
        setProcessing(true);
        setError(null);

        try {
            let endpoint = '';
            let body = {};

            if (paymentMethod === 'stripe') {
                endpoint = '/api/v1/stripe/create-checkout-session';
                body = {
                    amount: course.price,
                    description: `Thanh toán khóa học: ${course.title}`,
                    metadata: {
                        type: 'course_enrollment',
                        course_id: course.id
                    }
                };
            } else if (paymentMethod === 'vnpay') {
                endpoint = '/api/v1/vnpay/create-payment-url';
                body = {
                    amount: course.price,
                    order_info: `Thanh toán khóa học: ${course.title}`,
                    order_type: 'course_enrollment',
                    course_id: course.id
                };
            } else if (paymentMethod === 'momo') {
                endpoint = '/api/v1/momo/create-payment';
                body = {
                    amount: course.price,
                    order_info: `Thanh toán khóa học: ${course.title}`,
                    course_id: course.id
                };
            } else {
                alert('Phương thức thanh toán này chưa được hỗ trợ trong phiên bản này.');
                setProcessing(false);
                return;
            }

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(body)
            });

            const data = await response.json();

            if (data.success) {
                if (data.checkout_url) {
                    window.location.href = data.checkout_url;
                } else if (data.payment_url) {
                    window.location.href = data.payment_url;
                }
            } else {
                setError(data.message || 'Có lỗi xảy ra khi tạo thanh toán');
                setProcessing(false);
            }
        } catch (err) {
            console.error('Error:', err);
            setError('Có lỗi xảy ra khi xử lý thanh toán');
            setProcessing(false);
        }
    };

    if (loading) {
        return <LoadingSpinner size="large" message="Đang tải thông tin thanh toán..." />;
    }

    if (!course) return null;

    return (
        <div className="container py-5">
            <div className="row">
                {/* Payment Form */}
                <div className="col-md-8">
                    <div className="card shadow-sm mb-4">
                        <div className="card-header bg-primary text-white">
                            <h4 className="mb-0">
                                <i className="fas fa-shopping-cart me-2"></i> Thanh toán khóa học
                            </h4>
                        </div>
                        <div className="card-body">
                            {/* Error Message */}
                            {error && (
                                <div className="alert alert-danger">
                                    <i className="fas fa-exclamation-circle me-2"></i>
                                    {error}
                                </div>
                            )}

                            <form onSubmit={handlePayment}>
                                {/* Payment Method Selector */}
                                <PaymentMethodSelector
                                    selectedMethod={paymentMethod}
                                    onMethodChange={setPaymentMethod}
                                />

                                {/* Submit Buttons */}
                                <div className="d-grid gap-2 mt-4">
                                    <button
                                        type="submit"
                                        className="btn btn-primary btn-lg"
                                        disabled={processing}
                                    >
                                        {processing ? (
                                            <>
                                                <span className="spinner-border spinner-border-sm me-2"></span>
                                                Đang xử lý...
                                            </>
                                        ) : (
                                            <>
                                                <i className="fas fa-lock me-2"></i>
                                                Thanh toán {formatCurrency(course.price)}
                                            </>
                                        )}
                                    </button>
                                    <Link to={`/courses/${course.id}`} className="btn btn-outline-secondary">
                                        <i className="fas fa-arrow-left me-2"></i> Quay lại
                                    </Link>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {/* Order Summary Sidebar */}
                <div className="col-md-4">
                    <CheckoutOrderSummary course={course} />
                </div>
            </div>
        </div>
    );
}
