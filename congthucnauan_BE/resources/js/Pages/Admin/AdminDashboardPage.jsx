import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import StatCard from '../../Components/Admin/StatCard';
import RecentUsersTable from '../../Components/Admin/RecentUsersTable';
import RecentPaymentsTable from '../../Components/Admin/RecentPaymentsTable';
import CourseCard from '../../Components/Admin/CourseCard';

export default function AdminDashboardPage() {
    const [stats, setStats] = useState(null);
    const [recentUsers, setRecentUsers] = useState([]);
    const [recentPayments, setRecentPayments] = useState([]);
    const [recentCourses, setRecentCourses] = useState([]);
    const [loading, setLoading] = useState({
        stats: true,
        users: true,
        payments: true,
        courses: true
    });
    const [error, setError] = useState(null);

    const { token } = useAuth();

    useEffect(() => {
        fetchDashboardData();
    }, []);

    const fetchDashboardData = async () => {
        try {
            // Fetch all data in parallel
            const [statsRes, usersRes, paymentsRes, coursesRes] = await Promise.all([
                fetch('/api/v1/admin/dashboard/stats', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                }),
                fetch('/api/v1/admin/dashboard/recent-users?limit=10', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                }),
                fetch('/api/v1/admin/dashboard/recent-payments?limit=10', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                }),
                fetch('/api/v1/admin/dashboard/recent-courses?limit=6', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                })
            ]);

            // Parse responses
            const statsData = await statsRes.json();
            const usersData = await usersRes.json();
            const paymentsData = await paymentsRes.json();
            const coursesData = await coursesRes.json();

            // Update states
            if (statsRes.ok) {
                setStats(statsData);
                setLoading(prev => ({ ...prev, stats: false }));
            }

            if (usersRes.ok) {
                setRecentUsers(usersData.users || []);
                setLoading(prev => ({ ...prev, users: false }));
            }

            if (paymentsRes.ok) {
                setRecentPayments(paymentsData.payments || []);
                setLoading(prev => ({ ...prev, payments: false }));
            }

            if (coursesRes.ok) {
                setRecentCourses(coursesData.courses || []);
                setLoading(prev => ({ ...prev, courses: false }));
            }

        } catch (error) {
            console.error('Error fetching dashboard data:', error);
            setError('Có lỗi xảy ra khi tải dữ liệu');
            setLoading({
                stats: false,
                users: false,
                payments: false,
                courses: false
            });
        }
    };

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('vi-VN').format(amount);
    };

    return (
        <div>
            {/* Page Header */}
            <div className="page-header mb-4">
                <h1>
                    <i className="fas fa-tachometer-alt text-primary me-2"></i>
                    Bảng điều khiển
                </h1>
                <p className="page-description text-muted">
                    Tổng quan về dữ liệu hệ thống
                </p>
            </div>

            {error && (
                <div className="alert alert-danger" role="alert">
                    <i className="fas fa-exclamation-circle me-2"></i>
                    {error}
                </div>
            )}

            {/* Dashboard Stats */}
            <div className="row mb-4">
                <div className="col-xl-3 col-md-6 mb-4">
                    {loading.stats ? (
                        <div className="card h-100">
                            <div className="card-body">
                                <div className="spinner-border spinner-border-sm text-primary" role="status">
                                    <span className="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    ) : (
                        <StatCard
                            title="Người dùng"
                            value={stats?.userCount || 0}
                            icon="fas fa-users"
                            iconColor="primary"
                            borderColor="primary"
                            footerLink="/admin/users"
                            subText={
                                <>
                                    <i className="fas fa-user-plus me-1"></i>
                                    {stats?.userCountThisMonth || 0} người dùng mới tháng này
                                </>
                            }
                        />
                    )}
                </div>

                <div className="col-xl-3 col-md-6 mb-4">
                    {loading.stats ? (
                        <div className="card h-100">
                            <div className="card-body">
                                <div className="spinner-border spinner-border-sm text-success" role="status">
                                    <span className="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    ) : (
                        <StatCard
                            title="Công thức"
                            value={stats?.recipeCount || 0}
                            icon="fas fa-utensils"
                            iconColor="success"
                            borderColor="success"
                            footerLink="/recipes"
                        />
                    )}
                </div>

                <div className="col-xl-3 col-md-6 mb-4">
                    {loading.stats ? (
                        <div className="card h-100">
                            <div className="card-body">
                                <div className="spinner-border spinner-border-sm text-info" role="status">
                                    <span className="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    ) : (
                        <StatCard
                            title="Khóa học"
                            value={stats?.courseCount || 0}
                            icon="fas fa-chalkboard-teacher"
                            iconColor="info"
                            borderColor="info"
                            footerLink="/admin/courses"
                        />
                    )}
                </div>

                <div className="col-xl-3 col-md-6 mb-4">
                    {loading.stats ? (
                        <div className="card h-100">
                            <div className="card-body">
                                <div className="spinner-border spinner-border-sm text-warning" role="status">
                                    <span className="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    ) : (
                        <StatCard
                            title="Doanh thu"
                            value={`${formatCurrency(stats?.totalRevenue || 0)}đ`}
                            icon="fas fa-money-bill-wave"
                            iconColor="warning"
                            borderColor="warning"
                            footerLink="/admin/payments"
                        />
                    )}
                </div>
            </div>

            {/* Recent Activities */}
            <div className="row mb-4">
                {/* Recent Users */}
                <div className="col-lg-6 mb-4">
                    <div className="card">
                        <div className="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i className="fas fa-users me-2"></i>
                                <span className="fw-bold">Người dùng mới</span>
                            </div>
                            <Link to="/admin/users" className="btn btn-sm btn-primary">
                                <i className="fas fa-users me-1"></i> Tất cả người dùng
                            </Link>
                        </div>
                        <div className="card-body">
                            <RecentUsersTable users={recentUsers} loading={loading.users} />
                        </div>
                    </div>
                </div>

                {/* Recent Payments */}
                <div className="col-lg-6 mb-4">
                    <div className="card">
                        <div className="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i className="fas fa-money-bill-wave me-2"></i>
                                <span className="fw-bold">Thanh toán gần đây</span>
                            </div>
                            <Link to="/admin/payments" className="btn btn-sm btn-primary">
                                <i className="fas fa-receipt me-1"></i> Tất cả thanh toán
                            </Link>
                        </div>
                        <div className="card-body">
                            <RecentPaymentsTable payments={recentPayments} loading={loading.payments} />
                        </div>
                    </div>
                </div>
            </div>

            {/* Latest Courses */}
            <div className="row">
                <div className="col-12 mb-4">
                    <div className="card">
                        <div className="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i className="fas fa-chalkboard-teacher me-2"></i>
                                <span className="fw-bold">Khóa học mới nhất</span>
                            </div>
                            <Link to="/admin/courses" className="btn btn-sm btn-primary">
                                <i className="fas fa-graduation-cap me-1"></i> Tất cả khóa học
                            </Link>
                        </div>
                        <div className="card-body">
                            {loading.courses ? (
                                <div className="text-center py-5">
                                    <div className="spinner-border text-primary" role="status">
                                        <span className="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            ) : recentCourses.length === 0 ? (
                                <div className="alert alert-info">
                                    <i className="fas fa-info-circle me-2"></i>
                                    Chưa có khóa học nào.
                                </div>
                            ) : (
                                <div className="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                    {recentCourses.map(course => (
                                        <div key={course.id} className="col">
                                            <CourseCard course={course} />
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .page-header h1 {
                    font-size: 1.75rem;
                    font-weight: 600;
                    margin-bottom: 10px;
                    color: #2d3748;
                }

                .page-description {
                    font-size: 1rem;
                }

                .card {
                    border: none;
                    border-radius: 8px;
                    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
                    transition: all 0.2s ease;
                }

                .card:hover {
                    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
                }

                .card-header {
                    border-radius: 8px 8px 0 0;
                    font-weight: 500;
                    padding: 15px 20px;
                    background-color: #fff;
                    border-bottom: 1px solid rgba(0,0,0,.075);
                }
            `}</style>
        </div>
    );
}
