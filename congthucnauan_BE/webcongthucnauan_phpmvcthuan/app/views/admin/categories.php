<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col">
            <h1>Quản lý danh mục</h1>
        </div>
        <div class="col-auto">
            <a href="<?php echo URL_ROOT; ?>/admin" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Thêm danh mục mới</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo URL_ROOT; ?>/admin/categories" method="post">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Thêm danh mục</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Danh sách danh mục</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên danh mục</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['categories'] as $category): ?>
                                    <tr>
                                        <td><?php echo $category->id; ?></td>
                                        <td><?php echo $category->name; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($category->created_at)); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary edit-category-btn" 
                                                data-id="<?php echo $category->id; ?>"
                                                data-name="<?php echo htmlspecialchars($category->name); ?>">
                                                <i class="fas fa-edit"></i> Sửa
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-category-btn" 
                                                data-id="<?php echo $category->id; ?>"
                                                data-name="<?php echo htmlspecialchars($category->name); ?>">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Single Edit Modal (outside the loop) -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa danh mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo URL_ROOT; ?>/admin/categories" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_category_id">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Single Delete Modal (outside the loop) -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa danh mục <strong id="delete-category-name"></strong>?</p>
                    <p class="text-danger">Lưu ý: Việc này có thể ảnh hưởng đến các công thức thuộc danh mục này.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form id="delete-category-form" action="" method="post">
                        <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all edit buttons
    const editButtons = document.querySelectorAll('.edit-category-btn');
    
    // Get the modal elements
    const editModal = document.getElementById('editCategoryModal');
    const editModalInstance = new bootstrap.Modal(editModal);
    const categoryIdInput = document.getElementById('edit_category_id');
    const categoryNameInput = document.getElementById('edit_name');
    
    // Add click event to all edit buttons
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get data from button attributes
            const categoryId = this.getAttribute('data-id');
            const categoryName = this.getAttribute('data-name');
            
            // Set values in the form
            categoryIdInput.value = categoryId;
            categoryNameInput.value = categoryName;
            
            // Show the modal
            editModalInstance.show();
        });
    });

    // Delete modal functionality
    const deleteButtons = document.querySelectorAll('.delete-category-btn');
    const deleteModal = document.getElementById('deleteCategoryModal');
    const deleteModalInstance = new bootstrap.Modal(deleteModal);
    const deleteCategoryName = document.getElementById('delete-category-name');
    const deleteCategoryForm = document.getElementById('delete-category-form');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-id');
            const categoryName = this.getAttribute('data-name');
            
            // Update modal content
            deleteCategoryName.textContent = categoryName;
            
            // Update form action
            deleteCategoryForm.action = '<?php echo URL_ROOT; ?>/admin/deleteCategory/' + categoryId;
            
            // Show the modal
            deleteModalInstance.show();
        });
    });
});
</script>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>
