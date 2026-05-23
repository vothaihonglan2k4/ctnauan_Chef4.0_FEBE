import { Link } from 'react-router-dom';

export default function RecentUsersTable({ users, loading }) {
    const getRoleBadge = (role) => {
        if (role === 'admin') {
            return <span className="badge bg-danger">Admin</span>;
        } else if (role === 'manager') {
            return <span className="badge bg-info">Manager</span>;
        }
        return <span className="badge bg-secondary">Người dùng</span>;
    };

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    };

    if (loading) {
        return (
            <div className="text-center py-5">
                <div className="spinner-border text-primary" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
            </div>
        );
    }

    if (!users || users.length === 0) {
        return (
            <div className="alert alert-info">
                <i className="fas fa-info-circle me-2"></i>
                Chưa có người dùng nào.
            </div>
        );
    }

    return (
        <div className="table-responsive">
            <table className="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Ngày đăng ký</th>
                        <th>Vai trò</th>
                    </tr>
                </thead>
                <tbody>
                    {users.map(user => (
                        <tr key={user.id}>
                            <td>
                                <div className="d-flex align-items-center">
                                    <img 
                                        src={user.avatar && user.avatar !== 'default-avatar.png' 
                                            ? `/uploads/avatars/${user.avatar}` 
                                            : '/img/default-avatar.png'
                                        }
                                        className="rounded-circle me-2" 
                                        width="32" 
                                        height="32"
                                        alt={user.name}
                                        style={{ objectFit: 'cover' }}
                                    />
                                    {user.name}
                                </div>
                            </td>
                            <td>{user.email}</td>
                            <td>{formatDate(user.created_at)}</td>
                            <td>{getRoleBadge(user.role)}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
