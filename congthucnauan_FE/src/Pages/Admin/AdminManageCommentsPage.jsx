import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import LoadingSpinner from '../../Components/Common/LoadingSpinner';

export default function AdminManageCommentsPage() {
    const [comments, setComments] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [successMessage, setSuccessMessage] = useState('');
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedComments, setSelectedComments] = useState([]);

    const { token } = useAuth();

    useEffect(() => {
        fetchComments();
    }, [currentPage, searchTerm]);

    const fetchComments = async () => {
        try {
            setLoading(true);
            const params = new URLSearchParams({
                page: currentPage,
                search: searchTerm,
                per_page: 10
            });

            const response = await fetch(`/api/v1/admin/comments?${params}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (response.ok) {
                setComments(data.comments || []);
                setTotalPages(data.pagination?.last_page || 1);
                setError('');
                setSelectedComments([]);
            } else {
                setError(data.message || 'Không thể tải danh sách bình luận');
            }
        } catch (err) {
            console.error('Error fetching comments:', err);
            setError('Có lỗi xảy ra khi tải danh sách bình luận');
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteComment = async (commentId) => {
        if (!window.confirm('Bạn có chắc muốn xóa bình luận này? Thao tác này không thể hoàn tác.')) return;

        try {
            const response = await fetch(`/api/v1/admin/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (response.ok) {
                setSuccessMessage('Xóa bình luận thành công!');
                fetchComments();
                setTimeout(() => setSuccessMessage(''), 3000);
            } else {
                setError(data.message || 'Không thể xóa bình luận');
            }
        } catch (err) {
            console.error('Error deleting comment:', err);
            setError('Có lỗi xảy ra khi xóa bình luận');
        }
    };

    const handleDeleteMultiple = async () => {
        if (selectedComments.length === 0) {
            alert('Vui lòng chọn ít nhất một bình luận');
            return;
        }

        if (!window.confirm(`Bạn có chắc muốn xóa ${selectedComments.length} bình luận? Thao tác này không thể hoàn tác.`)) return;

        try {
            const response = await fetch(`/api/v1/admin/comments/delete-multiple`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: selectedComments })
            });

            const data = await response.json();
            if (response.ok) {
                setSuccessMessage(data.message);
                fetchComments();
                setTimeout(() => setSuccessMessage(''), 3000);
            } else {
                setError(data.message || 'Không thể xóa bình luận');
            }
        } catch (err) {
            console.error('Error deleting comments:', err);
            setError('Có lỗi xảy ra khi xóa bình luận');
        }
    };

    const handleSelectComment = (commentId) => {
        setSelectedComments(prev =>
            prev.includes(commentId)
                ? prev.filter(id => id !== commentId)
                : [...prev, commentId]
        );
    };

    const handleSelectAll = (e) => {
        if (e.target.checked) {
            setSelectedComments(comments.map(c => c.id));
        } else {
            setSelectedComments([]);
        }
    };

    if (loading) return <LoadingSpinner />;

    return (
        <div className="container-fluid mt-4">
            {/* Page Header */}
            <div className="page-header mb-4">
                <h1>
                    <i className="fas fa-comments text-primary me-2"></i>
                    Quản lý Bình luận
                </h1>
                <p className="text-muted">Xem và xóa các bình luận của người dùng trên diễn đàn</p>
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
                        <div className="col-md-8">
                            <label className="form-label">Tìm kiếm</label>
                            <input
                                type="text"
                                className="form-control"
                                placeholder="Tìm theo nội dung, người dùng hoặc bài viết..."
                                value={searchTerm}
                                onChange={(e) => {
                                    setSearchTerm(e.target.value);
                                    setCurrentPage(1);
                                }}
                            />
                        </div>
                        <div className="col-md-4">
                            <label className="form-label">&nbsp;</label>
                            {selectedComments.length > 0 && (
                                <button
                                    className="btn btn-danger w-100"
                                    onClick={handleDeleteMultiple}
                                >
                                    <i className="fas fa-trash me-2"></i>
                                    Xóa ({selectedComments.length})
                                </button>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Table */}
            <div className="card">
                <div className="card-header">
                    <i className="fas fa-table me-1"></i>
                    Danh sách bình luận (Tổng: {comments.length})
                </div>
                <div className="card-body">
                    <div className="table-responsive">
                        <table className="table table-bordered table-hover">
                            <thead className="table-light">
                                <tr>
                                    <th style={{ width: '40px' }}>
                                        <input
                                            type="checkbox"
                                            className="form-check-input"
                                            checked={selectedComments.length === comments.length && comments.length > 0}
                                            onChange={handleSelectAll}
                                        />
                                    </th>
                                    <th style={{ width: '60px' }}>ID</th>
                                    <th style={{ width: '30%' }}>Bình luận</th>
                                    <th>Người dùng</th>
                                    <th>Bài viết</th>
                                    <th>Ngày tạo</th>
                                    <th style={{ width: '100px' }}>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                {comments.length > 0 ? (
                                    comments.map(comment => (
                                        <tr key={comment.id}>
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    className="form-check-input"
                                                    checked={selectedComments.includes(comment.id)}
                                                    onChange={() => handleSelectComment(comment.id)}
                                                />
                                            </td>
                                            <td>{comment.id}</td>
                                            <td>
                                                <div style={{
                                                    maxHeight: '100px',
                                                    overflow: 'auto',
                                                    whiteSpace: 'pre-wrap',
                                                    wordBreak: 'break-word'
                                                }}>
                                                    {comment.content}
                                                </div>
                                            </td>
                                            <td>{comment.user_name}</td>
                                            <td>
                                                <Link
                                                    to={`/forum/${comment.post_id}`}
                                                    target="_blank"
                                                    className="text-decoration-none"
                                                    title="Xem bài viết"
                                                >
                                                    {comment.post_title}
                                                </Link>
                                            </td>
                                            <td>{new Date(comment.created_at).toLocaleString('vi-VN')}</td>
                                            <td>
                                                <button
                                                    className="btn btn-danger btn-sm"
                                                    onClick={() => handleDeleteComment(comment.id)}
                                                    title="Xóa bình luận"
                                                >
                                                    <i className="fas fa-trash"></i> Xóa
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="7" className="text-center text-muted py-4">
                                            Không có bình luận nào
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
