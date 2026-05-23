import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

export default function CourseSchedulePage() {
    const [schedules, setSchedules] = useState([]);
    const [loading, setLoading] = useState(true);
    const [activeTab, setActiveTab] = useState('upcoming');
    const { isAuthenticated, token } = useAuth();
    const navigate = useNavigate();

    useEffect(() => {
        if (!isAuthenticated) {
            navigate('/login');
            return;
        }
        fetchSchedules();
    }, [isAuthenticated]);

    const fetchSchedules = async () => {
        try {
            const response = await fetch('/api/v1/my-courses/schedule', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            setSchedules(data.schedules || []);
        } catch (error) {
            console.error('Error fetching schedules:', error);
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
                <p className="mt-2">Đang tải lịch học...</p>
            </div>
        );
    }

    return (
        <div className="container py-5">
            {/* Page Header */}
            <div className="row mb-4">
                <div className="col-md-8">
                    <h1 className="mb-3">Lịch học offline</h1>
                    <p className="lead text-muted">Lịch học offline cho các khóa học của bạn</p>
                </div>
                <div className="col-md-4 d-flex align-items-center justify-content-md-end">
                    <Link to="/my-courses" className="btn btn-outline-primary">
                        <i className="fas fa-arrow-left me-1"></i> Quay lại khóa học của tôi
                    </Link>
                </div>
            </div>

            {/* Schedule Content */}
            {schedules.length === 0 ? (
                <div className="card shadow-sm">
                    <div className="card-body text-center py-5">
                        <i className="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <h3>Bạn chưa có lịch học offline nào</h3>
                        <p className="lead">Các khóa học của bạn hiện chưa có lịch học offline kèm theo</p>
                        <Link to="/courses" className="btn btn-primary btn-lg mt-3">
                            <i className="fas fa-search me-2"></i> Khám phá thêm khóa học
                        </Link>
                    </div>
                </div>
            ) : (
                <div className="row">
                    <div className="col-md-12">
                        <div className="card shadow-sm">
                            <div className="card-header bg-white">
                                <ul className="nav nav-tabs card-header-tabs" role="tablist">
                                    <li className="nav-item" role="presentation">
                                        <button 
                                            className={`nav-link ${activeTab === 'upcoming' ? 'active' : ''}`}
                                            onClick={() => setActiveTab('upcoming')}
                                            type="button"
                                        >
                                            Lịch học sắp tới
                                        </button>
                                    </li>
                                    <li className="nav-item" role="presentation">
                                        <button 
                                            className={`nav-link ${activeTab === 'all' ? 'active' : ''}`}
                                            onClick={() => setActiveTab('all')}
                                            type="button"
                                        >
                                            Tất cả lịch học
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div className="card-body">
                                <div className="table-responsive">
                                    <table className="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Khóa học</th>
                                                <th>Địa điểm</th>
                                                <th>Thời gian</th>
                                                <th>Giảng viên</th>
                                                <th>Lịch học kế tiếp</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {schedules.map((schedule, index) => (
                                                <tr key={index}>
                                                    <td>
                                                        <Link to={`/courses/${schedule.course_id}`}>
                                                            {schedule.course_title}
                                                        </Link>
                                                    </td>
                                                    <td>{schedule.location}</td>
                                                    <td>{schedule.schedule_time}</td>
                                                    <td>{schedule.instructor}</td>
                                                    <td>
                                                        <span className="badge bg-primary">
                                                            {schedule.next_class}
                                                        </span>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Schedule Notice */}
            <div className="row mt-4">
                <div className="col-md-12">
                    <div className="alert alert-info">
                        <h5 className="alert-heading">
                            <i className="fas fa-info-circle me-2"></i> Lưu ý về lịch học
                        </h5>
                        <p>Lịch học có thể thay đổi tùy theo tình hình thực tế. Vui lòng kiểm tra thường xuyên để có thông tin cập nhật nhất.</p>
                        <hr />
                        <p className="mb-0">
                            Nếu bạn không thể tham gia buổi học, vui lòng thông báo trước ít nhất 24 giờ theo hotline: <strong>0123.456.789</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
}
