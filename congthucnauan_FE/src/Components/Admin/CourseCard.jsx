import { Link } from 'react-router-dom';

export default function CourseCard({ course }) {
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    };

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    };

    const truncateText = (text, maxLength = 100) => {
        if (!text) return '';
        return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
    };

    return (
        <div className="card h-100">
            <img 
                src={course.image 
                    ? `/uploads/courses/${course.image}` 
                    : '/img/congthucnauan.jpg'
                }
                className="card-img-top"
                alt={course.title}
                style={{ height: '180px', objectFit: 'cover' }}
                loading="lazy"
            />
            <div className="card-body">
                <h5 className="card-title">{course.title}</h5>
                <div className="d-flex justify-content-between align-items-center mb-2">
                    {course.classroom_name && (
                        <span className="badge bg-info">{course.classroom_name}</span>
                    )}
                    <span className="text-success fw-bold">
                        {formatCurrency(course.price)}
                    </span>
                </div>
                <p className="card-text text-muted small">
                    {truncateText(course.description)}
                </p>
            </div>
            <div className="card-footer bg-white">
                <div className="d-flex justify-content-between align-items-center">
                    <small className="text-muted">
                        <i className="far fa-calendar-alt me-1"></i>
                        {formatDate(course.created_at)}
                    </small>
                    <Link 
                        to={`/admin/courses/edit/${course.id}`}
                        className="btn btn-sm btn-outline-primary"
                    >
                        <i className="fas fa-edit me-1"></i> Sửa
                    </Link>
                </div>
            </div>
        </div>
    );
}
