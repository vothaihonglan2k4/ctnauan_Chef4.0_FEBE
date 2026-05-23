import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function ManagerCoursesPage() {
    const [courses, setCourses] = useState([]);
    const [classrooms, setClassrooms] = useState([]);
    const [stats, setStats] = useState({});
    const [loading, setLoading] = useState(true);
    const [showModal, setShowModal] = useState(false);
    const [editingCourse, setEditingCourse] = useState(null);
    const [formData, setFormData] = useState({ title: '', description: '', classroom_id: '', price: '', duration: '', level: 'beginner', status: 'draft' });
    const [successMessage, setSuccessMessage] = useState('');
    const { token } = useAuth();

    useEffect(() => { fetchCourses(); fetchClassrooms(); }, []);

    const fetchCourses = async () => {
        try {
            const res = await fetch('/api/v1/manager/courses', { headers: { 'Authorization': `Bearer ${token}` } });
            const data = await res.json();
            setCourses(data.courses || []);
            setStats(data.stats || {});
        } catch (e) { console.error(e); }
        finally { setLoading(false); }
    };

    const fetchClassrooms = async () => {
        const res = await fetch('/api/v1/manager/courses/classrooms', { headers: { 'Authorization': `Bearer ${token}` } });
        const data = await res.json();
        setClassrooms(data.classrooms || []);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const url = editingCourse ? `/api/v1/manager/courses/${editingCourse.id}` : '/api/v1/manager/courses';
        const method = editingCourse ? 'PUT' : 'POST';
        
        const res = await fetch(url, {
            method, headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        if (res.ok) {
            setSuccessMessage(editingCourse ? 'Cập nhật khóa học thành công!' : 'Thêm khóa học thành công!');
            setShowModal(false);
            resetForm();
            fetchCourses();
            setTimeout(() => setSuccessMessage(''), 3000);
        }
    };

    const handleDelete = async (id, title, studentCount) => {
        if (studentCount > 0) { alert(`Không thể xóa khóa học "${title}" vì đang có ${studentCount} học viên`); return; }
        if (!confirm(`Xóa khóa học "${title}"?`)) return;
        await fetch(`/api/v1/manager/courses/${id}`, { method: 'DELETE', headers: { 'Authorization': `Bearer ${token}` } });
        setSuccessMessage('Xóa khóa học thành công!');
        fetchCourses();
        setTimeout(() => setSuccessMessage(''), 3000);
    };

    const handleUpdateStatus = async (id, status, title) => {
        const statusText = { published: 'xuất bản', draft: 'chuyển về nháp', archived: 'lưu trữ' };
        if (!confirm(`Bạn có chắc muốn ${statusText[status]} khóa học "${title}"?`)) return;
        const res = await fetch(`/api/v1/manager/courses/${id}/status`, {
            method: 'PUT', headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({ status })
        });
        if (res.ok) { setSuccessMessage(`Khóa học đã được ${statusText[status]}`); fetchCourses(); setTimeout(() => setSuccessMessage(''), 3000); }
    };

    const openAdd = () => { setEditingCourse(null); resetForm(); setShowModal(true); };
    const openEdit = (c) => { setEditingCourse(c); setFormData({ title: c.title, description: c.description || '', classroom_id: c.classroom_id, price: c.price, duration: c.duration || '', level: c.level || 'beginner', status: c.status }); setShowModal(true); };
    const resetForm = () => setFormData({ title: '', description: '', classroom_id: '', price: '', duration: '', level: 'beginner', status: 'draft' });
    const formatCurrency = (amount) => new Intl.NumberFormat('vi-VN').format(amount) + '₫';

    if (loading) return <div className="text-center py-5"><div className="spinner-border text-success"></div></div>;


    return (
        <div>
            {/* Page Header */}
            <div className="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h1><i className="fas fa-graduation-cap text-info me-2"></i>Quản lý Khóa học</h1>
                    <p className="page-description">Tạo và quản lý các khóa học trực tuyến</p>
                </div>
                <div>
                    <Link to="/manager" className="btn btn-outline-secondary me-2"><i className="fas fa-arrow-left me-1"></i> Về Dashboard</Link>
                    <button className="btn btn-success" onClick={openAdd}><i className="fas fa-plus-circle me-2"></i>Thêm khóa học mới</button>
                </div>
            </div>

            {successMessage && <div className="alert alert-success"><i className="fas fa-check-circle me-2"></i>{successMessage}</div>}

            {/* Statistics */}
            <div className="row mb-4">
                {[
                    { icon: 'graduation-cap', color: 'info', value: stats.total || 0, label: 'Tổng khóa học' },
                    { icon: 'check-circle', color: 'success', value: stats.published || 0, label: 'Đang hoạt động' },
                    { icon: 'edit', color: 'warning', value: stats.draft || 0, label: 'Bản nháp' },
                    { icon: 'users', color: 'primary', value: stats.total_students || 0, label: 'Học viên' },
                ].map((s, i) => (
                    <div key={i} className="col-md-3">
                        <div className={`card border-left-${s.color}`}>
                            <div className="card-body text-center">
                                <i className={`fas fa-${s.icon} fa-2x text-${s.color} mb-2`}></i>
                                <div className="h4 mb-0">{s.value}</div>
                                <div className="small text-muted">{s.label}</div>
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {/* Courses List */}
            <div className="card">
                <div className="card-header d-flex justify-content-between"><span><i className="fas fa-list me-2"></i>Danh sách khóa học</span><span className="badge bg-info">{courses.length} khóa học</span></div>
                <div className="card-body">
                    {courses.length === 0 ? (
                        <div className="text-center py-5"><i className="fas fa-graduation-cap fa-4x text-muted mb-3"></i><h5>Chưa có khóa học nào</h5><button className="btn btn-info mt-2" onClick={openAdd}><i className="fas fa-plus me-1"></i>Tạo khóa học ngay</button></div>
                    ) : (
                        <div className="row">
                            {courses.map(course => (
                                <div key={course.id} className="col-lg-6 col-xl-4 mb-4">
                                    <div className="card h-100 course-card">
                                        <div className="position-relative overflow-hidden">
                                            <img src={course.image ? `/uploads/courses/${course.image}` : '/img/default-course.jpg'} className="card-img-top" style={{ height: '180px', objectFit: 'cover' }} alt={course.title} />
                                            <span className={`position-absolute top-0 end-0 m-2 badge bg-${course.status === 'published' ? 'success' : course.status === 'draft' ? 'warning' : 'secondary'}`}>
                                                {course.status === 'published' ? 'Hoạt động' : course.status === 'draft' ? 'Nháp' : 'Lưu trữ'}
                                            </span>
                                            {course.level && <span className="position-absolute top-0 start-0 m-2 badge bg-dark">{course.level === 'beginner' ? 'Cơ bản' : course.level === 'intermediate' ? 'Trung cấp' : 'Nâng cao'}</span>}
                                        </div>
                                        <div className="card-body d-flex flex-column">
                                            <h5 className="card-title">{course.title}</h5>
                                            <p className="card-text text-muted flex-grow-1">{course.description?.substring(0, 80)}...</p>
                                            <div className="row text-center mb-3">
                                                <div className="col-4"><div className="text-muted small">Giá</div><div className="fw-bold text-success">{formatCurrency(course.price)}</div></div>
                                                <div className="col-4"><div className="text-muted small">Học viên</div><div className="fw-bold text-primary">{course.student_count || 0}</div></div>
                                                <div className="col-4"><div className="text-muted small">Bài học</div><div className="fw-bold text-info">{course.lesson_count || 0}</div></div>
                                            </div>
                                            <div className="small text-muted">
                                                <div><i className="fas fa-clock me-1"></i>{course.duration || 0} phút</div>
                                                <div><i className="fas fa-chalkboard me-1"></i>{course.classroom?.name || 'N/A'}</div>
                                            </div>
                                        </div>
                                        <div className="card-footer bg-light">
                                            <div className="d-flex justify-content-between">
                                                <div>
                                                    <Link to={`/courses/${course.id}`} target="_blank" className="btn btn-sm btn-outline-primary me-1"><i className="fas fa-eye"></i></Link>
                                                    <button className="btn btn-sm btn-outline-secondary me-1" onClick={() => openEdit(course)}><i className="fas fa-edit"></i></button>
                                                    <button className="btn btn-sm btn-outline-danger" onClick={() => handleDelete(course.id, course.title, course.student_count)}><i className="fas fa-trash"></i></button>
                                                </div>
                                                <div className="dropdown">
                                                    <button className="btn btn-sm btn-outline-warning dropdown-toggle" data-bs-toggle="dropdown"><i className="fas fa-cog"></i></button>
                                                    <ul className="dropdown-menu dropdown-menu-end">
                                                        {course.status !== 'published' && <li><button className="dropdown-item text-success" onClick={() => handleUpdateStatus(course.id, 'published', course.title)}><i className="fas fa-play me-2"></i>Xuất bản</button></li>}
                                                        {course.status !== 'draft' && <li><button className="dropdown-item text-warning" onClick={() => handleUpdateStatus(course.id, 'draft', course.title)}><i className="fas fa-edit me-2"></i>Chuyển nháp</button></li>}
                                                        {course.status !== 'archived' && <li><button className="dropdown-item text-secondary" onClick={() => handleUpdateStatus(course.id, 'archived', course.title)}><i className="fas fa-archive me-2"></i>Lưu trữ</button></li>}
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>


            {/* Add/Edit Modal */}
            {showModal && (
                <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog modal-lg">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5>{editingCourse ? 'Sửa khóa học' : 'Thêm khóa học mới'}</h5>
                                <button className="btn-close" onClick={() => setShowModal(false)}></button>
                            </div>
                            <form onSubmit={handleSubmit}>
                                <div className="modal-body">
                                    <div className="mb-3">
                                        <label className="form-label">Tên khóa học *</label>
                                        <input className="form-control" value={formData.title} onChange={e => setFormData({...formData, title: e.target.value})} required />
                                    </div>
                                    <div className="mb-3">
                                        <label className="form-label">Mô tả</label>
                                        <textarea className="form-control" rows="3" value={formData.description} onChange={e => setFormData({...formData, description: e.target.value})} />
                                    </div>
                                    <div className="row">
                                        <div className="col-md-6 mb-3">
                                            <label className="form-label">Phòng học *</label>
                                            <select className="form-select" value={formData.classroom_id} onChange={e => setFormData({...formData, classroom_id: e.target.value})} required>
                                                <option value="">-- Chọn phòng học --</option>
                                                {classrooms.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                                            </select>
                                        </div>
                                        <div className="col-md-6 mb-3">
                                            <label className="form-label">Giá (VNĐ) *</label>
                                            <input type="number" className="form-control" value={formData.price} onChange={e => setFormData({...formData, price: e.target.value})} required />
                                        </div>
                                    </div>
                                    <div className="row">
                                        <div className="col-md-4 mb-3">
                                            <label className="form-label">Thời lượng (phút)</label>
                                            <input type="number" className="form-control" value={formData.duration} onChange={e => setFormData({...formData, duration: e.target.value})} />
                                        </div>
                                        <div className="col-md-4 mb-3">
                                            <label className="form-label">Cấp độ</label>
                                            <select className="form-select" value={formData.level} onChange={e => setFormData({...formData, level: e.target.value})}>
                                                <option value="beginner">Cơ bản</option>
                                                <option value="intermediate">Trung cấp</option>
                                                <option value="advanced">Nâng cao</option>
                                            </select>
                                        </div>
                                        <div className="col-md-4 mb-3">
                                            <label className="form-label">Trạng thái</label>
                                            <select className="form-select" value={formData.status} onChange={e => setFormData({...formData, status: e.target.value})}>
                                                <option value="draft">Bản nháp</option>
                                                <option value="published">Xuất bản</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div className="modal-footer">
                                    <button type="button" className="btn btn-secondary" onClick={() => setShowModal(false)}>Hủy</button>
                                    <button type="submit" className="btn btn-success">{editingCourse ? 'Cập nhật' : 'Thêm khóa học'}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}

            <style>{`
                .page-header { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; border-left: 4px solid #0dcaf0; }
                .page-header h1 { margin: 0; font-size: 1.5rem; font-weight: 600; }
                .page-description { margin: 5px 0 0 0; color: #6c757d; }
                .border-left-info { border-left: 4px solid #0dcaf0 !important; }
                .border-left-success { border-left: 4px solid #198754 !important; }
                .border-left-warning { border-left: 4px solid #ffc107 !important; }
                .border-left-primary { border-left: 4px solid #0d6efd !important; }
                .course-card { transition: all 0.3s ease; }
                .course-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
            `}</style>
        </div>
    );
}
