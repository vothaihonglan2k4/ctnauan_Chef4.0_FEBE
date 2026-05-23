import { useState, useEffect, useRef } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import Chart from 'chart.js/auto';

export default function AdminReportsPage() {
    const [stats, setStats] = useState({});
    const [categoryData, setCategoryData] = useState([]);
    const [revenueData, setRevenueData] = useState([]);
    const [userData, setUserData] = useState([]);
    const [paymentMethodData, setPaymentMethodData] = useState([]);
    const [popularRecipes, setPopularRecipes] = useState([]);
    const [loading, setLoading] = useState(true);
    const [activeTab, setActiveTab] = useState('users');
    const { token } = useAuth();

    const categoryChartRef = useRef(null);
    const revenueChartRef = useRef(null);
    const userChartRef = useRef(null);
    const categoryChartInstance = useRef(null);
    const revenueChartInstance = useRef(null);
    const userChartInstance = useRef(null);

    useEffect(() => { fetchAllData(); }, []);

    useEffect(() => {
        if (!loading && categoryData.length > 0) {
            createCategoryChart();
        }
        if (!loading && revenueData.length > 0) {
            createRevenueChart();
        }
        if (!loading && userData.length > 0) {
            createUserChart();
        }
        return () => {
            if (categoryChartInstance.current) categoryChartInstance.current.destroy();
            if (revenueChartInstance.current) revenueChartInstance.current.destroy();
            if (userChartInstance.current) userChartInstance.current.destroy();
        };
    }, [loading, categoryData, revenueData, userData]);

    const fetchAllData = async () => {
        try {
            const headers = { 'Authorization': `Bearer ${token}` };
            const [overviewRes, categoryRes, revenueRes, userRes, methodRes, recipesRes] = await Promise.all([
                fetch('/api/v1/admin/reports/overview', { headers }),
                fetch('/api/v1/admin/reports/recipes-by-category', { headers }),
                fetch('/api/v1/admin/reports/revenue-by-month', { headers }),
                fetch('/api/v1/admin/reports/user-registrations', { headers }),
                fetch('/api/v1/admin/reports/payments-by-method', { headers }),
                fetch('/api/v1/admin/reports/popular-recipes', { headers }),
            ]);
            
            const [overview, category, revenue, user, method, recipes] = await Promise.all([
                overviewRes.json(), categoryRes.json(), revenueRes.json(), 
                userRes.json(), methodRes.json(), recipesRes.json()
            ]);
            
            setStats(overview.stats || {});
            setCategoryData(category.data || []);
            setRevenueData(revenue.data || []);
            setUserData(user.data || []);
            setPaymentMethodData(method.data || []);
            setPopularRecipes(recipes.recipes || []);
        } catch (e) { console.error(e); }
        finally { setLoading(false); }
    };

    const createCategoryChart = () => {
        if (categoryChartInstance.current) categoryChartInstance.current.destroy();
        const ctx = categoryChartRef.current?.getContext('2d');
        if (!ctx) return;
        
        const colors = [
            'rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)', 'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)', 'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)',
            'rgba(199, 199, 199, 0.7)', 'rgba(83, 102, 255, 0.7)', 'rgba(40, 159, 64, 0.7)',
            'rgba(210, 199, 199, 0.7)'
        ];
        
        categoryChartInstance.current = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: categoryData.map(c => c.name),
                datasets: [{
                    label: 'Số lượng công thức',
                    data: categoryData.map(c => c.count),
                    backgroundColor: colors.slice(0, categoryData.length),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' },
                    title: { display: true, text: 'Công thức theo danh mục' }
                }
            }
        });
    };


    const createRevenueChart = () => {
        if (revenueChartInstance.current) revenueChartInstance.current.destroy();
        const ctx = revenueChartRef.current?.getContext('2d');
        if (!ctx) return;
        
        revenueChartInstance.current = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: revenueData.map(r => r.month),
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: revenueData.map(r => r.total),
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: { display: true, text: 'Doanh thu theo tháng (VNĐ)' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => new Intl.NumberFormat('vi-VN').format(value)
                        }
                    }
                }
            }
        });
    };

    const createUserChart = () => {
        if (userChartInstance.current) userChartInstance.current.destroy();
        const ctx = userChartRef.current?.getContext('2d');
        if (!ctx) return;
        
        userChartInstance.current = new Chart(ctx, {
            type: 'line',
            data: {
                labels: userData.map(u => u.month),
                datasets: [{
                    label: 'Số người đăng ký mới',
                    data: userData.map(u => u.count),
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
                    title: { display: true, text: 'Người dùng đăng ký theo thời gian' }
                },
                scales: { y: { beginAtZero: true } }
            }
        });
    };

    const formatCurrency = (amount) => new Intl.NumberFormat('vi-VN').format(amount) + '₫';
    const getMethodName = (method) => {
        const map = { stripe: 'Stripe', vnpay: 'VNPay', credit_card: 'Thẻ tín dụng', bank_transfer: 'Chuyển khoản', momo: 'MoMo' };
        return map[method] || method;
    };

    if (loading) {
        return <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>;
    }

    return (
        <div className="container mt-4">
            {/* Header */}
            <div className="row mb-3">
                <div className="col">
                    <h1>Báo cáo thống kê</h1>
                </div>
                <div className="col-auto">
                    <Link to="/admin" className="btn btn-secondary">
                        <i className="fas fa-arrow-left"></i> Quay lại
                    </Link>
                </div>
            </div>

            {/* Stats Cards */}
            <div className="row mb-4">
                <div className="col-md-3">
                    <div className="card text-center">
                        <div className="card-body">
                            <h1 className="display-4">{stats.users || 0}</h1>
                            <p className="lead">Người dùng</p>
                        </div>
                    </div>
                </div>
                <div className="col-md-3">
                    <div className="card text-center">
                        <div className="card-body">
                            <h1 className="display-4">{stats.recipes || 0}</h1>
                            <p className="lead">Công thức</p>
                        </div>
                    </div>
                </div>
                <div className="col-md-3">
                    <div className="card text-center">
                        <div className="card-body">
                            <h1 className="display-4">{stats.categories || 0}</h1>
                            <p className="lead">Danh mục</p>
                        </div>
                    </div>
                </div>
                <div className="col-md-3">
                    <div className="card text-center">
                        <div className="card-body">
                            <h1 className="display-4">{stats.payments || 0}</h1>
                            <p className="lead">Thanh toán</p>
                        </div>
                    </div>
                </div>
            </div>


            {/* Charts Row */}
            <div className="row">
                <div className="col-md-6">
                    <div className="card mb-4">
                        <div className="card-header">
                            <h5 className="mb-0">Công thức theo danh mục</h5>
                        </div>
                        <div className="card-body">
                            <div className="chart-container" style={{ position: 'relative', height: '300px' }}>
                                <canvas ref={categoryChartRef}></canvas>
                            </div>
                            <table className="table table-sm mt-2">
                                <thead>
                                    <tr>
                                        <th>Danh mục</th>
                                        <th>Số lượng công thức</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {categoryData.map((c, idx) => (
                                        <tr key={idx}>
                                            <td>{c.name}</td>
                                            <td>{c.count}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div className="col-md-6">
                    <div className="card mb-4">
                        <div className="card-header">
                            <h5 className="mb-0">Doanh thu theo tháng</h5>
                        </div>
                        <div className="card-body">
                            <div className="chart-container" style={{ position: 'relative', height: '300px' }}>
                                <canvas ref={revenueChartRef}></canvas>
                            </div>
                            <table className="table table-sm mt-2">
                                <thead>
                                    <tr>
                                        <th>Tháng</th>
                                        <th>Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {revenueData.map((r, idx) => (
                                        <tr key={idx}>
                                            <td>{r.month}</td>
                                            <td>{formatCurrency(r.total)}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {/* User Registration Chart */}
            <div className="row">
                <div className="col-md-12">
                    <div className="card mb-4">
                        <div className="card-header">
                            <h5 className="mb-0">Thống kê người dùng mới</h5>
                        </div>
                        <div className="card-body">
                            <div className="chart-container" style={{ position: 'relative', height: '300px' }}>
                                <canvas ref={userChartRef}></canvas>
                            </div>
                            <div className="mt-3 text-center">
                                <p className="mb-1">Tổng số người dùng: <strong>{stats.users || 0}</strong></p>
                                <p className="mb-1">Người dùng mới trong tháng này: <strong>{stats.new_users_30_days || 0}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {/* Detailed Reports */}
            <div className="row">
                <div className="col-md-12">
                    <div className="card">
                        <div className="card-header">
                            <h5 className="mb-0">Báo cáo chi tiết</h5>
                        </div>
                        <div className="card-body">
                            <ul className="nav nav-tabs" role="tablist">
                                <li className="nav-item" role="presentation">
                                    <button className={`nav-link ${activeTab === 'users' ? 'active' : ''}`} onClick={() => setActiveTab('users')}>Người dùng</button>
                                </li>
                                <li className="nav-item" role="presentation">
                                    <button className={`nav-link ${activeTab === 'recipes' ? 'active' : ''}`} onClick={() => setActiveTab('recipes')}>Công thức</button>
                                </li>
                                <li className="nav-item" role="presentation">
                                    <button className={`nav-link ${activeTab === 'payments' ? 'active' : ''}`} onClick={() => setActiveTab('payments')}>Thanh toán</button>
                                </li>
                            </ul>
                            <div className="tab-content p-3">
                                {/* Users Tab */}
                                {activeTab === 'users' && (
                                    <div className="tab-pane fade show active">
                                        <h4>Thống kê người dùng</h4>
                                        <p>Tổng số người dùng đăng ký: {stats.users || 0}</p>
                                        <p>Số người mới đăng ký trong 30 ngày qua: {stats.new_users_30_days || 0}</p>
                                    </div>
                                )}

                                {/* Recipes Tab */}
                                {activeTab === 'recipes' && (
                                    <div className="tab-pane fade show active">
                                        <h4>Thống kê công thức</h4>
                                        <p>Tổng số công thức: {stats.recipes || 0}</p>
                                        <p>Số công thức mới trong 30 ngày qua: {stats.new_recipes_30_days || 0}</p>
                                        
                                        <h5 className="mt-4">Công thức phổ biến nhất</h5>
                                        <table className="table table-striped">
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
                                                {popularRecipes.length === 0 ? (
                                                    <tr><td colSpan="5" className="text-center">Chưa có dữ liệu</td></tr>
                                                ) : popularRecipes.slice(0, 5).map(recipe => (
                                                    <tr key={recipe.id}>
                                                        <td>
                                                            <Link to={`/recipes/${recipe.id}`} target="_blank">
                                                                {recipe.title}
                                                            </Link>
                                                        </td>
                                                        <td>{recipe.category?.name || 'N/A'}</td>
                                                        <td>{recipe.user?.name || 'N/A'}</td>
                                                        <td>{recipe.ratings_count || 0}</td>
                                                        <td>{recipe.ratings_avg_rating ? parseFloat(recipe.ratings_avg_rating).toFixed(1) : 'Chưa có đánh giá'}</td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                )}

                                {/* Payments Tab */}
                                {activeTab === 'payments' && (
                                    <div className="tab-pane fade show active">
                                        <h4>Thống kê thanh toán</h4>
                                        <p>Tổng số giao dịch: {stats.payments || 0}</p>
                                        <p>Tổng doanh thu: {formatCurrency(stats.total_revenue || 0)}</p>
                                        
                                        <h5 className="mt-4">Thống kê theo phương thức thanh toán</h5>
                                        <table className="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Phương thức thanh toán</th>
                                                    <th>Số giao dịch</th>
                                                    <th>Tổng tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {paymentMethodData.length === 0 ? (
                                                    <tr><td colSpan="3" className="text-center">Chưa có dữ liệu</td></tr>
                                                ) : paymentMethodData.map((stat, idx) => (
                                                    <tr key={idx}>
                                                        <td>{getMethodName(stat.payment_method)}</td>
                                                        <td>{stat.count}</td>
                                                        <td>{formatCurrency(stat.total)}</td>
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
            </div>
        </div>
    );
}
