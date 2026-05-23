import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function AdminCategoriesPage() {
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [selectedCategory, setSelectedCategory] = useState(null);
    const [formData, setFormData] = useState({ name: '' });
    const [editFormData, setEditFormData] = useState({ name: '' });
    const [errors, setErrors] = useState({});
    const [successMessage, setSuccessMessage] = useState('');

    const { token } = useAuth();

    useEffect(() => {
        fetchCategories();
    }, []);

    const fetchCategories = async () => {
        try {
            const response = await fetch('/api/v1/admin/categories', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (response.ok) {
                setCategories(data.categories || []);
            }
        } catch (error) {
            console.error('Error fetching categories:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleAddCategory = async (e) => {
        e.preventDefault();
        setErrors({});

        try {
            const response = await fetch('/api/v1/admin/categories', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (response.ok) {
                setSuccessMessage('Thêm danh mục thành công!');
                setFormData({ name: '' });
                fetchCategories();
                setTimeout(() => setSuccessMessage(''), 3000);
            } else {
                setErrors(data.errors || { general: data.message });
            }
        } catch (error) {
            console.error('Error adding category:', error);
            setErrors({ general: 'Có lỗi xảy ra khi thêm danh mục' });
        }
    };

    const handleEditCategory = async (e) => {
        e.preventDefault();
        setErrors({});

        try {
            const response = await fetch(`/api/v1/admin/categories/${selectedCategory.id}`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(editFormData)
            });

            const data = await response.json();

            if (response.ok) {
                setSuccessMessage('Cập nhật danh mục thành công!');
                setShowEditModal(false);
                fetchCategories();
                setTimeout(() => setSuccessMessage(''), 3000);
            } else {
                setErrors(data.errors || { general: data.message });
            }
        } catch (error) {
            console.error('Error updating category:', error);
            setErrors({ general: 'Có lỗi xảy ra khi cập nhật danh mục' });
        }
    };

    const handleDeleteCategory = async () => {
        try {
            const response = await fetch(`/api/v1/admin/categories/${selectedCategory.id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok) {
                setSuccessMessage('Xóa danh mục thành công!');
                setShowDeleteModal(false);
                fetchCategories();
                setTimeout(() => setSuccessMessage(''), 3000);
            } else {
                alert(data.message);
                setShowDeleteModal(false);
            }
        } catch (error) {
            console.error('Error deleting category:', error);
            alert('Có lỗi xảy ra khi xóa danh mục');
        }
    };

    const openEditModal = (category) => {
        setSelectedCategory(category);
        setEditFormData({ name: category.name });
        setErrors({});
        setShowEditModal(true);
    };

    const openDeleteModal = (category) => {
        setSelectedCategory(category);
        setShowDeleteModal(true);
    };

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    };

    return (
        <div>
            {/* Page Header */}
            <div className="row mb-3">
                <div className="col">
                    <h1>
                        <i className="fas fa-folder text-primary me-2"></i>
                        Quản lý danh mục
                    </h1>
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

            <div className="row">
                {/* Add Category Form */}
                <div className="col-md-4">
                    <div className="card mb-4">
                        <div className="card-header bg-primary text-white">
                            <h5 className="mb-0">Thêm danh mục mới</h5>
                        </div>
                        <div className="card-body">
                            <form onSubmit={handleAddCategory}>
                                {errors.general && (
                                    <div className="alert alert-danger">{errors.general}</div>
                                )}
                                <div className="mb-3">
                                    <label className="form-label">
                                        Tên danh mục <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                                        value={formData.name}
                                        onChange={(e) => setFormData({ name: e.target.value })}
                                        required
                                    />
                                    {errors.name && (
                                        <div className="invalid-feedback">{errors.name[0]}</div>
                                    )}
                                </div>
                                <div className="d-grid">
                                    <button type="submit" className="btn btn-primary">
                                        <i className="fas fa-plus me-1"></i>
                                        Thêm danh mục
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {/* Categories List */}
                <div className="col-md-8">
                    <div className="card">
                        <div className="card-header bg-light">
                            <h5 className="mb-0">Danh sách danh mục</h5>
                        </div>
                        <div className="card-body">
                            {loading ? (
                                <div className="text-center py-5">
                                    <div className="spinner-border text-primary" role="status">
                                        <span className="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            ) : categories.length === 0 ? (
                                <div className="alert alert-info">
                                    <i className="fas fa-info-circle me-2"></i>
                                    Chưa có danh mục nào.
                                </div>
                            ) : (
                                <div className="table-responsive">
                                    <table className="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tên danh mục</th>
                                                <th>Ngày tạo</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {categories.map(category => (
                                                <tr key={category.id}>
                                                    <td>{category.id}</td>
                                                    <td>{category.name}</td>
                                                    <td>{formatDate(category.created_at)}</td>
                                                    <td>
                                                        <button 
                                                            className="btn btn-sm btn-primary me-1"
                                                            onClick={() => openEditModal(category)}
                                                        >
                                                            <i className="fas fa-edit"></i> Sửa
                                                        </button>
                                                        <button 
                                                            className="btn btn-sm btn-danger"
                                                            onClick={() => openDeleteModal(category)}
                                                        >
                                                            <i className="fas fa-trash"></i> Xóa
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
                </div>
            </div>

            {/* Edit Category Modal */}
            {showEditModal && selectedCategory && (
                <div className="modal show d-block" tabIndex="-1" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title">Sửa danh mục</h5>
                                <button 
                                    type="button" 
                                    className="btn-close" 
                                    onClick={() => setShowEditModal(false)}
                                ></button>
                            </div>
                            <form onSubmit={handleEditCategory}>
                                <div className="modal-body">
                                    {errors.general && (
                                        <div className="alert alert-danger">{errors.general}</div>
                                    )}
                                    <div className="mb-3">
                                        <label className="form-label">
                                            Tên danh mục <span className="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                                            value={editFormData.name}
                                            onChange={(e) => setEditFormData({ name: e.target.value })}
                                            required
                                        />
                                        {errors.name && (
                                            <div className="invalid-feedback">{errors.name[0]}</div>
                                        )}
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
                                    <button type="submit" className="btn btn-primary">
                                        Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}

            {/* Delete Category Modal */}
            {showDeleteModal && selectedCategory && (
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
                                <p>
                                    Bạn có chắc chắn muốn xóa danh mục <strong>{selectedCategory.name}</strong>?
                                </p>
                                <p className="text-danger">
                                    Lưu ý: Việc này có thể ảnh hưởng đến các công thức thuộc danh mục này.
                                </p>
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
                                    onClick={handleDeleteCategory}
                                >
                                    Xác nhận xóa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
