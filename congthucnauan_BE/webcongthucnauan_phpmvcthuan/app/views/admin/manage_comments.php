<?php require_once APP_ROOT . '/views/admin/includes/header.php'; ?>

<div class="page-header">
    <h1>
        <i class="fas fa-comments text-primary me-2"></i>
        Quản lý Bình luận & Đánh giá
    </h1>
    <p class="page-description">Xem và xóa các đánh giá có chứa bình luận của người dùng.</p>
</div>

<?php displayMessages(); ?>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Danh sách bình luận (Tổng: <?php echo $data['total_comments'] ?? 0; ?>)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTableComments" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Rating</th>
                        <th style="width: 30%;">Bình luận</th>
                        <th>Người dùng</th>
                        <th>Công thức</th>
                        <th>Ngày tạo</th>
                        <th style="width: 100px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['comments'])): // Dùng biến 'comments' từ controller ?>
                        <?php foreach ($data['comments'] as $rating): // Lặp qua các ratings có comment ?>
                        <tr>
                            <td><?php echo $rating->id; ?></td>
                            <td>
                                <?php 
                                // Hiển thị sao đánh giá
                                for ($i = 1; $i <= 5; $i++) {
                                    echo '<i class="' . ($i <= $rating->rating ? 'fas' : 'far') . ' fa-star text-warning"></i>';
                                }
                                ?>
                                (<?php echo $rating->rating; ?>)
                            </td>
                            <td><?php echo nl2br(htmlspecialchars($rating->comment)); ?></td>
                            <td><?php echo htmlspecialchars($rating->user_name); ?> (ID: <?php echo $rating->user_id; ?>)</td>
                            <td>
                                <a href="<?php echo URL_ROOT; ?>/recipes/show/<?php echo $rating->recipe_id; ?>" target="_blank" title="Xem công thức">
                                    <?php echo htmlspecialchars($rating->recipe_title); ?> (ID: <?php echo $rating->recipe_id; ?>)
                                </a>
                            </td>
                            <td><?php echo date_format(date_create($rating->created_at), 'd/m/Y H:i'); ?></td>
                            <td>
                                <!-- Form xóa gọi đến hàm deleteComment trong controller (nhưng hàm đó gọi deleteRating) -->
                                <form action="<?php echo URL_ROOT; ?>/admin/deleteComment/<?php echo $rating->id; ?>" method="post" class="d-inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá và bình luận này? Thao tác này không thể hoàn tác.');">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Xóa đánh giá & bình luận">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Không có bình luận nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/views/admin/includes/footer.php'; ?>

<script>
// $(document).ready(function() {
//     $('#dataTableComments').DataTable({
//         "language": {
//             "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json"
//         },
//         "order": [[ 5, "desc" ]] // Sắp xếp theo ngày tạo (cột thứ 6)
//     });
// });
</script> 