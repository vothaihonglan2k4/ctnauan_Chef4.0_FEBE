import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function ManagerContactsPage() {
    const [contacts, setContacts] = useState([]);
    const [stats, setStats] = useState({ total: 0, new: 0, read: 0 });
    const [loading, setLoading] = useState(true);
    const [selectedContact, setSelectedContact] = useState(null);
    const [showModal, setShowModal] = useState(false);
    const [successMessage, setSuccessMessage] = useState('');
    const [filterStatus, setFilterStatus] = useState('all');
    const { token } = useAuth();

    useEffect(() => { fetchContacts(); }, [filterStatus]);

    const fetchContacts = async () => {
        try {
            const res = await fetch(`/api/v1/manager/contacts?status=${filterStatus}`, { 
                headers: { 'Authorization': `Bearer ${token}` } 
            });
            const data = await res.json();
            setContacts(data.contacts || []);
            setStats(data.stats || { total: 0, new: 0, read: 0 });
        } catch (e) { console.error(e); }
        finally { setLoading(false); }
    };

    const showContactDetail = async (contact) => {
        // Fetch detail and mark as read
        const res = await fetch(`/api/v1/manager/contacts/${contact.id}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();
        setSelectedContact(data.contact);
        setShowModal(true);
        // Refresh list to update status
        fetchContacts();
    };

    const handleMarkRead = async (id) => {
        await fetch(`/api/v1/manager/contacts/${id}/status`, {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: 'read' })
        });
        setSuccessMessage('Đã đánh dấu đã đọc');
        fetchContacts();
        setTimeout(() => setSuccessMessage(''), 3000);
    };

    const handleDelete = async (id, name) => {
        if (!confirm(`Xóa liên hệ từ "${name}"?`)) return;
        await fetch(`/api/v1/manager/contacts/${id}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${token}` }
        });
        setSuccessMessage('Xóa liên hệ thành công!');
        fetchContacts();
        setTimeout(() => setSuccessMessage(''), 3000);
    };

    const formatDate = (dateStr) => {
        const date = new Date(dateStr);
        return date.toLocaleDateString('vi-VN');
    };

    const formatTime = (dateStr) => {
        const date = new Date(dateStr);
        return date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    };

    if (loading) return <div className="text-center py-5"><div className="spinner-border text-warning"></div></div>;

    return (
        <div>
            {/* Page Header */}
            <div className="page-header">
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        <h1><i className="fas fa-envelope text-warning me-2"></i>Quản Lý Liên Hệ</h1>
                        <p className="page-description">Xem và trả lời các tin nhắn từ khách hàng</p>
                    </div>
                    <div>
                        <Link to="/manager" className="btn btn-outline-secondary">
                            <i className="fas fa-arrow-left me-1"></i> Về Dashboard
                        </Link>
                    </div>
                </div>
            </div>

            {successMessage && <div className="alert alert-success alert-dismissible fade show"><i className="fas fa-check-circle me-2"></i>{successMessage}</div>}

            {/* Stats Row */}
            <div className="row mb-4">
                {[
                    { icon: 'envelope', color: 'info', value: stats.total, label: 'Tổng tin nhắn' },
                    { icon: 'exclamation-circle', color: 'danger', value: stats.new, label: 'Chưa đọc' },
                    { icon: 'check-circle', color: 'success', value: stats.read, label: 'Đã đọc' },
                    { icon: 'reply', color: 'warning', value: 0, label: 'Đã trả lời' },
                ].map((s, i) => (
                    <div key={i} className="col-md-3 mb-3">
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

            {/* Contacts List */}
            <div className="card">
                <div className="card-header d-flex justify-content-between align-items-center">
                    <h5 className="mb-0"><i className="fas fa-list me-2"></i>Danh Sách Liên Hệ</h5>
                    <select className="form-select form-select-sm" style={{ width: 'auto' }} value={filterStatus} onChange={e => setFilterStatus(e.target.value)}>
                        <option value="all">Tất cả</option>
                        <option value="new">Chưa đọc</option>
                        <option value="read">Đã đọc</option>
                    </select>
                </div>
                <div className="card-body p-0">
                    {contacts.length > 0 ? (
                        <div className="table-responsive">
                            <table className="table table-hover mb-0">
                                <thead className="bg-light">
                                    <tr>
                                        <th width="5%"><i className="fas fa-circle"></i></th>
                                        <th width="20%">Người gửi</th>
                                        <th width="25%">Tiêu đề</th>
                                        <th width="30%">Nội dung</th>
                                        <th width="15%">Thời gian</th>
                                        <th width="5%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {contacts.map(contact => (
                                        <tr key={contact.id} className={contact.status === 'new' ? 'table-light' : ''}>
                                            <td className="text-center">
                                                <i className={`fas fa-circle text-${contact.status === 'new' ? 'danger' : 'success'}`} 
                                                   style={{ fontSize: '8px' }} title={contact.status === 'new' ? 'Chưa đọc' : 'Đã đọc'}></i>
                                            </td>
                                            <td>
                                                <div className="fw-bold">{contact.name}</div>
                                                <div className="text-muted small">{contact.email}</div>
                                            </td>
                                            <td>
                                                <div className="fw-bold">{contact.subject}</div>
                                                <span className={`badge bg-${contact.status === 'new' ? 'danger' : 'success'} badge-sm`}>
                                                    {contact.status === 'new' ? 'Mới' : 'Đã đọc'}
                                                </span>
                                            </td>
                                            <td>
                                                <div className="text-truncate" style={{ maxWidth: '200px' }}>{contact.message}</div>
                                            </td>
                                            <td>
                                                <div className="small text-muted"><i className="fas fa-clock me-1"></i>{formatDate(contact.created_at)}</div>
                                                <div className="small text-muted">{formatTime(contact.created_at)}</div>
                                            </td>
                                            <td>
                                                <div className="dropdown">
                                                    <button className="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                        <i className="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul className="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <button className="dropdown-item" onClick={() => showContactDetail(contact)}>
                                                                <i className="fas fa-eye me-2"></i> Xem chi tiết
                                                            </button>
                                                        </li>
                                                        {contact.status === 'new' && (
                                                            <li>
                                                                <button className="dropdown-item" onClick={() => handleMarkRead(contact.id)}>
                                                                    <i className="fas fa-check me-2"></i> Đánh dấu đã đọc
                                                                </button>
                                                            </li>
                                                        )}
                                                        <li><hr className="dropdown-divider" /></li>
                                                        <li>
                                                            <a className="dropdown-item text-primary" href={`mailto:${contact.email}?subject=Re: ${encodeURIComponent(contact.subject)}`}>
                                                                <i className="fas fa-reply me-2"></i> Trả lời qua email
                                                            </a>
                                                        </li>
                                                        <li><hr className="dropdown-divider" /></li>
                                                        <li>
                                                            <button className="dropdown-item text-danger" onClick={() => handleDelete(contact.id, contact.name)}>
                                                                <i className="fas fa-trash me-2"></i> Xóa
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <div className="text-center py-5">
                            <i className="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h4 className="text-muted">Chưa có liên hệ nào</h4>
                            <p className="text-muted">Các tin nhắn liên hệ từ khách hàng sẽ hiển thị tại đây.</p>
                        </div>
                    )}
                </div>
            </div>

            {/* Contact Detail Modal */}
            {showModal && selectedContact && (
                <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog modal-lg">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title"><i className="fas fa-envelope me-2"></i>Chi Tiết Liên Hệ</h5>
                                <button type="button" className="btn-close" onClick={() => setShowModal(false)}></button>
                            </div>
                            <div className="modal-body">
                                <div className="row mb-3">
                                    <div className="col-md-6">
                                        <label className="fw-bold text-muted">Người gửi:</label>
                                        <div>{selectedContact.name}</div>
                                    </div>
                                    <div className="col-md-6">
                                        <label className="fw-bold text-muted">Email:</label>
                                        <div><a href={`mailto:${selectedContact.email}`}>{selectedContact.email}</a></div>
                                    </div>
                                </div>
                                <div className="row mb-3">
                                    <div className="col-md-6">
                                        <label className="fw-bold text-muted">Tiêu đề:</label>
                                        <div>{selectedContact.subject}</div>
                                    </div>
                                    <div className="col-md-6">
                                        <label className="fw-bold text-muted">Thời gian:</label>
                                        <div>{new Date(selectedContact.created_at).toLocaleString('vi-VN')}</div>
                                    </div>
                                </div>
                                <div className="mb-3">
                                    <label className="fw-bold text-muted">Nội dung:</label>
                                    <div className="bg-light p-3 rounded">{selectedContact.message}</div>
                                </div>
                            </div>
                            <div className="modal-footer">
                                <button type="button" className="btn btn-secondary" onClick={() => setShowModal(false)}>Đóng</button>
                                <a href={`mailto:${selectedContact.email}?subject=Re: ${encodeURIComponent(selectedContact.subject)}`} className="btn btn-primary">
                                    <i className="fas fa-reply me-1"></i> Trả lời
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            <style>{`
                .page-header { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; border-left: 4px solid #ffc107; }
                .page-header h1 { margin: 0; font-size: 1.5rem; font-weight: 600; }
                .page-description { margin: 5px 0 0 0; color: #6c757d; }
                .border-left-info { border-left: 4px solid #0dcaf0 !important; }
                .border-left-danger { border-left: 4px solid #dc3545 !important; }
                .border-left-success { border-left: 4px solid #198754 !important; }
                .border-left-warning { border-left: 4px solid #ffc107 !important; }
                .table-hover tbody tr:hover { background-color: rgba(0,0,0,0.02); }
                .text-truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                .badge-sm { font-size: 0.7em; }
                tr.table-light { font-weight: 500; }
            `}</style>
        </div>
    );
}
