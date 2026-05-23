import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function ManagerReportsPage() {
    const [stats, setStats] = useState(null);
    const [loading, setLoading] = useState(true);
    const { token } = useAuth();

    useEffect(() => { 
        if (token) fetchStats(); 
    }, [token]);

    const fetchStats = async () => {
        try {
            const res = await fetch('/api/v1/manager/reports/overview', { 
                headers: { 'Authorization': `Bearer ${token}` } 
            });
            if (!res.ok) throw new Error('API Error');
            const data = await res.json();
            setStats(data.stats);
        } catch (e) { console.error('Manager Reports Error:', e); }
        finally { setLoading(false); }
    };

    const formatCurrency = (amount) => new Intl.NumberFormat('vi-VN').format(amount || 0) + '₫';

    if (loading) return <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>;

    const recipes = stats?.recipes || { total: 0, approved: 0, pending: 0, rejected: 0 };
    const courses = stats?.courses || { total: 0, published: 0, draft: 0 };
    const users = stats?.users || { total: 0, this_month: 0 };
    const revenue = stats?.revenue || { total: 0, this_month: 0 };

    const recipeTotal = recipes.total || 1;
    const approvedPct = (recipes.approved / recipeTotal) * 100;
    const pendingPct = (recipes.pending / recipeTotal) * 100;
    const rejectedPct = (recipes.rejected / recipeTotal) * 100;

    return (
        <div>
            {/* Page Header */}
            <div className="page-header">
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        <h1><i className="fas fa-chart-bar text-primary me-2"></i>Báo Cáo & Thống Kê</h1>
                        <p className="page-description">Tổng quan về hoạt động và hiệu suất hệ thống</p>
                    </div>
                    <div>
                        <Link to="/manager" className="btn btn-outline-secondary">
                            <i className="fas fa-arrow-left me-1"></i> Về Dashboard
                        </Link>
                    </div>
                </div>
            </div>

            {/* Overview Stats */}
            <div className="row mb-4">
                {/* Recipes Stats */}
                <div className="col-lg-6 mb-4">
                    <div className="card h-100">
                        <div className="card-header bg-success text-white">
                            <h5 className="mb-0"><i className="fas fa-utensils me-2"></i>Thống Kê Công Thức</h5>
                        </div>
                        <div className="card-body">
                            <div className="row text-center">
                                <div className="col-3">
                                    <div className="h4 text-success mb-0">{recipes.total}</div>
                                    <div className="small text-muted">Tổng số</div>
                                </div>
                                <div className="col-3">
                                    <div className="h4 text-info mb-0">{recipes.approved}</div>
                                    <div className="small text-muted">Đã duyệt</div>
                                </div>
                                <div className="col-3">
                                    <div className="h4 text-warning mb-0">{recipes.pending}</div>
                                    <div className="small text-muted">Chờ duyệt</div>
                                </div>
                                <div className="col-3">
                                    <div className="h4 text-danger mb-0">{recipes.rejected}</div>
                                    <div className="small text-muted">Từ chối</div>
                                </div>
                            </div>
                            <hr />
                            <div className="progress" style={{ height: '10px' }}>
                                <div className="progress-bar bg-info" style={{ width: `${approvedPct}%` }}></div>
                                <div className="progress-bar bg-warning" style={{ width: `${pendingPct}%` }}></div>
                                <div className="progress-bar bg-danger" style={{ width: `${rejectedPct}%` }}></div>
                            </div>
                            <div className="text-center mt-2">
                                <small className="text-muted">
                                    <span className="text-info">■</span> Đã duyệt 
                                    <span className="text-warning ms-2">■</span> Chờ duyệt 
                                    <span className="text-danger ms-2">■</span> Từ chối
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Courses Stats */}
                <div className="col-lg-6 mb-4">
                    <div className="card h-100">
                        <div className="card-header bg-info text-white">
                            <h5 className="mb-0"><i className="fas fa-graduation-cap me-2"></i>Thống Kê Khóa Học</h5>
                        </div>
                        <div className="card-body">
                            <div className="row text-center">
                                <div className="col-4">
                                    <div className="h4 text-info mb-0">{courses.total}</div>
                                    <div className="small text-muted">Tổng số</div>
                                </div>
                                <div className="col-4">
                                    <div className="h4 text-success mb-0">{courses.published}</div>
                                    <div className="small text-muted">Hoạt động</div>
                                </div>
                                <div className="col-4">
                                    <div className="h4 text-warning mb-0">{courses.draft}</div>
                                    <div className="small text-muted">Bản nháp</div>
                                </div>
                            </div>
                            <hr />
                            <div className="d-flex justify-content-between align-items-center">
                                <div>
                                    <div className="h5 text-primary mb-0">{users.total}</div>
                                    <div className="small text-muted">Tổng học viên</div>
                                </div>
                                <div className="text-end">
                                    <div className="h5 text-success mb-0">+{users.this_month}</div>
                                    <div className="small text-muted">Mới tháng này</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Revenue Stats */}
            <div className="row mb-4">
                <div className="col-lg-8 mb-4">
                    <div className="card">
                        <div className="card-header">
                            <h5 className="mb-0"><i className="fas fa-money-bill-wave me-2"></i>Doanh Thu</h5>
                        </div>
                        <div className="card-body">
                            <div className="row">
                                <div className="col-md-6">
                                    <div className="bg-light p-3 rounded mb-3">
                                        <div className="d-flex align-items-center">
                                            <div className="bg-success text-white rounded-circle p-3 me-3 icon-circle">
                                                <i className="fas fa-coins fa-2x"></i>
                                            </div>
                                            <div>
                                                <div className="h4 mb-0 text-success">{formatCurrency(revenue.total)}</div>
                                                <div className="text-muted">Tổng doanh thu</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-md-6">
                                    <div className="bg-light p-3 rounded mb-3">
                                        <div className="d-flex align-items-center">
                                            <div className="bg-info text-white rounded-circle p-3 me-3 icon-circle">
                                                <i className="fas fa-calendar-alt fa-2x"></i>
                                            </div>
                                            <div>
                                                <div className="h4 mb-0 text-info">{formatCurrency(revenue.this_month)}</div>
                                                <div className="text-muted">Tháng {new Date().getMonth() + 1}/{new Date().getFullYear()}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="alert alert-info mb-0">
                                <i className="fas fa-info-circle me-2"></i>
                                <strong>Lưu ý:</strong> Manager chỉ có thể xem báo cáo doanh thu tổng quan. 
                                Để xem chi tiết, vui lòng liên hệ Admin.
                            </div>
                        </div>
                    </div>
                </div>

                {/* Quick Actions */}
                <div className="col-lg-4 mb-4">
                    <div className="card">
                        <div className="card-header">
                            <h5 className="mb-0"><i className="fas fa-tasks me-2"></i>Thao Tác Nhanh</h5>
                        </div>
                        <div className="card-body">
                            <div className="d-grid gap-2">
                                <Link to="/manager/recipes" className="btn btn-outline-success">
                                    <i className="fas fa-utensils me-2"></i>Duyệt công thức
                                    {recipes.pending > 0 && <span className="badge bg-warning ms-2">{recipes.pending}</span>}
                                </Link>
                                <Link to="/manager/courses" className="btn btn-outline-info">
                                    <i className="fas fa-graduation-cap me-2"></i>Quản lý khóa học
                                </Link>
                                <Link to="/manager/contacts" className="btn btn-outline-warning">
                                    <i className="fas fa-envelope me-2"></i>Xem liên hệ
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* System Info */}
            <div className="card">
                <div className="card-header">
                    <h5 className="mb-0"><i className="fas fa-cogs me-2"></i>Thông Tin Hệ Thống</h5>
                </div>
                <div className="card-body">
                    <div className="row mb-4">
                        <div className="col-md-3 mb-3 text-center">
                            <div className="h5 mb-0">v1.0.0</div>
                            <div className="text-muted small">Phiên bản</div>
                        </div>
                        <div className="col-md-3 mb-3 text-center">
                            <div className="h5 mb-0 text-success"><i className="fas fa-circle"></i> Online</div>
                            <div className="text-muted small">Trạng thái</div>
                        </div>
                        <div className="col-md-3 mb-3 text-center">
                            <div className="h5 mb-0">{new Date().toLocaleDateString('vi-VN')}</div>
                            <div className="text-muted small">Cập nhật lần cuối</div>
                        </div>
                        <div className="col-md-3 mb-3 text-center">
                            <div className="h5 mb-0 text-info">Manager</div>
                            <div className="text-muted small">Quyền truy cập</div>
                        </div>
                    </div>
                    <hr />
                    <div className="row">
                        <div className="col-md-6">
                            <h6 className="text-success"><i className="fas fa-check-circle me-1"></i>Quyền được cấp:</h6>
                            <ul className="list-unstyled">
                                <li><i className="fas fa-check text-success me-2"></i>Duyệt/từ chối công thức</li>
                                <li><i className="fas fa-check text-success me-2"></i>Quản lý khóa học</li>
                                <li><i className="fas fa-check text-success me-2"></i>Xem liên hệ khách hàng</li>
                                <li><i className="fas fa-check text-success me-2"></i>Xem báo cáo tổng quan</li>
                                <li><i className="fas fa-check text-success me-2"></i>Tạo nội dung mới</li>
                            </ul>
                        </div>
                        <div className="col-md-6">
                            <h6 className="text-muted"><i className="fas fa-times-circle me-1"></i>Quyền hạn chế:</h6>
                            <ul className="list-unstyled">
                                <li><i className="fas fa-times text-muted me-2"></i>Quản lý người dùng</li>
                                <li><i className="fas fa-times text-muted me-2"></i>Xem chi tiết thanh toán</li>
                                <li><i className="fas fa-times text-muted me-2"></i>Cài đặt hệ thống</li>
                                <li><i className="fas fa-times text-muted me-2"></i>Xóa dữ liệu quan trọng</li>
                                <li><i className="fas fa-times text-muted me-2"></i>Phân quyền người dùng</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .page-header { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; border-left: 4px solid #0d6efd; }
                .page-header h1 { margin: 0; font-size: 1.5rem; font-weight: 600; }
                .page-description { margin: 5px 0 0 0; color: #6c757d; }
                .progress { background-color: #e9ecef; }
                .card { transition: transform 0.2s ease; }
                .card:hover { transform: translateY(-2px); }
                .icon-circle { width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; }
                .alert-info { background-color: rgba(13, 202, 240, 0.1); border-color: rgba(13, 202, 240, 0.2); }
            `}</style>
        </div>
    );
}
