import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import LoadingSpinner from '../../Components/Common/LoadingSpinner';

export default function AdminManageRecipesPage() {
    const [recipes, setRecipes] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [successMessage, setSuccessMessage] = useState('');
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [searchTerm, setSearchTerm] = useState('');
    const [filterStatus, setFilterStatus] = useState('pending');

    const { token, user } = useAuth();

    useEffect(() => {
        fetchRecipes();
    }, [currentPage, filterStatus, searchTerm]);

    const fetchRecipes = async () => {
        try {
            setLoading(true);
            const params = new URLSearchParams({
                page: currentPage,
                status: filterStatus,
                search: searchTerm,
                per_page: 10
            });

            const response = await fetch(`/api/v1/admin/recipes?${params}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (response.ok) {
                setRecipes(data.recipes || []);
                setTotalPages(data.pagination?.last_page || 1);
                setError('');
            } else {
                setError(data.message || 'Không thể tải danh sách công thức');
            }
        } catch (err) {
            console.error('Error fetching recipes:', err);
            setError('Có lỗi xảy ra khi tải danh sách công thức');
        } finally {
            setLoading(false);
        }
    };

    const handleApprove = async (recipeId) => {
        if (!window.confirm('Bạn có chắc muốn duyệt công thức này?')) return;

        try {
            const response = await fetch(`/api/v1/admin/recipes/${recipeId}/approve`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (response.ok) {
                setSuccessMessage('Duyệt công thức thành công!');
                fetchRecipes();
                setTimeout(() => setSuccessMessage(''), 3000);
            } else {
                setError(data.message || 'Không thể duyệt công thức');
            }
        } catch (err) {
            console.error('Error approving recipe:', err);
            setError('Có lỗi xảy ra khi duyệt công thức');
        }
    };

    const handleReject = async (recipeId) => {
        if (!window.confirm('Bạn có chắc muốn từ chối công thức này?')) return;

        try {
            const response = await fetch(`/api/v1/admin/recipes/${recipeId}/reject`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (response.ok) {
                setSuccessMessage('Từ chối công thức thành công!');
                fetchRecipes();
                setTimeout(() => setSuccessMessage(''), 3000);
            } else {
                setError(data.message || 'Không thể từ chối công thức');
            }
        } catch (err) {
            console.error('Error rejecting recipe:', err);
            setError('Có lỗi xảy ra khi từ chối công thức');
        }
    };

    const handleDelete = async (recipeId) => {
        if (!window.confirm('Bạn có chắc muốn xóa công thức này?')) return;

        try {
            const response = await fetch(`/api/v1/admin/recipes/${recipeId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (response.ok) {
                setSuccessMessage('Xóa công thức thành công!');
                fetchRecipes();
                setTimeout(() => setSuccessMessage(''), 3000);
            } else {
                setError(data.message || 'Không thể xóa công thức');
            }
        } catch (err) {
            console.error('Error deleting recipe:', err);
            setError('Có lỗi xảy ra khi xóa công thức');
        }
    };

    const getStatusBadge = (status) => {
        const statusMap = {
            pending: { class: 'bg-warning', text: 'Chờ duyệt' },
            approved: { class: 'bg-success', text: 'Đã duyệt' },
            rejected: { class: 'bg-danger', text: 'Từ chối' }
        };
        const statusInfo = statusMap[status] || { class: 'bg-secondary', text: status };
        return <span className={`badge ${statusInfo.class}`}>{statusInfo.text}</span>;
    };

    if (loading) return <LoadingSpinner />;

    return (
        <div className="container-fluid mt-4">
            {/* Page Header */}
            <div className="page-header mb-4">
                <h1>
                    <i className="fas fa-clipboard-check text-primary me-2"></i>
                    Duyệt Công thức
                </h1>
                <p className="text-muted">Quản lý các công thức đang chờ duyệt</p>
            </div>

            {/* Messages */}
            {successMessage && (
                <div className="alert alert-success alert-dismissible fade show" role="alert">
                    <i className="fas fa-check-circle me-2"></i>
                    {successMessage}
                    <button type="button" className="btn-close" onClick={() => setSuccessMessage('')}></button>
                </div>
            )}

            {error && (
                <div className="alert alert-danger alert-dismissible fade show" role="alert">
                    <i className="fas fa-exclamation-circle me-2"></i>
                    {error}
                    <button type="button" className="btn-close" onClick={() => setError('')}></button>
                </div>
            )}

            {/* Filters */}
            <div className="card mb-4">
                <div className="card-body">
                    <div className="row g-3">
                        <div className="col-md-6">
                            <label className="form-label">Tìm kiếm</label>
                            <input
                                type="text"
                                className="form-control"
                                placeholder="Tìm theo tên công thức..."
                                value={searchTerm}
                                onChange={(e) => {
                                    setSearchTerm(e.target.value);
                                    setCurrentPage(1);
                                }}
                            />
                        </div>
                        <div className="col-md-6">
                            <label className="form-label">Trạng thái</label>
                            <select
                                className="form-select"
                                value={filterStatus}
                                onChange={(e) => {
                                    setFilterStatus(e.target.value);
                                    setCurrentPage(1);
                                }}
                            >
                                <option value="pending">Chờ duyệt</option>
                                <option value="approved">Đã duyệt</option>
                                <option value="rejected">Từ chối</option>
                                <option value="">Tất cả</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {/* Table */}
            <div className="card">
                <div className="card-header">
                    <i className="fas fa-table me-1"></i>
                    Danh sách công thức
                </div>
                <div className="card-body">
                    <div className="table-responsive">
                        <table className="table table-bordered table-hover">
                            <thead className="table-light">
                                <tr>
                                    <th style={{ width: '60px' }}>ID</th>
                                    <th>Tên công thức</th>
                                    <th>Người đăng</th>
                                    <th>Danh mục</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th style={{ width: '250px' }}>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                {recipes.length > 0 ? (
                                    recipes.map(recipe => (
                                        <tr key={recipe.id}>
                                            <td>{recipe.id}</td>
                                            <td>
                                                <Link
                                                    to={`/recipes/${recipe.id}`}
                                                    target="_blank"
                                                    className="text-decoration-none"
                                                    title="Xem chi tiết"
                                                >
                                                    {recipe.title}
                                                </Link>
                                            </td>
                                            <td>{recipe.author_name}</td>
                                            <td>{recipe.category_name}</td>
                                            <td>{getStatusBadge(recipe.status)}</td>
                                            <td>{new Date(recipe.created_at).toLocaleString('vi-VN')}</td>
                                            <td>
                                                <div className="btn-group btn-group-sm" role="group">
                                                    {recipe.status === 'pending' && (
                                                        <>
                                                            <button
                                                                className="btn btn-success"
                                                                onClick={() => handleApprove(recipe.id)}
                                                                title="Duyệt"
                                                            >
                                                                <i className="fas fa-check"></i> Duyệt
                                                            </button>
                                                            <button
                                                                className="btn btn-danger"
                                                                onClick={() => handleReject(recipe.id)}
                                                                title="Từ chối"
                                                            >
                                                                <i className="fas fa-times"></i> Từ chối
                                                            </button>
                                                        </>
                                                    )}
                                                    <Link
                                                        to={`/recipes/${recipe.id}`}
                                                        target="_blank"
                                                        className="btn btn-info"
                                                        title="Xem chi tiết"
                                                    >
                                                        <i className="fas fa-eye"></i> Xem
                                                    </Link>
                                                    <button
                                                        className="btn btn-danger"
                                                        onClick={() => handleDelete(recipe.id)}
                                                        title="Xóa"
                                                    >
                                                        <i className="fas fa-trash"></i> Xóa
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="7" className="text-center text-muted py-4">
                                            Không có công thức nào
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {totalPages > 1 && (
                        <nav aria-label="Page navigation" className="mt-4">
                            <ul className="pagination justify-content-center">
                                <li className={`page-item ${currentPage === 1 ? 'disabled' : ''}`}>
                                    <button
                                        className="page-link"
                                        onClick={() => setCurrentPage(1)}
                                        disabled={currentPage === 1}
                                    >
                                        Đầu tiên
                                    </button>
                                </li>
                                <li className={`page-item ${currentPage === 1 ? 'disabled' : ''}`}>
                                    <button
                                        className="page-link"
                                        onClick={() => setCurrentPage(currentPage - 1)}
                                        disabled={currentPage === 1}
                                    >
                                        Trước
                                    </button>
                                </li>

                                {Array.from({ length: totalPages }, (_, i) => i + 1).map(page => (
                                    <li key={page} className={`page-item ${currentPage === page ? 'active' : ''}`}>
                                        <button
                                            className="page-link"
                                            onClick={() => setCurrentPage(page)}
                                        >
                                            {page}
                                        </button>
                                    </li>
                                ))}

                                <li className={`page-item ${currentPage === totalPages ? 'disabled' : ''}`}>
                                    <button
                                        className="page-link"
                                        onClick={() => setCurrentPage(currentPage + 1)}
                                        disabled={currentPage === totalPages}
                                    >
                                        Sau
                                    </button>
                                </li>
                                <li className={`page-item ${currentPage === totalPages ? 'disabled' : ''}`}>
                                    <button
                                        className="page-link"
                                        onClick={() => setCurrentPage(totalPages)}
                                        disabled={currentPage === totalPages}
                                    >
                                        Cuối cùng
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    )}
                </div>
            </div>
        </div>
    );
}
