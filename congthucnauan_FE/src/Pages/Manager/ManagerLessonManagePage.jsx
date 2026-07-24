import { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function ManagerLessonManagePage() {
    const { courseId } = useParams();
    const navigate = useNavigate();
    const [course, setCourse] = useState(null);
    const [lessons, setLessons] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showModal, setShowModal] = useState(false);
    const [editingLesson, setEditingLesson] = useState(null);
    const [formData, setFormData] = useState({
        title: '', content: '', video_url: '', duration_minutes: '',
        sort_order: '', is_free: false, summary: '', image: ''
    });
    const [successMessage, setSuccessMessage] = useState('');
    const [deleteConfirm, setDeleteConfirm] = useState(null);
    const { token } = useAuth();

    useEffect(() => { fetchLessons(); }, []);

    const fetchLessons = async () => {
        try {
            const res = await fetch(`/api/v1/manager/courses/${courseId}/lessons`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();
            setCourse(data.course || null);
            setLessons(data.lessons || []);
        } catch (e) { console.error(e); }
        finally { setLoading(false); }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const url = editingLesson
            ? `/api/v1/manager/courses/${courseId}/lessons/${editingLesson.id}`
            : `/api/v1/manager/courses/${courseId}/lessons`;

        const body = new FormData();
        if (editingLesson) {
            // Laravel form method spoofing for PUT with FormData
            body.append('_method', 'PUT');
        }
        Object.keys(formData).forEach(key => {
            if (key === 'image') {
                // image: only send if user uploaded a new file
                if (formData.image instanceof File) {
                    body.append('image', formData.image);
                } else if (editingLesson) {
                    // When editing and no new file uploaded, keep original image
                    body.append('image', formData.image);
                }
            } else {
                body.append(key, formData[key]);
            }
        });

        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}` },
            body
        });

        if (res.ok) {
            setSuccessMessage(editingLesson ? 'Cập nhật bài học thành công!' : 'Thêm bài học thành công!');
            setShowModal(false);
            resetForm();
            fetchLessons();
            setTimeout(() => setSuccessMessage(''), 3000);
        }
    };

    const handleDelete = async (id, title) => {
        const res = await fetch(`/api/v1/manager/courses/${courseId}/lessons/${id}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (res.ok) {
            setSuccessMessage('Xóa bài học thành công!');
            fetchLessons();
            setTimeout(() => setSuccessMessage(''), 3000);
        }
        setDeleteConfirm(null);
    };

    const openAdd = () => {
        setEditingLesson(null);
        resetForm();
        setShowModal(true);
    };

    const openEdit = (lesson) => {
        setEditingLesson(lesson);
        setFormData({
            title: lesson.title,
            content: lesson.content || '',
            video_url: lesson.video_url || '',
            duration_minutes: lesson.duration_minutes || '',
            sort_order: lesson.sort_order || '',
            is_free: lesson.is_free || false,
            summary: lesson.summary || '',
            image: lesson.image || ''
        });
        setShowModal(true);
    };

    const resetForm = () => setFormData({
        title: '', content: '', video_url: '', duration_minutes: '',
        sort_order: '', is_free: false, summary: '', image: ''
    });

    const handleMoveUp = async (lesson) => {
        const idx = lessons.findIndex(l => l.id === lesson.id);
        if (idx <= 0) return;
        const newLessons = [...lessons];
        [newLessons[idx - 1], newLessons[idx]] = [newLessons[idx], newLessons[idx - 1]];

        await fetch(`/api/v1/manager/courses/${courseId}/lessons/reorder`, {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({ lesson_ids: newLessons.map(l => l.id) })
        });
        fetchLessons();
    };

    const handleMoveDown = async (lesson) => {
        const idx = lessons.findIndex(l => l.id === lesson.id);
        if (idx >= lessons.length - 1) return;
        const newLessons = [...lessons];
        [newLessons[idx], newLessons[idx + 1]] = [newLessons[idx + 1], newLessons[idx]];

        await fetch(`/api/v1/manager/courses/${courseId}/lessons/reorder`, {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({ lesson_ids: newLessons.map(l => l.id) })
        });
        fetchLessons();
    };

    const formatDuration = (mins) => {
        if (!mins || mins <= 0) return '—';
        const h = Math.floor(mins / 60);
        const m = mins % 60;
        return h > 0 ? `${h}h ${m} phút` : `${m} phút`;
    };

    if (loading) return <div className="text-center py-5"><div className="spinner-border text-info"></div></div>;

    return (
        <div>
            {/* Page Header */}
            <div className="page-header">
                <div className="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h1>
                            <Link to="/manager/courses" className="text-decoration-none text-info me-2">
                                <i className="fas fa-arrow-left"></i>
                            </Link>
                            <i className="fas fa-book-open text-info me-2"></i>
                            Quản lý Bài học
                        </h1>
                        {course && <p className="page-description mb-0">{course.title}</p>}
                    </div>
                    <div>
                        <button className="btn btn-success" onClick={openAdd}>
                            <i className="fas fa-plus-circle me-2"></i>Thêm bài học mới
                        </button>
                    </div>
                </div>
            </div>

            {successMessage && (
                <div className="alert alert-success">
                    <i className="fas fa-check-circle me-2"></i>{successMessage}
                </div>
            )}

            {/* Lesson Stats */}
            <div className="row mb-4">
                <div className="col-md-3">
                    <div className="card border-left-info">
                        <div className="card-body text-center">
                            <i className="fas fa-book-open fa-2x text-info mb-2"></i>
                            <div className="h4 mb-0">{lessons.length}</div>
                            <div className="small text-muted">Tổng bài học</div>
                        </div>
                    </div>
                </div>
                <div className="col-md-3">
                    <div className="card border-left-success">
                        <div className="card-body text-center">
                            <i className="fas fa-unlock fa-2x text-success mb-2"></i>
                            <div className="h4 mb-0">{lessons.filter(l => l.is_free).length}</div>
                            <div className="small text-muted">Bài học miễn phí</div>
                        </div>
                    </div>
                </div>
                <div className="col-md-3">
                    <div className="card border-left-warning">
                        <div className="card-body text-center">
                            <i className="fas fa-clock fa-2x text-warning mb-2"></i>
                            <div className="h4 mb-0">
                                {formatDuration(lessons.reduce((sum, l) => sum + (l.duration_minutes || 0), 0))}
                            </div>
                            <div className="small text-muted">Tổng thời lượng</div>
                        </div>
                    </div>
                </div>
                <div className="col-md-3">
                    <div className="card border-left-primary">
                        <div className="card-body text-center">
                            <i className="fas fa-video fa-2x text-primary mb-2"></i>
                            <div className="h4 mb-0">{lessons.filter(l => l.video_url).length}</div>
                            <div className="small text-muted">Có video</div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Lessons List */}
            <div className="card">
                <div className="card-header d-flex justify-content-between">
                    <span><i className="fas fa-list-ol me-2"></i>Danh sách bài học</span>
                    <span className="badge bg-info">{lessons.length} bài học</span>
                </div>
                <div className="card-body">
                    {lessons.length === 0 ? (
                        <div className="text-center py-5">
                            <i className="fas fa-book-open fa-4x text-muted mb-3"></i>
                            <h5>Chưa có bài học nào</h5>
                            <p className="text-muted">Hãy thêm bài học đầu tiên cho khóa học này</p>
                            <button className="btn btn-info mt-2" onClick={openAdd}>
                                <i className="fas fa-plus me-1"></i>Thêm bài học ngay
                            </button>
                        </div>
                    ) : (
                        <div className="table-responsive">
                            <table className="table table-hover align-middle">
                                <thead className="table-light">
                                    <tr>
                                        <th style={{ width: '50px' }}>STT</th>
                                        <th>Tên bài học</th>
                                        <th style={{ width: '120px' }}>Thời lượng</th>
                                        <th style={{ width: '80px' }}>Miễn phí</th>
                                        <th style={{ width: '120px' }}>Video</th>
                                        <th style={{ width: '200px' }} className="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {lessons.map((lesson, index) => (
                                        <tr key={lesson.id}>
                                            <td>
                                                <div className="d-flex align-items-center gap-1">
                                                    <button className="btn btn-sm btn-outline-secondary py-0 px-1"
                                                        onClick={() => handleMoveUp(lesson)} disabled={index === 0}
                                                        title="Lên trên">
                                                        <i className="fas fa-chevron-up" style={{ fontSize: '10px' }}></i>
                                                    </button>
                                                    <span className="badge bg-secondary">{index + 1}</span>
                                                    <button className="btn btn-sm btn-outline-secondary py-0 px-1"
                                                        onClick={() => handleMoveDown(lesson)} disabled={index === lessons.length - 1}
                                                        title="Xuống dưới">
                                                        <i className="fas fa-chevron-down" style={{ fontSize: '10px' }}></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <div className="fw-bold">{lesson.title}</div>
                                                {lesson.summary && (
                                                    <div className="small text-muted">{lesson.summary.substring(0, 60)}...</div>
                                                )}
                                            </td>
                                            <td>{formatDuration(lesson.duration_minutes)}</td>
                                            <td>
                                                {lesson.is_free ? (
                                                    <span className="badge bg-success"><i className="fas fa-unlock me-1"></i>Miễn phí</span>
                                                ) : (
                                                    <span className="badge bg-secondary"><i className="fas fa-lock me-1"></i>Trả phí</span>
                                                )}
                                            </td>
                                            <td>
                                                {lesson.video_url ? (
                                                    <span className="badge bg-primary"><i className="fas fa-play me-1"></i>Có video</span>
                                                ) : (
                                                    <span className="text-muted"><i className="fas fa-video-slash"></i></span>
                                                )}
                                            </td>
                                            <td className="text-center">
                                                <button className="btn btn-sm btn-outline-info me-1" onClick={() => openEdit(lesson)} title="Sửa">
                                                    <i className="fas fa-edit"></i>
                                                </button>
                                                <button className="btn btn-sm btn-outline-danger" onClick={() => setDeleteConfirm(lesson)} title="Xóa">
                                                    <i className="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
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
                                <h5>{editingLesson ? 'Sửa bài học' : 'Thêm bài học mới'}</h5>
                                <button className="btn-close" onClick={() => setShowModal(false)}></button>
                            </div>
                            <form onSubmit={handleSubmit}>
                                <div className="modal-body">
                                    <div className="mb-3">
                                        <label className="form-label">Tên bài học *</label>
                                        <input className="form-control" value={formData.title}
                                            onChange={e => setFormData({ ...formData, title: e.target.value })} required />
                                    </div>
                                    <div className="mb-3">
                                        <label className="form-label">Tóm tắt</label>
                                        <textarea className="form-control" rows="2" value={formData.summary}
                                            onChange={e => setFormData({ ...formData, summary: e.target.value })}
                                            placeholder="Mô tả ngắn về nội dung bài học" />
                                    </div>
                                    <div className="mb-3">
                                        <label className="form-label">Nội dung chi tiết</label>
                                        <textarea className="form-control" rows="5" value={formData.content}
                                            onChange={e => setFormData({ ...formData, content: e.target.value })}
                                            placeholder="Nội dung chi tiết của bài học (có thể dùng HTML)" />
                                    </div>
                                    <div className="row">
                                        <div className="col-md-6 mb-3">
                                            <label className="form-label">URL Video</label>
                                            <input type="url" className="form-control" value={formData.video_url}
                                                onChange={e => setFormData({ ...formData, video_url: e.target.value })}
                                                placeholder="https://youtube.com/watch?v=..." />
                                        </div>
                                        <div className="col-md-6 mb-3">
                                            <label className="form-label">Thời lượng (phút)</label>
                                            <input type="number" className="form-control" value={formData.duration_minutes}
                                                onChange={e => setFormData({ ...formData, duration_minutes: e.target.value })}
                                                min="0" />
                                        </div>
                                    </div>
                                    <div className="row">
                                        <div className="col-md-4 mb-3">
                                            <label className="form-label">Thứ tự hiển thị</label>
                                            <input type="number" className="form-control" value={formData.sort_order}
                                                onChange={e => setFormData({ ...formData, sort_order: e.target.value })}
                                                min="0" />
                                        </div>
                                        <div className="col-md-4 mb-3">
                                            <label className="form-label">Trạng thái</label>
                                            <div className="form-check form-switch mt-2">
                                                <input className="form-check-input" type="checkbox" id="isFree"
                                                    checked={formData.is_free}
                                                    onChange={e => setFormData({ ...formData, is_free: e.target.checked })} />
                                                <label className="form-check-label" htmlFor="isFree">Bài học miễn phí</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="modal-footer">
                                    <button type="button" className="btn btn-secondary" onClick={() => setShowModal(false)}>Hủy</button>
                                    <button type="submit" className="btn btn-success">
                                        {editingLesson ? 'Cập nhật' : 'Thêm bài học'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}

            {/* Delete Confirmation Modal */}
            {deleteConfirm && (
                <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog modal-sm">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5>Xác nhận xóa</h5>
                                <button className="btn-close" onClick={() => setDeleteConfirm(null)}></button>
                            </div>
                            <div className="modal-body">
                                <p>Bạn có chắc muốn xóa bài học <strong>"{deleteConfirm.title}"</strong>?</p>
                                <p className="text-danger small mb-0">
                                    <i className="fas fa-exclamation-triangle me-1"></i>
                                    Hành động này không thể hoàn tác.
                                </p>
                            </div>
                            <div className="modal-footer">
                                <button className="btn btn-secondary" onClick={() => setDeleteConfirm(null)}>Hủy</button>
                                <button className="btn btn-danger" onClick={() => handleDelete(deleteConfirm.id, deleteConfirm.title)}>
                                    <i className="fas fa-trash me-1"></i>Xóa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            <style>{`
                .page-header {
                    background: white;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    margin-bottom: 20px;
                    border-left: 4px solid #0dcaf0;
                }
                .page-header h1 { margin: 0; font-size: 1.5rem; font-weight: 600; }
                .page-description { margin: 5px 0 0 0; color: #6c757d; }
                .border-left-info { border-left: 4px solid #0dcaf0 !important; }
                .border-left-success { border-left: 4px solid #198754 !important; }
                .border-left-warning { border-left: 4px solid #ffc107 !important; }
                .border-left-primary { border-left: 4px solid #0d6efd !important; }
                .table th { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; }
                .table td { vertical-align: middle; }
            `}</style>
        </div>
    );
}
