import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function ManagerDashboardPage() {
    const [stats, setStats] = useState({});
    const [recentContacts, setRecentContacts] = useState([]);
    const [loading, setLoading] = useState(true);
    const { token, user } = useAuth();

    useEffect(() => { fetchDashboardData(); }, []);

    const fetchDashboardData = async () => {
        try {
            const headers = { 'Authorization': `Bearer ${token}` };
            const [statsRes, contactsRes] = await Promise.all([
                fetch('/api/v1/manager/dashboard/stats', { headers }),
                fetch('/api/v1/manager/dashboard/recent-contacts', { headers }),
            ]);
            
            const statsData = await statsRes.json();
            const contactsData = await contactsRes.json();
            
            setStats(statsData);
            setRecentContacts(contactsData.contacts || []);
        } catch (e) { console.error(e); }
        finally { setLoading(false); }
    };

    if (loading) {
        return <div className="text-center py-5"><div className="spinner-border text-success"></div></div>;
    }

    return (
        <div>
            {/* Page Header */}
            <div className="page-header">
                <h1><i className="fas fa-tachometer-alt text-success me-2"></i>Manager Dashboard</h1>
                <p className="page-description">Tổng quan quản lý nội dung và khóa học</p>
            </div>

            {/* Dashboard Stats */}
            <div className="row">
                <div className="col-xl-3 col-md-6 mb-4">
                    <div className="card border-left-success h-100 stat-card">
                        <div className="card-body">
                            <div className="d-flex justify-content-between align-items-center">
                                <div>
                                    <div className="text-xs text-uppercase mb-1 text-muted fw-bold">Tổng Công Thức</div>
                                    <div className="h4 mb-0 fw-bold">{stats.total_recipes || 0}</div>
                                    <div className="small text-success mt-1"><i className="fas fa-utensils me-1"></i>Đã phê duyệt</div>
                                </div>
                                <div className="p-3 rounded-circle bg-success bg-opacity-10">
                                    <i className="fas fa-utensils fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                        <div className="card-footer bg-light py-2">
                            <Link to="/manager/recipes" className="text-decoration-none small text-success fw-bold">
                                <i className="fas fa-arrow-right me-1"></i> Quản lý công thức
                            </Link>
                        </div>
                    </div>
                </div>

                <div className="col-xl-3 col-md-6 mb-4">
                    <div className="card border-left-warning h-100 stat-card">
                        <div className="card-body">
                            <div className="d-flex justify-content-between align-items-center">
                                <div>
                                    <div className="text-xs text-uppercase mb-1 text-muted fw-bold">Chờ Duyệt</div>
                                    <div className="h4 mb-0 fw-bold">{stats.pending_recipes || 0}</div>
                                    <div className="small text-warning mt-1"><i className="fas fa-clock me-1"></i>Cần xem xét</div>
                                </div>
                                <div className="p-3 rounded-circle bg-warning bg-opacity-10">
                                    <i className="fas fa-clock fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                        <div className="card-footer bg-light py-2">
                            <Link to="/manager/recipes?status=pending" className="text-decoration-none small text-warning fw-bold">
                                <i className="fas fa-arrow-right me-1"></i> Duyệt công thức
                            </Link>
                        </div>
                    </div>
                </div>

                <div className="col-xl-3 col-md-6 mb-4">
                    <div className="card border-left-info h-100 stat-card">
                        <div className="card-body">
                            <div className="d-flex justify-content-between align-items-center">
                                <div>
                                    <div className="text-xs text-uppercase mb-1 text-muted fw-bold">Khóa Học</div>
                                    <div className="h4 mb-0 fw-bold">{stats.total_courses || 0}</div>
                                    <div className="small text-info mt-1"><i className="fas fa-graduation-cap me-1"></i>Đang hoạt động</div>
                                </div>
                                <div className="p-3 rounded-circle bg-info bg-opacity-10">
                                    <i className="fas fa-graduation-cap fa-2x text-info"></i>
                                </div>
                            </div>
                        </div>
                        <div className="card-footer bg-light py-2">
                            <Link to="/manager/courses" className="text-decoration-none small text-info fw-bold">
                                <i className="fas fa-arrow-right me-1"></i> Quản lý khóa học
                            </Link>
                        </div>
                    </div>
                </div>

                <div className="col-xl-3 col-md-6 mb-4">
                    <div className="card border-left-primary h-100 stat-card">
                        <div className="card-body">
                            <div className="d-flex justify-content-between align-items-center">
                                <div>
                                    <div className="text-xs text-uppercase mb-1 text-muted fw-bold">Học Viên</div>
                                    <div className="h4 mb-0 fw-bold">{stats.total_students || 0}</div>
                                    <div className="small text-primary mt-1"><i className="fas fa-users me-1"></i>Đang học</div>
                                </div>
                                <div className="p-3 rounded-circle bg-primary bg-opacity-10">
                                    <i className="fas fa-users fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                        <div className="card-footer bg-light py-2">
                            <Link to="/manager/reports" className="text-decoration-none small text-primary fw-bold">
                                <i className="fas fa-arrow-right me-1"></i> Xem báo cáo
                            </Link>
                        </div>
                    </div>
                </div>
            </div>


            {/* Quick Actions */}
            <div className="row mb-4">
                <div className="col-12">
                    <div className="card">
                        <div className="card-header bg-success text-white">
                            <h5 className="mb-0"><i className="fas fa-bolt me-2"></i>Thao Tác Nhanh</h5>
                        </div>
                        <div className="card-body">
                            <div className="row">
                                <div className="col-lg-3 col-md-6 mb-3">
                                    <div className="d-grid">
                                        <Link to="/manager/recipes" className="btn btn-outline-success quick-action">
                                            <i className="fas fa-utensils me-2"></i>Duyệt Công Thức
                                        </Link>
                                    </div>
                                </div>
                                <div className="col-lg-3 col-md-6 mb-3">
                                    <div className="d-grid">
                                        <Link to="/manager/courses" className="btn btn-outline-info quick-action">
                                            <i className="fas fa-graduation-cap me-2"></i>Quản Lý Khóa Học
                                        </Link>
                                    </div>
                                </div>
                                <div className="col-lg-3 col-md-6 mb-3">
                                    <div className="d-grid">
                                        <Link to="/manager/contacts" className="btn btn-outline-warning quick-action">
                                            <i className="fas fa-envelope me-2"></i>Xem Liên Hệ
                                        </Link>
                                    </div>
                                </div>
                                <div className="col-lg-3 col-md-6 mb-3">
                                    <div className="d-grid">
                                        <Link to="/manager/reports" className="btn btn-outline-primary quick-action">
                                            <i className="fas fa-chart-bar me-2"></i>Báo Cáo
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Content Area */}
            <div className="row">
                {/* Recent Contacts */}
                <div className="col-lg-6 mb-4">
                    <div className="card">
                        <div className="card-header d-flex justify-content-between align-items-center">
                            <h5 className="mb-0"><i className="fas fa-envelope me-2"></i>Liên Hệ Gần Đây</h5>
                            <span className="badge bg-warning">{recentContacts.length} mới</span>
                        </div>
                        <div className="card-body p-0">
                            {recentContacts.length === 0 ? (
                                <div className="text-center py-4">
                                    <i className="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p className="text-muted">Chưa có liên hệ mới</p>
                                </div>
                            ) : (
                                <div className="list-group list-group-flush">
                                    {recentContacts.map(contact => (
                                        <div key={contact.id} className="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                                            <div className="ms-2 me-auto">
                                                <div className="fw-bold">{contact.name}</div>
                                                <div className="text-muted small">{contact.subject}</div>
                                                <div className="text-muted small">
                                                    <i className="fas fa-clock me-1"></i>
                                                    {new Date(contact.created_at).toLocaleString('vi-VN')}
                                                </div>
                                            </div>
                                            <span className={`badge bg-${contact.status === 'new' ? 'danger' : 'success'} rounded-pill`}>
                                                {contact.status === 'new' ? 'Mới' : 'Đã đọc'}
                                            </span>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                        {recentContacts.length > 0 && (
                            <div className="card-footer text-center">
                                <Link to="/manager/contacts" className="btn btn-sm btn-outline-primary">
                                    <i className="fas fa-eye me-1"></i> Xem Tất Cả
                                </Link>
                            </div>
                        )}
                    </div>
                </div>

                {/* Manager Info */}
                <div className="col-lg-6 mb-4">
                    <div className="card">
                        <div className="card-header">
                            <h5 className="mb-0"><i className="fas fa-user-shield me-2"></i>Thông Tin Quản Lý</h5>
                        </div>
                        <div className="card-body">
                            <div className="row mb-3">
                                <div className="col-6">
                                    <div className="text-muted small">Tên:</div>
                                    <div className="fw-bold">{user?.name || 'Manager'}</div>
                                </div>
                                <div className="col-6">
                                    <div className="text-muted small">Role:</div>
                                    <div className="fw-bold text-success">
                                        <i className="fas fa-user-tie me-1"></i>Manager
                                    </div>
                                </div>
                            </div>
                            
                            <div className="row mb-3">
                                <div className="col-6">
                                    <div className="text-muted small">Email:</div>
                                    <div className="fw-bold">{user?.email || 'manager@domain.com'}</div>
                                </div>
                                <div className="col-6">
                                    <div className="text-muted small">Trạng thái:</div>
                                    <div className="fw-bold text-success">
                                        <i className="fas fa-circle me-1" style={{ fontSize: '8px' }}></i>Đang hoạt động
                                    </div>
                                </div>
                            </div>
                            
                            <hr />
                            
                            <div className="mb-3">
                                <div className="text-muted small mb-2">Quyền truy cập:</div>
                                <div className="row">
                                    <div className="col-6">
                                        <div className="text-success small"><i className="fas fa-check me-1"></i>Quản lý công thức</div>
                                        <div className="text-success small"><i className="fas fa-check me-1"></i>Quản lý khóa học</div>
                                        <div className="text-success small"><i className="fas fa-check me-1"></i>Xem liên hệ</div>
                                    </div>
                                    <div className="col-6">
                                        <div className="text-muted small"><i className="fas fa-times me-1"></i>Quản lý user</div>
                                        <div className="text-muted small"><i className="fas fa-times me-1"></i>Cài đặt hệ thống</div>
                                        <div className="text-muted small"><i className="fas fa-times me-1"></i>Xóa dữ liệu</div>
                                    </div>
                                </div>
                            </div>

                            <div className="text-center">
                                <Link to="/profile" className="btn btn-sm btn-outline-success me-2">
                                    <i className="fas fa-user-edit me-1"></i>Cập nhật profile
                                </Link>
                                <Link to="/" className="btn btn-sm btn-outline-primary">
                                    <i className="fas fa-home me-1"></i>Về trang chủ
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .page-header {
                    background: white;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    margin-bottom: 20px;
                    border-left: 4px solid #198754;
                }
                .page-header h1 { margin: 0; font-size: 1.5rem; font-weight: 600; }
                .page-description { margin: 5px 0 0 0; color: #6c757d; }
                .border-left-primary { border-left: 4px solid #0d6efd !important; }
                .border-left-success { border-left: 4px solid #198754 !important; }
                .border-left-info { border-left: 4px solid #0dcaf0 !important; }
                .border-left-warning { border-left: 4px solid #ffc107 !important; }
                .stat-card { transition: all 0.3s ease; }
                .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
                .quick-action { transition: all 0.3s ease; }
                .quick-action:hover { transform: translateY(-1px); }
            `}</style>
        </div>
    );
}
