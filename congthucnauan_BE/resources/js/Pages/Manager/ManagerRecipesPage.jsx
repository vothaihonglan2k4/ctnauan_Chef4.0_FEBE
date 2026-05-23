import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function ManagerRecipesPage() {
    const [recipes, setRecipes] = useState([]);
    const [stats, setStats] = useState({});
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState('all');
    const [successMessage, setSuccessMessage] = useState('');
    const { token } = useAuth();

    useEffect(() => { fetchRecipes(); }, []);

    const fetchRecipes = async () => {
        try {
            const res = await fetch('/api/v1/manager/recipes', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();
            setRecipes(data.recipes || []);
            setStats(data.stats || {});
        } catch (e) { console.error(e); }
        finally { setLoading(false); }
    };

    const handleApprove = async (id, title) => {
        if (!confirm(`Bạn có chắc muốn duyệt công thức "${title}"?`)) return;
        
        const res = await fetch(`/api/v1/manager/recipes/${id}/approve`, {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${token}` }
        });
        
        if (res.ok) {
            setSuccessMessage('Công thức đã được phê duyệt!');
            fetchRecipes();
            setTimeout(() => setSuccessMessage(''), 3000);
        }
    };

    const handleReject = async (id, title) => {
        if (!confirm(`Bạn có chắc muốn từ chối công thức "${title}"?`)) return;
        
        const res = await fetch(`/api/v1/manager/recipes/${id}/reject`, {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${token}` }
        });
        
        if (res.ok) {
            setSuccessMessage('Công thức đã bị từ chối!');
            fetchRecipes();
            setTimeout(() => setSuccessMessage(''), 3000);
        }
    };

    const getStatusBadge = (status) => {
        const map = {
            approved: ['success', 'Đã duyệt'],
            pending: ['warning', 'Chờ duyệt'],
            rejected: ['danger', 'Từ chối']
        };
        const [color, text] = map[status] || ['secondary', status];
        return <span className={`badge bg-${color}`}>{text}</span>;
    };

    const filteredRecipes = filter === 'all' 
        ? recipes 
        : recipes.filter(r => r.status === filter);

    if (loading) {
        return <div className="text-center py-5"><div className="spinner-border text-success"></div></div>;
    }

    return (
        <div>
            {/* Page Header */}
            <div className="page-header">
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        <h1><i className="fas fa-utensils text-success me-2"></i>Quản Lý Công Thức</h1>
                        <p className="page-description">Duyệt và quản lý các công thức nấu ăn</p>
                    </div>
                    <div>
                        <Link to="/manager" className="btn btn-outline-secondary">
                            <i className="fas fa-arrow-left me-1"></i> Về Dashboard
                        </Link>
                    </div>
                </div>
            </div>

            {successMessage && (
                <div className="alert alert-success alert-dismissible fade show">
                    <i className="fas fa-check-circle me-2"></i>{successMessage}
                    <button type="button" className="btn-close" onClick={() => setSuccessMessage('')}></button>
                </div>
            )}

            {/* Filter Tabs */}
            <div className="row mb-4">
                <div className="col-12">
                    <div className="card">
                        <div className="card-body p-3">
                            <ul className="nav nav-pills justify-content-center">
                                <li className="nav-item">
                                    <button className={`nav-link ${filter === 'all' ? 'active' : ''}`} onClick={() => setFilter('all')}>
                                        <i className="fas fa-list me-1"></i>Tất cả ({stats.total || 0})
                                    </button>
                                </li>
                                <li className="nav-item">
                                    <button className={`nav-link ${filter === 'pending' ? 'active' : ''}`} onClick={() => setFilter('pending')}>
                                        <i className="fas fa-clock me-1"></i>Chờ duyệt ({stats.pending || 0})
                                    </button>
                                </li>
                                <li className="nav-item">
                                    <button className={`nav-link ${filter === 'approved' ? 'active' : ''}`} onClick={() => setFilter('approved')}>
                                        <i className="fas fa-check me-1"></i>Đã duyệt ({stats.approved || 0})
                                    </button>
                                </li>
                                <li className="nav-item">
                                    <button className={`nav-link ${filter === 'rejected' ? 'active' : ''}`} onClick={() => setFilter('rejected')}>
                                        <i className="fas fa-times me-1"></i>Đã từ chối ({stats.rejected || 0})
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>


            {/* Recipes List */}
            <div className="row">
                {filteredRecipes.length === 0 ? (
                    <div className="col-12">
                        <div className="card">
                            <div className="card-body text-center py-5">
                                <i className="fas fa-utensils fa-4x text-muted mb-3"></i>
                                <h4 className="text-muted">Chưa có công thức nào</h4>
                                <p className="text-muted">Các công thức sẽ hiển thị tại đây khi có người dùng tạo mới.</p>
                            </div>
                        </div>
                    </div>
                ) : (
                    filteredRecipes.map(recipe => (
                        <div key={recipe.id} className="col-lg-4 col-md-6 mb-4">
                            <div className="card h-100 recipe-card">
                                {/* Recipe Image */}
                                <div className="position-relative overflow-hidden">
                                    <img 
                                        src={recipe.image ? `/uploads/${recipe.image}` : '/img/default-recipe.jpg'} 
                                        className="card-img-top" 
                                        style={{ height: '200px', objectFit: 'cover', transition: 'transform 0.3s' }}
                                        alt={recipe.title}
                                    />
                                    <span className={`position-absolute top-0 end-0 m-2 badge bg-${
                                        recipe.status === 'approved' ? 'success' : 
                                        recipe.status === 'pending' ? 'warning' : 'danger'
                                    }`}>
                                        {recipe.status === 'approved' ? 'Đã duyệt' : 
                                         recipe.status === 'pending' ? 'Chờ duyệt' : 'Từ chối'}
                                    </span>
                                </div>
                                
                                <div className="card-body">
                                    <h5 className="card-title">{recipe.title}</h5>
                                    <p className="card-text text-muted small">
                                        {recipe.description?.substring(0, 100)}...
                                    </p>
                                    
                                    {/* Recipe Info */}
                                    <div className="row text-center mb-3">
                                        <div className="col-4">
                                            <div className="text-muted small">Tác giả</div>
                                            <div className="fw-bold small">{recipe.user?.name || 'N/A'}</div>
                                        </div>
                                        <div className="col-4">
                                            <div className="text-muted small">Danh mục</div>
                                            <div className="fw-bold small">{recipe.category?.name || 'N/A'}</div>
                                        </div>
                                        <div className="col-4">
                                            <div className="text-muted small">Ngày tạo</div>
                                            <div className="fw-bold small">{new Date(recipe.created_at).toLocaleDateString('vi-VN')}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                {/* Action Buttons */}
                                <div className="card-footer bg-light">
                                    <div className="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div>
                                            <Link to={`/recipes/${recipe.id}`} target="_blank" className="btn btn-sm btn-outline-primary">
                                                <i className="fas fa-eye me-1"></i> Xem
                                            </Link>
                                        </div>
                                        
                                        {recipe.status === 'pending' ? (
                                            <div>
                                                <button 
                                                    className="btn btn-sm btn-success me-1"
                                                    onClick={() => handleApprove(recipe.id, recipe.title)}
                                                >
                                                    <i className="fas fa-check me-1"></i> Duyệt
                                                </button>
                                                <button 
                                                    className="btn btn-sm btn-danger"
                                                    onClick={() => handleReject(recipe.id, recipe.title)}
                                                >
                                                    <i className="fas fa-times me-1"></i> Từ chối
                                                </button>
                                            </div>
                                        ) : (
                                            <span className={`badge bg-${recipe.status === 'approved' ? 'success' : 'secondary'}`}>
                                                {recipe.status === 'approved' ? 'Đã xử lý' : 'Đã từ chối'}
                                            </span>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))
                )}
            </div>

            <style>{`
                .page-header {
                    background: white;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    margin-bottom: 20px;
                    border-left: 4px solid #198754;
                }
                .page-header h1 { margin: 0; font-size: 1.5rem; font-weight: 600; }
                .page-description { margin: 5px 0 0 0; color: #6c757d; }
                
                .nav-pills .nav-link {
                    color: #6c757d;
                    border-radius: 20px;
                    margin: 0 5px;
                    transition: all 0.3s ease;
                    border: none;
                    background: transparent;
                }
                .nav-pills .nav-link.active,
                .nav-pills .nav-link:hover {
                    background-color: #198754;
                    color: white;
                }
                
                .recipe-card {
                    transition: all 0.3s ease;
                }
                .recipe-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                }
                .recipe-card:hover .card-img-top {
                    transform: scale(1.05);
                }
            `}</style>
        </div>
    );
}
