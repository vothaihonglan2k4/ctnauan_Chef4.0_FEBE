export default function RecentPaymentsTable({ payments, loading }) {
    const getStatusBadge = (status) => {
        if (status === 'completed') {
            return <span className="badge bg-success">Hoàn thành</span>;
        } else if (status === 'pending') {
            return <span className="badge bg-warning">Đang xử lý</span>;
        }
        return <span className="badge bg-danger">Thất bại</span>;
    };

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    };

    if (loading) {
        return (
            <div className="text-center py-5">
                <div className="spinner-border text-primary" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
            </div>
        );
    }

    if (!payments || payments.length === 0) {
        return (
            <div className="alert alert-info">
                <i className="fas fa-info-circle me-2"></i>
                Chưa có thanh toán nào.
            </div>
        );
    }

    return (
        <div className="table-responsive">
            <table className="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Người dùng</th>
                        <th>Khóa học</th>
                        <th>Số tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    {payments.map(payment => (
                        <tr key={payment.id}>
                            <td><code>#{payment.id}</code></td>
                            <td>{payment.user_name}</td>
                            <td>{payment.course_title}</td>
                            <td className="fw-bold text-success">
                                {formatCurrency(payment.amount)}
                            </td>
                            <td>{getStatusBadge(payment.status)}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
