import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function AdminPermissionsPage() {
    const [roles, setRoles] = useState([]);
    const [permissions, setPermissions] = useState([]);
    const [selectedRole, setSelectedRole] = useState(null);
    const [selectedPermissions, setSelectedPermissions] = useState([]);
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [successMessage, setSuccessMessage] = useState('');
    const [errorMessage, setErrorMessage] = useState('');

    const { token } = useAuth();

    useEffect(() => {
        fetchRoles();
        fetchPermissions();
    }, []);

    const fetchRoles = async () => {
        try {
            const response = await fetch('/api/v1/admin/rbac/roles', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                setRoles(data.roles || []);
                if (data.roles && data.roles.length > 0) {
                    handleRoleChange(data.roles[0].id);
                }
            }
        } catch (error) {
            console.error('Error fetching roles:', error);
        }
    };

    const fetchPermissions = async () => {
        try {
            const response = await fetch('/api/v1/admin/rbac/permissions', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                setPermissions(data.permissions || []);
            }
        } catch (error) {
            console.error('Error fetching permissions:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleRoleChange = async (roleId) => {
        const role = roles.find(r => r.id === parseInt(roleId));
        setSelectedRole(role);

        if (!role) return;

        try {
            const response = await fetch(`/api/v1/admin/rbac/roles/${roleId}/permissions`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                const permIds = data.role.permissions.map(p => p.id);
                setSelectedPermissions(permIds);
            }
        } catch (error) {
            console.error('Error fetching role permissions:', error);
        }
    };

    const handlePermissionToggle = (permissionId) => {
        // Admin role không cho phép chỉnh sửa
        if (selectedRole && selectedRole.name === 'admin') {
            return;
        }

        setSelectedPermissions(prev => {
            if (prev.includes(permissionId)) {
                return prev.filter(id => id !== permissionId);
            } else {
                return [...prev, permissionId];
            }
        });
    };

    const handleSave = async () => {
        if (!selectedRole) return;

        // Chặn lưu admin role
        if (selectedRole.name === 'admin') {
            setErrorMessage('Không cho phép chỉnh sửa quyền Admin');
            setTimeout(() => setErrorMessage(''), 3000);
            return;
        }

        setSaving(true);
        setErrorMessage('');

        try {
            const response = await fetch(`/api/v1/admin/rbac/roles/${selectedRole.id}/permissions`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    permission_ids: selectedPermissions
                })
            });

            const data = await response.json();

            if (response.ok) {
                setSuccessMessage('Cập nhật quyền thành công!');
                setTimeout(() => setSuccessMessage(''), 3000);
            } else {
                setErrorMessage(data.message || 'Có lỗi xảy ra');
                setTimeout(() => setErrorMessage(''), 3000);
            }
        } catch (error) {
            console.error('Error saving permissions:', error);
            setErrorMessage('Có lỗi xảy ra khi lưu quyền');
            setTimeout(() => setErrorMessage(''), 3000);
        } finally {
            setSaving(false);
        }
    };

    // Group permissions by module
    const groupedPermissions = permissions.reduce((acc, perm) => {
        if (!acc[perm.module]) {
            acc[perm.module] = [];
        }
        acc[perm.module].push(perm);
        return acc;
    }, {});

    const isAdmin = selectedRole && selectedRole.name === 'admin';

    return (
        <div>
            {/* Page Header */}
            <div className="row mb-3">
                <div className="col">
                    <h1>
                        <i className="fas fa-shield-alt text-primary me-2"></i>
                        Phân quyền hệ thống
                    </h1>
                    <p className="text-muted">Quản lý quyền truy cập theo vai trò</p>
                </div>
                <div className="col-auto">
                    <Link to="/admin" className="btn btn-secondary">
                        <i className="fas fa-arrow-left me-1"></i> Quay lại
                    </Link>
                </div>
            </div>

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

            {errorMessage && (
                <div className="alert alert-danger alert-dismissible fade show" role="alert">
                    <i className="fas fa-exclamation-circle me-2"></i>
                    {errorMessage}
                    <button
                        type="button"
                        className="btn-close"
                        onClick={() => setErrorMessage('')}
                    ></button>
                </div>
            )}

            <div className="row">
                {/* Role Selector */}
                <div className="col-md-3 mb-3">
                    <div className="card">
                        <div className="card-header bg-primary text-white">
                            <h5 className="mb-0">
                                <i className="fas fa-user-tag me-2"></i>
                                Chọn vai trò
                            </h5>
                        </div>
                        <div className="card-body">
                            {loading ? (
                                <div className="text-center py-3">
                                    <div className="spinner-border spinner-border-sm" role="status">
                                        <span className="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            ) : (
                                <div className="list-group">
                                    {roles.map(role => (
                                        <button
                                            key={role.id}
                                            type="button"
                                            className={`list-group-item list-group-item-action ${
                                                selectedRole && selectedRole.id === role.id ? 'active' : ''
                                            }`}
                                            onClick={() => handleRoleChange(role.id)}
                                        >
                                            <div className="d-flex justify-content-between align-items-center">
                                                <span>
                                                    {role.display_name || role.name}
                                                </span>
                                                {role.name === 'admin' && (
                                                    <span className="badge bg-danger">Full</span>
                                                )}
                                            </div>
                                        </button>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>

                    {isAdmin && (
                        <div className="alert alert-warning mt-3" role="alert">
                            <i className="fas fa-info-circle me-2"></i>
                            <small>Admin có toàn quyền, không thể chỉnh sửa</small>
                        </div>
                    )}
                </div>

                {/* Permissions Matrix */}
                <div className="col-md-9">
                    <div className="card">
                        <div className="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 className="mb-0">
                                <i className="fas fa-list-check me-2"></i>
                                Danh sách quyền
                                {selectedRole && (
                                    <span className="ms-2 badge bg-light text-dark">
                                        {selectedRole.display_name || selectedRole.name}
                                    </span>
                                )}
                            </h5>
                            {!isAdmin && selectedRole && (
                                <button
                                    className="btn btn-light btn-sm"
                                    onClick={handleSave}
                                    disabled={saving}
                                >
                                    {saving ? (
                                        <>
                                            <span className="spinner-border spinner-border-sm me-1" role="status"></span>
                                            Đang lưu...
                                        </>
                                    ) : (
                                        <>
                                            <i className="fas fa-save me-1"></i>
                                            Cập nhật
                                        </>
                                    )}
                                </button>
                            )}
                        </div>
                        <div className="card-body">
                            {loading ? (
                                <div className="text-center py-5">
                                    <div className="spinner-border text-primary" role="status">
                                        <span className="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            ) : !selectedRole ? (
                                <div className="text-center py-5 text-muted">
                                    <i className="fas fa-hand-pointer fa-3x mb-3"></i>
                                    <p>Vui lòng chọn vai trò để xem quyền</p>
                                </div>
                            ) : (
                                <div className="permissions-grid">
                                    {Object.keys(groupedPermissions).map(module => (
                                        <div key={module} className="mb-4">
                                            <h6 className="text-uppercase text-primary border-bottom pb-2">
                                                <i className="fas fa-folder me-2"></i>
                                                {module}
                                            </h6>
                                            <div className="row">
                                                {groupedPermissions[module].map(permission => (
                                                    <div key={permission.id} className="col-md-6 col-lg-4 mb-2">
                                                        <div className="form-check">
                                                            <input
                                                                className="form-check-input"
                                                                type="checkbox"
                                                                id={`perm-${permission.id}`}
                                                                checked={
                                                                    isAdmin || selectedPermissions.includes(permission.id)
                                                                }
                                                                onChange={() => handlePermissionToggle(permission.id)}
                                                                disabled={isAdmin}
                                                            />
                                                            <label
                                                                className="form-check-label"
                                                                htmlFor={`perm-${permission.id}`}
                                                                style={{ cursor: isAdmin ? 'not-allowed' : 'pointer' }}
                                                            >
                                                                {permission.display_name || permission.code}
                                                                <br />
                                                                <small className="text-muted">{permission.action}</small>
                                                            </label>
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .permissions-grid {
                    max-height: 600px;
                    overflow-y: auto;
                }

                .form-check-input:disabled {
                    cursor: not-allowed;
                }

                .list-group-item.active {
                    background-color: #0d6efd;
                    border-color: #0d6efd;
                }
            `}</style>
        </div>
    );
}
