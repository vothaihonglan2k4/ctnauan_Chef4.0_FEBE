<?php require_once APP_ROOT . '/views/includes/header.php'; ?>

<!-- Thêm style cho canvas biểu đồ -->
<style>
canvas {
    max-height: 300px;
    width: 100% !important;
    height: 100% !important;
}
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}
</style>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col">
            <h1>Báo cáo thống kê</h1>
        </div>
        <div class="col-auto">
            <a href="<?php echo URL_ROOT; ?>/admin" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h1 class="display-4"><?php echo $data['userCount']; ?></h1>
                    <p class="lead">Người dùng</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h1 class="display-4"><?php echo $data['recipeCount']; ?></h1>
                    <p class="lead">Công thức</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h1 class="display-4"><?php echo $data['categoryCount']; ?></h1>
                    <p class="lead">Danh mục</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h1 class="display-4"><?php echo $data['paymentCount']; ?></h1>
                    <p class="lead">Thanh toán</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Công thức theo danh mục</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <table class="table table-sm mt-2">
                        <thead>
                            <tr>
                                <th>Danh mục</th>
                                <th>Số lượng công thức</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['categoryData'] as $category => $count): ?>
                                <tr>
                                    <td><?php echo $category; ?></td>
                                    <td><?php echo $count; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Doanh thu theo tháng</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                    <table class="table table-sm mt-2">
                        <thead>
                            <tr>
                                <th>Tháng</th>
                                <th>Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['paymentsByMonth'] as $month => $amount): ?>
                                <tr>
                                    <td><?php echo $month; ?></td>
                                    <td><?php echo number_format($amount); ?>₫</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thống kê người dùng mới</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="userRegistrationChart"></canvas>
                    </div>
                    <div class="mt-3 text-center">
                        <p class="mb-1">Tổng số người dùng: <strong><?php echo $data['userCount']; ?></strong></p>
                        <p class="mb-1">Người dùng mới trong tháng này: 
                            <strong>
                                <?php
                                $userModel = new User();
                                $userModel->query('SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_FORMAT(NOW() ,"%Y-%m-01")');
                                echo $userModel->single()->count;
                                ?>
                            </strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Báo cáo chi tiết</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true">Người dùng</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="recipes-tab" data-bs-toggle="tab" data-bs-target="#recipes" type="button" role="tab" aria-controls="recipes" aria-selected="false">Công thức</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab" aria-controls="payments" aria-selected="false">Thanh toán</button>
                        </li>
                    </ul>
                    <div class="tab-content p-3" id="reportTabsContent">
                        <div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="users-tab">
                            <h4>Thống kê người dùng</h4>
                            <p>Tổng số người dùng đăng ký: <?php echo $data['userCount']; ?></p>
                            <p>Số người mới đăng ký trong 30 ngày qua: 
                                <?php
                                $userModel = new User();
                                $userModel->query('SELECT COUNT(*) as count FROM users WHERE created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)');
                                echo $userModel->single()->count;
                                ?>
                            </p>
                        </div>
                        <div class="tab-pane fade" id="recipes" role="tabpanel" aria-labelledby="recipes-tab">
                            <h4>Thống kê công thức</h4>
                            <p>Tổng số công thức: <?php echo $data['recipeCount']; ?></p>
                            <p>Số công thức mới trong 30 ngày qua: 
                                <?php
                                $recipeModel = new Recipe();
                                $recipeModel->query('SELECT COUNT(*) as count FROM recipes WHERE created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)');
                                echo $recipeModel->single()->count;
                                ?>
                            </p>
                            <h5 class="mt-4">Công thức phổ biến nhất</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tên công thức</th>
                                        <th>Danh mục</th>
                                        <th>Người đăng</th>
                                        <th>Số đánh giá</th>
                                        <th>Điểm trung bình</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $recipeModel->query('SELECT r.id, r.title, c.name as category_name, u.name as author, 
                                                COUNT(rt.id) as rating_count, AVG(rt.rating) as avg_rating 
                                                FROM recipes r 
                                                INNER JOIN categories c ON r.category_id = c.id 
                                                INNER JOIN users u ON r.user_id = u.id 
                                                LEFT JOIN ratings rt ON r.id = rt.recipe_id 
                                                GROUP BY r.id, r.title, c.name, u.name 
                                                ORDER BY avg_rating DESC, rating_count DESC 
                                                LIMIT 5');
                                    $popularRecipes = $recipeModel->resultSet();
                                    
                                    foreach($popularRecipes as $recipe):
                                    ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo URL_ROOT; ?>/recipes/show/<?php echo $recipe->id; ?>" target="_blank">
                                                    <?php echo $recipe->title; ?>
                                                </a>
                                            </td>
                                            <td><?php echo $recipe->category_name; ?></td>
                                            <td><?php echo $recipe->author; ?></td>
                                            <td><?php echo $recipe->rating_count; ?></td>
                                            <td>
                                                <?php 
                                                $rating = round($recipe->avg_rating, 1);
                                                echo $rating ? $rating : 'Chưa có đánh giá'; 
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
                            <h4>Thống kê thanh toán</h4>
                            <p>Tổng số giao dịch: <?php echo $data['paymentCount']; ?></p>
                            <p>Tổng doanh thu: <?php 
                                $totalRevenue = 0;
                                foreach($data['paymentsByMonth'] as $amount) {
                                    $totalRevenue += $amount;
                                }
                                echo number_format($totalRevenue); 
                            ?>₫</p>
                            
                            <h5 class="mt-4">Thống kê theo phương thức thanh toán</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Phương thức thanh toán</th>
                                        <th>Số giao dịch</th>
                                        <th>Tổng tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $paymentModel = new Payment();
                                    $paymentModel->query('SELECT payment_method, COUNT(*) as count, SUM(amount) as total 
                                                        FROM payments 
                                                        WHERE status = "completed" 
                                                        GROUP BY payment_method');
                                    $paymentStats = $paymentModel->resultSet();
                                    
                                    foreach($paymentStats as $stat):
                                        $methodName = '';
                                        switch($stat->payment_method) {
                                            case 'credit_card':
                                                $methodName = 'Thẻ tín dụng';
                                                break;
                                            case 'bank_transfer':
                                                $methodName = 'Chuyển khoản';
                                                break;
                                            case 'momo':
                                                $methodName = 'MoMo';
                                                break;
                                            default:
                                                $methodName = $stat->payment_method;
                                        }
                                    ?>
                                        <tr>
                                            <td><?php echo $methodName; ?></td>
                                            <td><?php echo $stat->count; ?></td>
                                            <td><?php echo number_format($stat->total); ?>₫</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Thêm script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Dữ liệu cho biểu đồ danh mục
        const categoryData = {
            labels: [<?php echo "'" . implode("', '", array_keys($data['categoryData'])) . "'"; ?>],
            datasets: [{
                label: 'Số lượng công thức',
                data: [<?php echo implode(", ", array_values($data['categoryData'])); ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(199, 199, 199, 0.7)',
                    'rgba(83, 102, 255, 0.7)',
                    'rgba(40, 159, 64, 0.7)',
                    'rgba(210, 199, 199, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(199, 199, 199, 1)',
                    'rgba(83, 102, 255, 1)',
                    'rgba(40, 159, 64, 1)',
                    'rgba(210, 199, 199, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Dữ liệu cho biểu đồ doanh thu
        const revenueData = {
            labels: [<?php echo "'" . implode("', '", array_keys($data['paymentsByMonth'])) . "'"; ?>],
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: [<?php echo implode(", ", array_values($data['paymentsByMonth'])); ?>],
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                tension: 0.4
            }]
        };

        // Khởi tạo biểu đồ danh mục (dạng tròn)
        const categoryChartElement = document.getElementById('categoryChart');
        if (categoryChartElement) {
            const categoryChart = new Chart(categoryChartElement, {
                type: 'pie',
                data: categoryData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        title: {
                            display: true,
                            text: 'Công thức theo danh mục'
                        }
                    }
                }
            });
        } else {
            console.error('Không tìm thấy phần tử #categoryChart');
        }

        // Khởi tạo biểu đồ doanh thu (dạng cột)
        const revenueChartElement = document.getElementById('revenueChart');
        if (revenueChartElement) {
            const revenueChart = new Chart(revenueChartElement, {
                type: 'bar',
                data: revenueData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Doanh thu theo tháng (VNĐ)'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                // Định dạng số tiền VND
                                callback: function(value) {
                                    return new Intl.NumberFormat('vi-VN').format(value);
                                }
                            }
                        }
                    }
                }
            });
        } else {
            console.error('Không tìm thấy phần tử #revenueChart');
        }

        // Tạo biểu đồ số người dùng đăng ký mới
        fetch('<?php echo URL_ROOT; ?>/admin/users_registration_data')
            .then(response => response.json())
            .then(data => {
                const userRegistrationChartElement = document.getElementById('userRegistrationChart');
                if (userRegistrationChartElement) {
                    const userRegistrationChart = new Chart(userRegistrationChartElement, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Số người đăng ký mới',
                                data: data.data,
                                backgroundColor: 'rgba(54, 162, 235, 0.3)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Người dùng đăng ký theo thời gian'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                } else {
                    console.error('Không tìm thấy phần tử #userRegistrationChart');
                }
            })
            .catch(error => console.error('Lỗi khi lấy dữ liệu người dùng:', error));
    } catch (error) {
        console.error('Lỗi khi tạo biểu đồ:', error);
    }
});
</script>

<?php require_once APP_ROOT . '/views/includes/footer.php'; ?>
