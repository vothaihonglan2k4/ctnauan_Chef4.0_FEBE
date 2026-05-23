import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

export default function MyCoursesPage() {
    const [enrollments, setEnrollments] = useState([]);
    const [loading, setLoading] = useState(true);
    const { isAuthenticated } = useAuth();
    const navigate = useNavigate();

    useEffect(() => {
        if (!isAuthenticated) {
            navigate('/login');
            return;
        }
        fetchMyCourses();
    }, [isAuthenticated]);

    const fetchMyCourses = async () => {
        try {
            const token = localStorage.getItem('token');
            const response = await fetch('/api/v1/my-courses', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch my courses');
            }

            const data = await response.json();
            setEnrollments(data.enrollments || []);
        } catch (error) {
            console.error('Error:', error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return (
            <div className="container py-5 text-center">
                <div className="spinner-border text-primary" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
            </div>
        );
    }

    return (
        <div className="container py-5">
            <div className="row mb-4">
                <div className="col-md-8">
                    <h1 className="mb-3">Khóa học của tôi</h1>
                    <p className="lead text-muted">Danh sách các khóa học bạn đã đăng ký</p>
                </div>
                <div className="col-md-4 d-flex align-items-center justify-content-md-end">
                    <Link to="/my-courses/schedule" className="btn btn-outline-primary me-2">
                        <i className="fas fa-calendar-alt me-1"></i> Lịch học
                    </Link>
                    <Link to="/courses" className="btn btn-outline-success">
                        <i className="fas fa-graduation-cap me-1"></i> Khám phá khóa học
                    </Link>
                </div>
            </div>

            {enrollments.length === 0 ? (
                <div className="card shadow-sm">
                    <div className="card-body text-center py-5">
                        <i className="fas fa-book-reader fa-4x text-muted mb-3"></i>
                        <h3>Bạn chưa đăng ký khóa học nào</h3>
                        <p className="lead">Hãy khám phá các khóa học hấp dẫn của chúng tôi để nâng cao kỹ năng nấu ăn</p>
                        <Link to="/courses" className="btn btn-primary btn-lg mt-3">
                            <i className="fas fa-search me-2"></i> Tìm khóa học ngay
                        </Link>
                    </div>
                </div>
            ) : (
                <div className="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    {enrollments.map(enrollment => (
                        <div key={enrollment.id} className="col">
                            <div className="card h-100 shadow-sm">
                                <div className="position-relative">
                                    <img
                                        src={enrollment.course.image ? `/uploads/courses/${enrollment.course.image}` : '/img/congthucnauan.jpg'}
                                        className="card-img-top"
                                        alt={enrollment.course.title}
                                        style={{ height: '160px', objectFit: 'cover' }}
                                    />

                                    <div className="position-absolute top-0 end-0 mt-2 me-2">
                                        {enrollment.status === 'completed' ? (
                                            <span className="badge bg-success">Đã hoàn thành</span>
                                        ) : (
                                            <span className="badge bg-primary">Đang học</span>
                                        )}
                                    </div>
                                </div>

                                <div className="card-body">
                                    <h5 className="card-title">{enrollment.course.title}</h5>

                                    <div className="progress mt-3 mb-2" style={{ height: '8px' }}>
                                        <div
                                            className="progress-bar"
                                            role="progressbar"
                                            style={{ width: `${enrollment.progress}%` }}
                                            aria-valuenow={enrollment.progress}
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                        ></div>
                                    </div>

                                    <div className="d-flex justify-content-between mb-3">
                                        <small className="text-muted">Tiến độ: {enrollment.progress}%</small>
                                        <small className="text-muted">{enrollment.course.lessons_count || 0} bài học</small>
                                    </div>

                                    <div className="d-flex justify-content-between align-items-center mb-2">
                                        <small className="text-muted">
                                            <i className="fas fa-clock me-1"></i>
                                            Đăng ký: {new Date(enrollment.enrolled_at).toLocaleDateString('vi-VN')}
                                        </small>
                                    </div>
                                </div>

                                <div className="card-footer bg-transparent">
                                    <Link
                                        to={`/courses/learn/${enrollment.course.id}`}
                                        className="btn btn-primary w-100"
                                    >
                                        {enrollment.progress > 0 ? (
                                            <>
                                                <i className="fas fa-play-circle me-1"></i> Tiếp tục học
                                            </>
                                        ) : (
                                            <>
                                                <i className="fas fa-play-circle me-1"></i> Bắt đầu học
                                            </>
                                        )}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
