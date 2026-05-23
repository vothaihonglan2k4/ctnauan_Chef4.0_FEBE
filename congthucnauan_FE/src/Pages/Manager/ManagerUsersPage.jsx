import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function ManagerUsersPage() {
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showViewModal, setShowViewModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [selectedUser, setSelectedUser] = useState(null);
    const [formData, setFormData] = useState({ name: '', email: '', role: 'user' });
    const [errors, setErrors] = useState({});
    const [successMessage, setSuccessMessage] = useState('');

    const { token, user: currentUser } = useAuth();

    // Helper check permission
    const hasPermission = (permissionCode) => {
        if (!currentUser) return false;
        if (currentUser.role === 'admin') return true;
        return currentUser.permissions && currentUser.permissions.includes(permissionCode);
    };

    useEffect(() => {
        fetchUsers();
    }, []);

    const fetchUsers = async () => {
        try {
            const response = await fetch('/api/v1/manager/users', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (response.ok) {
                setUsers(data.users || []);
            }
        } catch (error) {
            console.error('Error fetching users:', error);
        } finally {
            setLoading(false);
        }
    };

    const openEditModal = (user) => {
        setSelectedUser(user);
        setFormData({ name: user.name, email: user.email, role: user.role });
        setErrors({});
        setShowEditModal(true);
    };

    const handleEditUser = async (e) => {
        e.preventDefault();
        setErrors({});

        try {
            const response = await fetch(`/api/v1/manager/users/${selectedUser.id}`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (response.ok) {
                setSuccessMessage('Cập nhật người dùng thành công!');
                setShowEditModal(false);
                fetchUsers();
                setTimeout(() => setSuccessMessage(''), 3000);
            } else {
                setErrors(data.errors || { general: data.message });
            }
        } catch (error) {
            console.error('Error updating user:', error);
            setErrors({ general: 'Có lỗi xảy ra khi cập nhật người dùng' });
        }
    };

    const openDeleteModal = (user) => {
        setSelectedUser(user);
        setShowDeleteModal(true);
    };

    const handleDeleteUser = async () => {
        try {
            const response = await fetch(`/api/v1/manager/users/${selectedUser.id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok) {
                setSuccessMessage('Xóa người dùng thành công!');
                setShowDeleteModal(false);
                fetchUsers();
                setTimeout(() => setSuccessMessage(''), 3000);
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Error deleting user:', error);
            alert('Có lỗi xảy ra khi xóa người dùng');
        }
    };

    const openViewModal = (user) => {
        setSelectedUser(user);
        setShowViewModal(true);
    };

    const getRoleBadge = (role) => {
        if (role === 'admin') {
            return <span className="badge bg-danger">Quản trị viên</span>;
        } else if (role === 'manager') {
            return <span className="badge bg-warning">Quản lý</span>;
        }
        return <span className="badge bg-info">Người dùng</span>;
    };

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    };

    return (
        <div className="container-fluid p-4">
            {/* Page Header */}
            <div className="row mb-3">
                <div className="col">
                    <h1>
                        <i className="fas fa-users text-success me-2"></i>
                        Quản lý người dùng
                    </h1>
                    <p className="text-muted">Xem danh sách người dùng trong hệ thống</p>
                </div>
                <div className="col-auto">
                    <Link to="/manager" className="btn btn-secondary">
                        <i className="fas fa-arrow-left me-1"></i> Quay lại
                    </Link>
                </div>
            </div>

            {/* Success Message */}
            {successMessage && (
                <div className="alert alert-success alert-dismissible fade show" role="alert">
                    <i className="fas fa-check-circle me-2"></i>
                    {successMessage}
                    <button
                        type="button"
                        className="btn-close"
                        onClick={() => setSuccessMessage('')}
                    ></button>
                </div>
            )}

            {/* Users Table */}
            <div className="card">
                <div className="card-body">
                    {loading ? (
                        <div className="text-center py-5">
                            <div className="spinner-border text-success" role="status">
                                <span className="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    ) : (
                        <div className="table-responsive">
                            <table className="table table-striped table-hover">
                                <thead className="table-success">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                        <th>Ngày đăng ký</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {users.map(user => (
                                        <tr key={user.id}>
                                            <td>{user.id}</td>
                                            <td>{user.name}</td>
                                            <td>{user.email}</td>
                                            <td>{getRoleBadge(user.role)}</td>
                                            <td>{formatDate(user.created_at)}</td>
                                            <td>
                                                <div className="btn-group" role="group">
                                                    <button
                                                        type="button"
                                                        className="btn btn-info btn-sm"
                                                        onClick={() => openViewModal(user)}
                                                    >
                                                        <i className="fas fa-eye"></i> Xem
                                                    </button>
                                                    {hasPermission('user.update') && (
                                                        <button
                                                            type="button"
                                                            className="btn btn-warning btn-sm"
                                                            onClick={() => openEditModal(user)}
                                                        >
                                                            <i className="fas fa-edit"></i> Sửa
                                                        </button>
                                                    )}
                                                    {hasPermission('user.delete') && (
                                                        <button
                                                            type="button"
                                                            className="btn btn-danger btn-sm"
                                                            onClick={() => openDeleteModal(user)}
                                                        >
                                                            <i className="fas fa-trash"></i> Xóa
                                                        </button>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>

            {/* View User Modal */}
            {showViewModal && selectedUser && (
                <div className="modal show d-block" tabIndex="-1" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title">Thông tin người dùng</h5>
                                <button
                                    type="button"
                                    className="btn-close"
                                    onClick={() => setShowViewModal(false)}
                                ></button>
                            </div>
                            <div className="modal-body">
                                <div className="mb-3">
                                    <strong>ID:</strong> {selectedUser.id}
                                </div>
                                <div className="mb-3">
                                    <strong>Tên:</strong> {selectedUser.name}
                                </div>
                                <div className="mb-3">
                                    <strong>Email:</strong> {selectedUser.email}
                                </div>
                                <div className="mb-3">
                                    <strong>Vai trò:</strong> {getRoleBadge(selectedUser.role)}
                                </div>
                                <div className="mb-3">
                                    <strong>Số điện thoại:</strong> {selectedUser.phone || 'Chưa cập nhật'}
                                </div>
                                <div className="mb-3">
                                    <strong>Địa chỉ:</strong> {selectedUser.address || 'Chưa cập nhật'}
                                </div>
                                <div className="mb-3">
                                    <strong>Ngày đăng ký:</strong> {formatDate(selectedUser.created_at)}
                                </div>
                            </div>
                            <div className="modal-footer">
                                <button
                                    type="button"
                                    className="btn btn-secondary"
                                    onClick={() => setShowViewModal(false)}
                                >
                                    Đóng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Edit User Modal */}
            {showEditModal && selectedUser && (
                <div className="modal show d-block" tabIndex="-1" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title">Sửa thông tin người dùng</h5>
                                <button
                                    type="button"
                                    className="btn-close"
                                    onClick={() => setShowEditModal(false)}
                                ></button>
                            </div>
                            <form onSubmit={handleEditUser}>
                                <div className="modal-body">
                                    {errors.general && (
                                        <div className="alert alert-danger">{errors.general}</div>
                                    )}
                                    <div className="mb-3">
                                        <label className="form-label">Tên:</label>
                                        <input
                                            type="text"
                                            className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                                            value={formData.name}
                                            onChange={(e) => setFormData({...formData, name: e.target.value})}
                                            required
                                        />
                                        {errors.name && <div className="invalid-feedback">{errors.name[0]}</div>}
                                    </div>
                                    <div className="mb-3">
                                        <label className="form-label">Email:</label>
                                        <input
                                            type="email"
                                            className={`form-control ${errors.email ? 'is-invalid' : ''}`}
                                            value={formData.email}
                                            onChange={(e) => setFormData({...formData, email: e.target.value})}
                                            required
                                        />
                                        {errors.email && <div className="invalid-feedback">{errors.email[0]}</div>}
                                    </div>
                                    <div className="mb-3">
                                        <label className="form-label">Vai trò:</label>
                                        <select
                                            className="form-select"
                                            value={formData.role}
                                            onChange={(e) => setFormData({...formData, role: e.target.value})}
                                            required
                                        >
                                            <option value="user">Người dùng</option>
                                            <option value="manager">Quản lý</option>
                                            <option value="admin">Quản trị viên</option>
                                        </select>
                                    </div>
                                </div>
                                <div className="modal-footer">
                                    <button
                                        type="button"
                                        className="btn btn-secondary"
                                        onClick={() => setShowEditModal(false)}
                                    >
                                        Hủy
                                    </button>
                                    <button type="submit" className="btn btn-warning">
                                        Cập nhật
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}

            {/* Delete User Modal */}
            {showDeleteModal && selectedUser && (
                <div className="modal show d-block" tabIndex="-1" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title">Xác nhận xóa</h5>
                                <button
                                    type="button"
                                    className="btn-close"
                                    onClick={() => setShowDeleteModal(false)}
                                ></button>
                            </div>
                            <div className="modal-body">
                                <p>Bạn có chắc chắn muốn xóa người dùng <strong>{selectedUser.name}</strong>?</p>
                                <p className="text-danger">Hành động này không thể hoàn tác!</p>
                            </div>
                            <div className="modal-footer">
                                <button
                                    type="button"
                                    className="btn btn-secondary"
                                    onClick={() => setShowDeleteModal(false)}
                                >
                                    Hủy
                                </button>
                                <button
                                    type="button"
                                    className="btn btn-danger"
                                    onClick={handleDeleteUser}
                                >
                                    Xóa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
