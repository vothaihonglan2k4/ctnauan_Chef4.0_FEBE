import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function AdminCoursesPage() {
    const [courses, setCourses] = useState([]);
    const [classrooms, setClassrooms] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showModal, setShowModal] = useState(false);
    const [editingCourse, setEditingCourse] = useState(null);
    const [formData, setFormData] = useState({ title: '', description: '', classroom_id: '', price: '', duration: '', status: 'draft' });
    const [successMessage, setSuccessMessage] = useState('');
    const { token } = useAuth();

    useEffect(() => { fetchCourses(); fetchClassrooms(); }, []);

    const fetchCourses = async () => {
        try {
            const res = await fetch('/api/v1/admin/courses', { headers: { 'Authorization': `Bearer ${token}` } });
            const data = await res.json();
            setCourses(data.courses || []);
        } catch (e) { console.error(e); }
        finally { setLoading(false); }
    };

    const fetchClassrooms = async () => {
        const res = await fetch('/api/v1/admin/courses/classrooms', { headers: { 'Authorization': `Bearer ${token}` } });
        const data = await res.json();
        setClassrooms(data.classrooms || []);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const url = editingCourse ? `/api/v1/admin/courses/${editingCourse.id}` : '/api/v1/admin/courses';
        const method = editingCourse ? 'PUT' : 'POST';
        
        const res = await fetch(url, {
            method, headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        if (res.ok) {
            setSuccessMessage(editingCourse ? 'Cập nhật thành công!' : 'Thêm thành công!');
            setShowModal(false);
            setEditingCourse(null);
            setFormData({ title: '', description: '', classroom_id: '', price: '', duration: '', status: 'draft' });
            fetchCourses();
            setTimeout(() => setSuccessMessage(''), 3000);
        }
    };

    const handleDelete = async (id, title) => {
        if (!confirm(`Xóa khóa học "${title}"?`)) return;
        await fetch(`/api/v1/admin/courses/${id}`, { method: 'DELETE', headers: { 'Authorization': `Bearer ${token}` } });
        setSuccessMessage('Xóa thành công!');
        fetchCourses();
        setTimeout(() => setSuccessMessage(''), 3000);
    };

    const openEdit = (c) => { setEditingCourse(c); setFormData({ title: c.title, description: c.description || '', classroom_id: c.classroom_id, price: c.price, duration: c.duration || '', status: c.status }); setShowModal(true); };
    const openAdd = () => { setEditingCourse(null); setFormData({ title: '', description: '', classroom_id: '', price: '', duration: '', status: 'draft' }); setShowModal(true); };

    const getStatusBadge = (status) => {
        const map = { published: ['success', 'Đã xuất bản'], draft: ['warning', 'Bản nháp'], archived: ['secondary', 'Đã lưu trữ'] };
        const [color, text] = map[status] || ['secondary', status];
        return <span className={`badge bg-${color}`}>{text}</span>;
    };

    return (
        <div>
            <div className="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1><i className="fas fa-graduation-cap text-primary me-2"></i>Quản lý khóa học</h1>
                    <p className="text-muted">Tạo và quản lý các khóa học trực tuyến</p>
                </div>
                <button className="btn btn-success" onClick={openAdd}><i className="fas fa-plus-circle me-2"></i>Thêm khóa học</button>
            </div>

            {successMessage && <div className="alert alert-success"><i className="fas fa-check-circle me-2"></i>{successMessage}</div>}

            <div className="card">
                <div className="card-header d-flex justify-content-between"><span><i className="fas fa-list me-2"></i>Danh sách khóa học</span><span className="badge bg-info">{courses.length} khóa học</span></div>
                <div className="card-body">
                    {loading ? <div className="text-center py-5"><div className="spinner-border text-primary"></div></div> : courses.length === 0 ? (
                        <div className="text-center py-5"><i className="fas fa-graduation-cap fa-4x text-muted mb-3"></i><h5>Chưa có khóa học nào</h5></div>
                    ) : (
                        <div className="table-responsive">
                            <table className="table table-hover table-striped">
                                <thead><tr><th>ID</th><th>Hình</th><th>Tên khóa học</th><th>Phòng học</th><th>Giá</th><th>Thời lượng</th><th>Học viên</th><th>Ngày tạo</th><th>Chức năng</th></tr></thead>
                                <tbody>
                                    {courses.map(c => (
                                        <tr key={c.id}>
                                            <td>{c.id}</td>
                                            <td><img src={c.image ? `/uploads/courses/${c.image}` : '/img/congthucnauan.jpg'} alt="" className="img-thumbnail" style={{ width: 60, height: 60, objectFit: 'cover' }} /></td>
                                            <td><div className="fw-bold">{c.title}</div>{getStatusBadge(c.status)}</td>
                                            <td><span className="badge bg-info"><i className="fas fa-door-open me-1"></i>{c.classroom?.name || 'N/A'}</span></td>
                                            <td className="fw-bold text-success">{new Intl.NumberFormat('vi-VN').format(c.price)} ₫</td>
                                            <td><i className="far fa-clock me-1"></i>{c.duration || 0} phút</td>
                                            <td><span className="badge bg-primary">{c.student_count || 0}</span></td>
                                            <td>{new Date(c.created_at).toLocaleDateString('vi-VN')}</td>
                                            <td>
                                                <Link to={`/admin/courses/${c.id}/lessons`} className="btn btn-sm btn-info text-white me-1" title="Bài học"><i className="fas fa-book"></i></Link>
                                                <button className="btn btn-sm btn-primary me-1" onClick={() => openEdit(c)}><i className="fas fa-edit"></i></button>
                                                <button className="btn btn-sm btn-danger" onClick={() => handleDelete(c.id, c.title)}><i className="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>

            {showModal && (
                <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog modal-lg">
                        <div className="modal-content">
                            <div className="modal-header"><h5>{editingCourse ? 'Sửa khóa học' : 'Thêm khóa học'}</h5><button className="btn-close" onClick={() => setShowModal(false)}></button></div>
                            <form onSubmit={handleSubmit}>
                                <div className="modal-body">
                                    <div className="mb-3"><label className="form-label">Tên khóa học *</label><input className="form-control" value={formData.title} onChange={e => setFormData({...formData, title: e.target.value})} required /></div>
                                    <div className="mb-3"><label className="form-label">Mô tả</label><textarea className="form-control" rows="3" value={formData.description} onChange={e => setFormData({...formData, description: e.target.value})} /></div>
                                    <div className="row">
                                        <div className="col-md-6 mb-3"><label className="form-label">Phòng học *</label><select className="form-select" value={formData.classroom_id} onChange={e => setFormData({...formData, classroom_id: e.target.value})} required><option value="">-- Chọn phòng học --</option>{classrooms.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}</select></div>
                                        <div className="col-md-6 mb-3"><label className="form-label">Giá (VNĐ) *</label><input type="number" className="form-control" value={formData.price} onChange={e => setFormData({...formData, price: e.target.value})} required /></div>
                                    </div>
                                    <div className="row">
                                        <div className="col-md-6 mb-3"><label className="form-label">Thời lượng (phút)</label><input type="number" className="form-control" value={formData.duration} onChange={e => setFormData({...formData, duration: e.target.value})} /></div>
                                        <div className="col-md-6 mb-3"><label className="form-label">Trạng thái</label><select className="form-select" value={formData.status} onChange={e => setFormData({...formData, status: e.target.value})}><option value="draft">Bản nháp</option><option value="published">Đã xuất bản</option><option value="archived">Đã lưu trữ</option></select></div>
                                    </div>
                                </div>
                                <div className="modal-footer"><button type="button" className="btn btn-secondary" onClick={() => setShowModal(false)}>Hủy</button><button type="submit" className="btn btn-primary">{editingCourse ? 'Cập nhật' : 'Thêm'}</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
