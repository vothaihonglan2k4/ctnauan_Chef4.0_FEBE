<?php require_once APP_ROOT . '/views/admin/includes/header.php'; ?>

<div class="page-header">
    <h1>
        <i class="fas fa-clipboard-check text-primary me-2"></i>
        Duyệt Công thức
    </h1>
    <p class="page-description">Quản lý các công thức đang chờ duyệt.</p>
</div>

<?php displayMessages(); // Giả sử bạn có hàm helper này để hiển thị flash messages ?>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Danh sách công thức chờ duyệt
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTableRecipes" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên công thức</th>
                        <th>Người đăng</th>
                        <th>Danh mục</th>
                        <th>Ngày tạo</th>
                        <th style="width: 200px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['pending_recipes'])): ?>
                        <?php foreach ($data['pending_recipes'] as $recipe): ?>
                        <tr>
                            <td><?php echo $recipe->id; ?></td>
                            <td>
                                <a href="<?php echo URL_ROOT; ?>/recipes/show/<?php echo $recipe->id; ?>" target="_blank" title="Xem chi tiết (Trang người dùng)">
                                    <?php echo htmlspecialchars($recipe->title); ?>
                                </a>
                                <!-- Link xem trước cho admin (nếu cần) -->
                                <!-- <a href="<?php echo URL_ROOT; ?>/admin/previewRecipe/<?php echo $recipe->id; ?>" target="_blank" class="ms-2"><i class="fas fa-search-plus"></i></a> -->
                            </td>
                            <td><?php echo htmlspecialchars($recipe->author); ?></td>
                            <td><?php echo htmlspecialchars($recipe->category_name); ?></td>
                            <td><?php echo date_format(date_create($recipe->created_at), 'd/m/Y H:i'); ?></td>
                            <td>
                                <form action="<?php echo URL_ROOT; ?>/admin/approveRecipe/<?php echo $recipe->id; ?>" method="post" class="d-inline-block me-1 mb-1" onsubmit="return confirm('Bạn có chắc muốn duyệt công thức này?');">
                                    <button type="submit" class="btn btn-success btn-sm" title="Duyệt">
                                        <i class="fas fa-check"></i> Duyệt
                                    </button>
                                </form>
                                <form action="<?php echo URL_ROOT; ?>/admin/rejectRecipe/<?php echo $recipe->id; ?>" method="post" class="d-inline-block me-1 mb-1" onsubmit="return confirm('Bạn có chắc muốn từ chối công thức này?');">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Từ chối">
                                        <i class="fas fa-times"></i> Từ chối
                                    </button>
                                </form>
                                <a href="<?php echo URL_ROOT; ?>/recipes/edit/<?php echo $recipe->id; ?>" class="btn btn-warning btn-sm d-inline-block me-1 mb-1" title="Sửa công thức & video">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <a href="<?php echo URL_ROOT; ?>/recipes/show/<?php echo $recipe->id; ?>" target="_blank" class="btn btn-info btn-sm d-inline-block mb-1" title="Xem chi tiết (Trang người dùng)">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Không có công thức nào đang chờ duyệt.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/admin/includes/footer.php'; ?>

<!-- Bạn có thể cần thêm DataTables JS nếu chưa có trong footer -->
<!-- <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet"> -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<!-- <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> -->
<!-- <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script> -->
<script>
// $(document).ready(function() {
//     $('#dataTableRecipes').DataTable({
//         "language": {
//             "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json"
//         }
//     });
// });
</script> 