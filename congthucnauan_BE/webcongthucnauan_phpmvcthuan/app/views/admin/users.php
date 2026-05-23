<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col">
            <h1>Quản lý người dùng</h1>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus"></i> Thêm người dùng
            </button>
            <a href="<?php echo URL_ROOT; ?>/admin" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
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
                        <?php foreach($data['users'] as $user): ?>
                            <tr>
                                <td><?php echo $user->id; ?></td>
                                <td><?php echo $user->name; ?></td>
                                <td><?php echo $user->email; ?></td>
                                <td>
                                    <?php if($user->role == 'admin'): ?>
                                        <span class="badge bg-danger">Quản trị viên</span>
                                    <?php elseif($user->role == 'manager'): ?>
                                        <span class="badge bg-warning">Quản lý</span>
                                    <?php else: ?>
                                        <span class="badge bg-info">Người dùng</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($user->created_at)); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-info btn-sm" onclick="viewUser(<?php echo $user->id; ?>)">
                                            <i class="fas fa-eye"></i> Xem
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm" onclick="editUser(<?php echo $user->id; ?>)">
                                            <i class="fas fa-edit"></i> Sửa
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteUser(<?php echo $user->id; ?>)">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Người Dùng -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Thêm người dùng mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm" method="POST" action="<?php echo URL_ROOT; ?>/admin/add_user">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addUserName" class="form-label">Tên:</label>
                        <input type="text" class="form-control" id="addUserName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="addUserEmail" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="addUserEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="addUserPassword" class="form-label">Mật khẩu:</label>
                        <input type="password" class="form-control" id="addUserPassword" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="addUserRole" class="form-label">Vai trò:</label>
                        <select class="form-select" id="addUserRole" name="role" required>
                            <option value="user" selected>Người dùng</option>
                            <option value="manager">Quản lý</option>
                            <option value="admin">Quản trị viên</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm người dùng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xem Chi Tiết -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewUserModalLabel">Chi tiết người dùng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">ID:</label>
                    <p id="viewUserId"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tên:</label>
                    <p id="viewUserName"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <p id="viewUserEmail"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Vai trò:</label>
                    <p id="viewUserRole"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ngày đăng ký:</label>
                    <p id="viewUserCreatedAt"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sửa Người Dùng -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Sửa thông tin người dùng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST" action="<?php echo URL_ROOT; ?>/admin/updateUser">
                <div class="modal-body">
                    <input type="hidden" id="editUserId" name="user_id">
                    <div class="mb-3">
                        <label for="editUserName" class="form-label">Tên:</label>
                        <input type="text" class="form-control" id="editUserName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editUserEmail" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="editUserEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="editUserRole" class="form-label">Vai trò:</label>
                        <select class="form-select" id="editUserRole" name="role" required>
                            <option value="user">Người dùng</option>
                            <option value="manager">Quản lý</option>
                            <option value="admin">Quản trị viên</option>
                        </select>
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

<!-- Modal Xóa Người Dùng -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa người dùng này?</p>
                <p class="text-danger">Lưu ý: Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteUserForm" method="POST" action="<?php echo URL_ROOT; ?>/admin/deleteUser">
                    <input type="hidden" id="deleteUserId" name="user_id">
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Không thể xóa tài khoản của chính mình -->
<div class="modal fade" id="cantDeleteSelfDeleteModal" tabindex="-1" aria-labelledby="cantDeleteSelfDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cantDeleteSelfDeleteModalLabel">Thông báo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn không thể xóa tài khoản của chính mình.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Không thể tự chuyển quyền admin thành user -->
<div class="modal fade" id="cantDemoteAdminModal" tabindex="-1" aria-labelledby="cantDemoteAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cantDemoteAdminModalLabel">Thông báo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn không thể tự chuyển quyền admin của mình thành người dùng được.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
// Định nghĩa URL_ROOT cho JavaScript
const URL_ROOT = '<?php echo URL_ROOT; ?>';
const CURRENT_USER_ID = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

// Hàm hiển thị modal xem chi tiết
function viewUser(userId) {
    console.log('Đang xem thông tin user:', userId);
    // Gọi API để lấy thông tin người dùng
    fetch(`${URL_ROOT}/admin/getUser/${userId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(user => {
            console.log('Dữ liệu user:', user);
            document.getElementById('viewUserId').textContent = user.id;
            document.getElementById('viewUserName').textContent = user.name;
            document.getElementById('viewUserEmail').textContent = user.email;
            let roleText = 'Người dùng';
            if (user.role === 'admin') roleText = 'Quản trị viên';
            else if (user.role === 'manager') roleText = 'Quản lý';
            document.getElementById('viewUserRole').textContent = roleText;
            document.getElementById('viewUserCreatedAt').textContent = new Date(user.created_at).toLocaleDateString('vi-VN');
            
            // Hiển thị modal
            const modal = new bootstrap.Modal(document.getElementById('viewUserModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Lỗi khi lấy thông tin user:', error);
            alert('Có lỗi xảy ra khi lấy thông tin người dùng');
        });
}

// Hàm hiển thị modal sửa
function editUser(userId) {
    console.log('Đang sửa thông tin user:', userId);
    // Gọi API để lấy thông tin người dùng
    fetch(`${URL_ROOT}/admin/getUser/${userId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(user => {
            console.log('Dữ liệu user để sửa:', user);
            document.getElementById('editUserId').value = user.id;
            document.getElementById('editUserName').value = user.name;
            document.getElementById('editUserEmail').value = user.email;
            document.getElementById('editUserRole').value = user.role;
            
            // Hiển thị modal
            const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Lỗi khi lấy thông tin user để sửa:', error);
            alert('Có lỗi xảy ra khi lấy thông tin người dùng');
        });
}

// Hàm hiển thị modal xóa
function deleteUser(userId) {
    document.getElementById('deleteUserId').value = userId;
    const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
    modal.show();
}

// Thêm event listener cho form sửa người dùng
document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const userId = document.getElementById('editUserId').value;
    const selectedRole = document.getElementById('editUserRole').value;
    // Nếu admin tự phế truất vai trò quản trị của mình
    if (parseInt(userId) === CURRENT_USER_ID && selectedRole !== 'admin') {
        // Đóng modal sửa
        const editModal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
        editModal.hide();
        // Hiển thị modal cảnh báo đúng
        const infoModal = new bootstrap.Modal(document.getElementById('cantDemoteAdminModal'));
        infoModal.show();
        return;
    }
    const formData = new FormData(this);
    fetch(`${URL_ROOT}/admin/edit_user/${userId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(() => {
        // Đóng modal và reload trang
        const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
        modal.hide();
        window.location.reload();
    })
    .catch(error => {
        console.error('Lỗi khi cập nhật người dùng:', error);
        alert('Có lỗi xảy ra khi cập nhật thông tin người dùng');
    });
});

// Thêm event listener cho form xóa người dùng
document.getElementById('deleteUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const userId = document.getElementById('deleteUserId').value;
    if (parseInt(userId) === CURRENT_USER_ID) {
        // Đóng modal xác nhận xóa
        const delModal = bootstrap.Modal.getInstance(document.getElementById('deleteUserModal'));
        delModal.hide();
        // Hiển thị modal thông báo không thể xóa chính mình
        const infoModal = new bootstrap.Modal(document.getElementById('cantDeleteSelfDeleteModal'));
        infoModal.show();
        return;
    }
    fetch(`${URL_ROOT}/admin/delete_user/${userId}`, {
        method: 'POST'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(() => {
        // Đóng modal và reload trang
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteUserModal'));
        modal.hide();
        window.location.reload();
    })
    .catch(error => {
        console.error('Lỗi khi xóa người dùng:', error);
        alert('Có lỗi xảy ra khi xóa người dùng');
    });
});

// Kiểm tra xem Bootstrap đã được load chưa
document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap chưa được load!');
        alert('Có lỗi xảy ra: Bootstrap chưa được load');
    } else {
        console.log('Bootstrap đã được load thành công');
    }
});
</script>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>
