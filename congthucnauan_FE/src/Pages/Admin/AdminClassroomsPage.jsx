import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function AdminClassroomsPage() {
    const [classrooms, setClassrooms] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showModal, setShowModal] = useState(false);
    const [editingClassroom, setEditingClassroom] = useState(null);
    const [formData, setFormData] = useState({ name: '', description: '', capacity: '', location: '', active: true });
    const [successMessage, setSuccessMessage] = useState('');
    const { token } = useAuth();

    useEffect(() => { fetchClassrooms(); }, []);

    const fetchClassrooms = async () => {
        try {
            const res = await fetch('/api/v1/admin/classrooms', { headers: { 'Authorization': `Bearer ${token}` } });
            const data = await res.json();
            setClassrooms(data.classrooms || []);
        } catch (e) { console.error(e); }
        finally { setLoading(false); }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const url = editingClassroom ? `/api/v1/admin/classrooms/${editingClassroom.id}` : '/api/v1/admin/classrooms';
        const method = editingClassroom ? 'PUT' : 'POST';
        
        const res = await fetch(url, {
            method, headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        if (res.ok) {
            setSuccessMessage(editingClassroom ? 'Cập nhật thành công!' : 'Thêm thành công!');
            setShowModal(false);
            setEditingClassroom(null);
            setFormData({ name: '', description: '', capacity: '', location: '', active: true });
            fetchClassrooms();
            setTimeout(() => setSuccessMessage(''), 3000);
        }
    };

    const handleDelete = async (id, name, courseCount) => {
        if (courseCount > 0) { alert(`Không thể xóa phòng học "${name}" vì đang có ${courseCount} khóa học`); return; }
        if (!confirm(`Xóa phòng học "${name}"?`)) return;
        await fetch(`/api/v1/admin/classrooms/${id}`, { method: 'DELETE', headers: { 'Authorization': `Bearer ${token}` } });
        setSuccessMessage('Xóa thành công!');
        fetchClassrooms();
        setTimeout(() => setSuccessMessage(''), 3000);
    };

    const openEdit = (c) => { setEditingClassroom(c); setFormData({ name: c.name, description: c.description || '', capacity: c.capacity, location: c.location, active: c.active }); setShowModal(true); };
    const openAdd = () => { setEditingClassroom(null); setFormData({ name: '', description: '', capacity: '', location: '', active: true }); setShowModal(true); };

    return (
        <div>
            <div className="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1><i className="fas fa-door-open text-primary me-2"></i>Quản lý phòng học</h1>
                    <p className="text-muted">Quản lý các phòng học cho các khóa học</p>
                </div>
                <button className="btn btn-success" onClick={openAdd}><i className="fas fa-plus-circle me-2"></i>Thêm phòng học</button>
            </div>

            {successMessage && <div className="alert alert-success"><i className="fas fa-check-circle me-2"></i>{successMessage}</div>}

            <div className="card">
                <div className="card-header d-flex justify-content-between"><span><i className="fas fa-list me-2"></i>Danh sách phòng học</span><span className="badge bg-info">{classrooms.length} phòng</span></div>
                <div className="card-body">
                    {loading ? <div className="text-center py-5"><div className="spinner-border text-primary"></div></div> : classrooms.length === 0 ? (
                        <div className="text-center py-5"><i className="fas fa-door-open fa-4x text-muted mb-3"></i><h5>Chưa có phòng học nào</h5></div>
                    ) : (
                        <div className="table-responsive">
                            <table className="table table-hover table-striped">
                                <thead><tr><th>ID</th><th>Tên</th><th>Mô tả</th><th>Sức chứa</th><th>Vị trí</th><th>Khóa học</th><th>Trạng thái</th><th>Chức năng</th></tr></thead>
                                <tbody>
                                    {classrooms.map(c => (
                                        <tr key={c.id}>
                                            <td>{c.id}</td>
                                            <td className="fw-bold">{c.name}</td>
                                            <td>{c.description?.substring(0, 50)}{c.description?.length > 50 ? '...' : ''}</td>
                                            <td>{c.capacity} người</td>
                                            <td>{c.location}</td>
                                            <td><span className="badge bg-primary">{c.courses_count || 0}</span></td>
                                            <td><span className={`badge bg-${c.active ? 'success' : 'danger'}`}>{c.active ? 'Hoạt động' : 'Không hoạt động'}</span></td>
                                            <td>
                                                <button className="btn btn-sm btn-primary me-1" onClick={() => openEdit(c)}><i className="fas fa-edit"></i></button>
                                                <button className="btn btn-sm btn-danger" onClick={() => handleDelete(c.id, c.name, c.courses_count)}><i className="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
                <div className="card-footer d-flex justify-content-between"><span>Tổng: <strong>{classrooms.length}</strong></span><Link to="/admin/courses" className="btn btn-outline-primary btn-sm"><i className="fas fa-graduation-cap me-1"></i>Quản lý khóa học</Link></div>
            </div>

            {showModal && (
                <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog">
                        <div className="modal-content">
                            <div className="modal-header"><h5>{editingClassroom ? 'Sửa phòng học' : 'Thêm phòng học'}</h5><button className="btn-close" onClick={() => setShowModal(false)}></button></div>
                            <form onSubmit={handleSubmit}>
                                <div className="modal-body">
                                    <div className="mb-3"><label className="form-label">Tên phòng học *</label><input className="form-control" value={formData.name} onChange={e => setFormData({...formData, name: e.target.value})} required /></div>
                                    <div className="mb-3"><label className="form-label">Mô tả</label><textarea className="form-control" rows="3" value={formData.description} onChange={e => setFormData({...formData, description: e.target.value})} /></div>
                                    <div className="row">
                                        <div className="col-md-6 mb-3"><label className="form-label">Sức chứa *</label><input type="number" className="form-control" value={formData.capacity} onChange={e => setFormData({...formData, capacity: e.target.value})} required /></div>
                                        <div className="col-md-6 mb-3"><label className="form-label">Vị trí *</label><input className="form-control" value={formData.location} onChange={e => setFormData({...formData, location: e.target.value})} required /></div>
                                    </div>
                                    <div className="form-check"><input type="checkbox" className="form-check-input" checked={formData.active} onChange={e => setFormData({...formData, active: e.target.checked})} /><label className="form-check-label">Hoạt động</label></div>
                                </div>
                                <div className="modal-footer"><button type="button" className="btn btn-secondary" onClick={() => setShowModal(false)}>Hủy</button><button type="submit" className="btn btn-primary">{editingClassroom ? 'Cập nhật' : 'Thêm'}</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
