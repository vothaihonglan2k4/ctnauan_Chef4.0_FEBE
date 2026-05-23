import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

export default function CoursesPage() {
    const [courses, setCourses] = useState([]);
    const [loading, setLoading] = useState(true);
    
    const { isAuthenticated } = useAuth();

    useEffect(() => {
        fetchCourses();
    }, []);

    const fetchCourses = async () => {
        try {
            const response = await fetch('/api/v1/courses');
            const data = await response.json();
            setCourses(data.courses || []);
        } catch (error) {
            console.error('Error:', error);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="container mt-4 py-5">
            <div className="row mb-4">
                <div className="col-md-8">
                    <h1 className="mb-3">
                        <i className="fas fa-graduation-cap me-2"></i>
                        Khóa học nấu ăn
                    </h1>
                    <p className="lead text-muted">
                        Nâng cao kỹ năng nấu ăn của bạn với các khóa học trực tuyến từ các đầu bếp chuyên nghiệp
                    </p>
                </div>
                {isAuthenticated && (
                    <div className="col-md-4 d-flex align-items-center justify-content-md-end">
                        <Link to="/my-courses" className="btn btn-outline-success">
                            <i className="fas fa-book-reader me-2"></i>
                            Khóa học của tôi
                        </Link>
                    </div>
                )}
            </div>

            {loading ? (
                <div className="text-center py-5">
                    <div className="spinner-border text-primary"></div>
                </div>
            ) : courses.length === 0 ? (
                <div className="alert alert-info">
                    <i className="fas fa-info-circle me-2"></i>
                    Hiện tại chưa có khóa học nào được công bố. Vui lòng quay lại sau.
                </div>
            ) : (
                <div className="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    {courses.map(course => (
                        <div key={course.id} className="col">
                            <div className="card h-100 shadow-sm">
                                <img 
                                    src={course.image ? `/uploads/courses/${course.image}` : '/img/congthucnauan.jpg'}
                                    className="card-img-top"
                                    alt={course.title}
                                    style={{ height: '200px', objectFit: 'cover' }}
                                />
                                
                                {course.price === 0 && (
                                    <div className="badge bg-success position-absolute top-0 end-0 mt-2 me-2">
                                        Miễn phí
                                    </div>
                                )}
                                
                                <div className="card-body">
                                    <h5 className="card-title">{course.title}</h5>
                                    <p className="card-text text-truncate">{course.description}</p>
                                    
                                    <div className="d-flex justify-content-between align-items-center mb-2">
                                        <small className="text-muted">
                                            <i className="fas fa-users me-1"></i>
                                            {course.enrollments_count || 0} học viên
                                        </small>
                                        <small className="text-muted">
                                            <i className="fas fa-film me-1"></i>
                                            {course.lessons_count || 0} bài học
                                        </small>
                                    </div>
                                </div>
                                
                                <div className="card-footer bg-transparent">
                                    <div className="d-flex justify-content-between align-items-center">
                                        {course.price > 0 ? (
                                            <span className="fw-bold text-primary">
                                                {new Intl.NumberFormat('vi-VN').format(course.price)}₫
                                            </span>
                                        ) : (
                                            <span className="fw-bold text-success">Miễn phí</span>
                                        )}
                                        
                                        <Link 
                                            to={`/courses/${course.id}`}
                                            className="btn btn-outline-primary btn-sm"
                                        >
                                            Xem chi tiết
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
