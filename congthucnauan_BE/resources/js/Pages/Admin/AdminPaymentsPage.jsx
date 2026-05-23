import { useState, useEffect, useRef } from 'react';
import { useAuth } from '../../contexts/AuthContext';

export default function AdminPaymentsPage() {
    const [payments, setPayments] = useState([]);
    const [stats, setStats] = useState({});
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState({ status: 'all', payment_method: 'all', search: '' });
    const [selectedPayment, setSelectedPayment] = useState(null);
    const [showModal, setShowModal] = useState(false);
    const [showInvoice, setShowInvoice] = useState(false);
    const [successMessage, setSuccessMessage] = useState('');
    const { token } = useAuth();
    const invoiceRef = useRef();

    useEffect(() => { fetchPayments(); }, [filter.status, filter.payment_method]);

    const fetchPayments = async () => {
        try {
            const params = new URLSearchParams();
            if (filter.status !== 'all') params.append('status', filter.status);
            if (filter.payment_method !== 'all') params.append('payment_method', filter.payment_method);
            if (filter.search) params.append('search', filter.search);
            
            const res = await fetch(`/api/v1/admin/payments?${params}`, { 
                headers: { 'Authorization': `Bearer ${token}` } 
            });
            const data = await res.json();
            setPayments(data.payments || []);
            setStats(data.stats || {});
        } catch (e) { console.error(e); }
        finally { setLoading(false); }
    };

    const handleSearch = (e) => {
        e.preventDefault();
        fetchPayments();
    };

    const handleUpdateStatus = async (id, newStatus) => {
        if (!confirm(`Cập nhật trạng thái thành "${getStatusText(newStatus)}"?`)) return;
        
        const res = await fetch(`/api/v1/admin/payments/${id}/status`, {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: newStatus })
        });
        
        if (res.ok) {
            setSuccessMessage('Cập nhật trạng thái thành công!');
            fetchPayments();
            setShowModal(false);
            setTimeout(() => setSuccessMessage(''), 3000);
        }
    };

    const openDetail = (payment) => { setSelectedPayment(payment); setShowModal(true); };
    const openInvoice = (payment) => { setSelectedPayment(payment); setShowInvoice(true); };
    
    const handlePrint = () => {
        const printContent = invoiceRef.current;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Hóa đơn #${String(selectedPayment.id).padStart(6, '0')}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; }
                    .invoice-container { max-width: 800px; margin: 0 auto; }
                    .invoice-header { border-bottom: 3px solid #0d6efd; padding-bottom: 20px; margin-bottom: 30px; }
                    .company-info h1 { color: #0d6efd; font-size: 28px; font-weight: bold; }
                    .invoice-title h2 { font-size: 36px; color: #333; }
                    .badge-status { padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: 600; }
                    .badge-completed { background: #d4edda; color: #155724; }
                    .badge-pending { background: #fff3cd; color: #856404; }
                    .badge-failed { background: #f8d7da; color: #721c24; }
                    .info-label { font-weight: 600; color: #555; display: inline-block; width: 150px; }
                    .invoice-table th { background: #0d6efd; color: white; padding: 12px; }
                    .invoice-table td { padding: 12px; border-bottom: 1px solid #dee2e6; }
                    .grand-total .amount { font-size: 24px; color: #0d6efd; font-weight: bold; }
                    @media print { body { margin: 0; padding: 15px; } }
                </style>
            </head>
            <body>${printContent.innerHTML}</body>
            </html>
        `);
        printWindow.document.close();
        printWindow.onload = () => { printWindow.print(); };
    };


    const getStatusBadge = (status) => {
        const map = { 
            completed: ['success', 'Hoàn thành'], 
            pending: ['warning', 'Đang xử lý'], 
            failed: ['danger', 'Thất bại'],
            refunded: ['info', 'Hoàn tiền']
        };
        const [color, text] = map[status] || ['secondary', status];
        return <span className={`badge bg-${color}`}>{text}</span>;
    };

    const getStatusText = (status) => {
        const map = { completed: 'Hoàn thành', pending: 'Đang xử lý', failed: 'Thất bại', refunded: 'Hoàn tiền' };
        return map[status] || status;
    };

    const getMethodBadge = (method) => {
        const map = { 
            stripe: ['primary', 'Stripe'], 
            vnpay: ['info', 'VNPay'], 
            credit_card: ['info', 'Thẻ tín dụng'],
            bank_transfer: ['primary', 'Chuyển khoản'],
            momo: ['danger', 'MoMo']
        };
        const [color, text] = map[method] || ['secondary', method];
        return <span className={`badge bg-${color}`}>{text}</span>;
    };

    const getMethodText = (method) => {
        const map = { 
            stripe: 'Stripe', 
            vnpay: 'VNPay', 
            credit_card: 'Thẻ tín dụng',
            bank_transfer: 'Chuyển khoản ngân hàng',
            momo: 'Ví MoMo',
            demo: 'Demo (Thanh toán thử)'
        };
        return map[method] || method;
    };

    const formatCurrency = (amount) => new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
    const formatDate = (date) => new Date(date).toLocaleString('vi-VN');

    return (
        <div>
            <div className="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1><i className="fas fa-money-bill-wave text-primary me-2"></i>Quản lý thanh toán</h1>
                    <p className="text-muted">Quản lý các giao dịch thanh toán</p>
                </div>
            </div>

            {successMessage && <div className="alert alert-success"><i className="fas fa-check-circle me-2"></i>{successMessage}</div>}

            {/* Stats Cards */}
            <div className="row mb-4">
                <div className="col-md-3 mb-3">
                    <div className="card border-left-primary h-100">
                        <div className="card-body">
                            <div className="text-xs text-primary text-uppercase mb-1">Tổng giao dịch</div>
                            <div className="h5 mb-0 fw-bold">{stats.total || 0}</div>
                        </div>
                    </div>
                </div>
                <div className="col-md-3 mb-3">
                    <div className="card border-left-success h-100">
                        <div className="card-body">
                            <div className="text-xs text-success text-uppercase mb-1">Hoàn thành</div>
                            <div className="h5 mb-0 fw-bold">{stats.completed || 0}</div>
                        </div>
                    </div>
                </div>
                <div className="col-md-3 mb-3">
                    <div className="card border-left-warning h-100">
                        <div className="card-body">
                            <div className="text-xs text-warning text-uppercase mb-1">Đang xử lý</div>
                            <div className="h5 mb-0 fw-bold">{stats.pending || 0}</div>
                        </div>
                    </div>
                </div>
                <div className="col-md-3 mb-3">
                    <div className="card border-left-info h-100">
                        <div className="card-body">
                            <div className="text-xs text-info text-uppercase mb-1">Tổng doanh thu</div>
                            <div className="h5 mb-0 fw-bold">{formatCurrency(stats.total_amount || 0)}</div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Filters */}
            <div className="card mb-4">
                <div className="card-body">
                    <form onSubmit={handleSearch} className="row g-3 align-items-end">
                        <div className="col-md-3">
                            <label className="form-label">Trạng thái</label>
                            <select className="form-select" value={filter.status} onChange={e => setFilter({...filter, status: e.target.value})}>
                                <option value="all">Tất cả</option>
                                <option value="completed">Hoàn thành</option>
                                <option value="pending">Đang xử lý</option>
                                <option value="failed">Thất bại</option>
                                <option value="refunded">Hoàn tiền</option>
                            </select>
                        </div>
                        <div className="col-md-3">
                            <label className="form-label">Phương thức</label>
                            <select className="form-select" value={filter.payment_method} onChange={e => setFilter({...filter, payment_method: e.target.value})}>
                                <option value="all">Tất cả</option>
                                <option value="stripe">Stripe</option>
                                <option value="vnpay">VNPay</option>
                                <option value="bank_transfer">Chuyển khoản</option>
                            </select>
                        </div>
                        <div className="col-md-4">
                            <label className="form-label">Tìm kiếm</label>
                            <input type="text" className="form-control" placeholder="Mã giao dịch, tên, email..." value={filter.search} onChange={e => setFilter({...filter, search: e.target.value})} />
                        </div>
                        <div className="col-md-2">
                            <button type="submit" className="btn btn-primary w-100"><i className="fas fa-search me-1"></i>Tìm</button>
                        </div>
                    </form>
                </div>
            </div>


            {/* Payments Table */}
            <div className="card">
                <div className="card-header d-flex justify-content-between">
                    <span><i className="fas fa-list me-2"></i>Danh sách thanh toán</span>
                    <span className="badge bg-info">{payments.length} giao dịch</span>
                </div>
                <div className="card-body">
                    {loading ? (
                        <div className="text-center py-5"><div className="spinner-border text-primary"></div></div>
                    ) : payments.length === 0 ? (
                        <div className="text-center py-5">
                            <i className="fas fa-money-bill-wave fa-4x text-muted mb-3"></i>
                            <h5>Chưa có giao dịch nào</h5>
                        </div>
                    ) : (
                        <div className="table-responsive">
                            <table className="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Người dùng</th>
                                        <th>Khóa học</th>
                                        <th>Số tiền</th>
                                        <th>Phương thức</th>
                                        <th>Trạng thái</th>
                                        <th>Mã giao dịch</th>
                                        <th>Ngày</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {payments.map(p => (
                                        <tr key={p.id}>
                                            <td>{p.id}</td>
                                            <td>
                                                <div className="fw-bold">{p.user?.name || 'N/A'}</div>
                                                <small className="text-muted">{p.user?.email}</small>
                                            </td>
                                            <td>{p.enrollment?.course?.title || 'N/A'}</td>
                                            <td className="fw-bold text-success">{formatCurrency(p.amount)}</td>
                                            <td>{getMethodBadge(p.payment_method)}</td>
                                            <td>{getStatusBadge(p.status)}</td>
                                            <td>
                                                <span className="text-truncate d-inline-block" style={{ maxWidth: 100 }} title={p.transaction_id}>
                                                    {p.transaction_id || 'N/A'}
                                                </span>
                                            </td>
                                            <td>{formatDate(p.created_at)}</td>
                                            <td>
                                                <button className="btn btn-sm btn-primary me-1" onClick={() => openDetail(p)} title="Chi tiết">
                                                    <i className="fas fa-eye"></i>
                                                </button>
                                                <button className="btn btn-sm btn-success" onClick={() => openInvoice(p)} title="In hóa đơn">
                                                    <i className="fas fa-print"></i>
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

            {/* Detail Modal */}
            {showModal && selectedPayment && (
                <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog modal-lg">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5>Chi tiết thanh toán #{selectedPayment.id}</h5>
                                <button className="btn-close" onClick={() => setShowModal(false)}></button>
                            </div>
                            <div className="modal-body">
                                <div className="row">
                                    <div className="col-md-6">
                                        <table className="table table-borderless">
                                            <tbody>
                                                <tr><th>ID:</th><td>{selectedPayment.id}</td></tr>
                                                <tr><th>Người dùng:</th><td>{selectedPayment.user?.name}<br/><small className="text-muted">{selectedPayment.user?.email}</small></td></tr>
                                                <tr><th>Khóa học:</th><td>{selectedPayment.enrollment?.course?.title || 'N/A'}</td></tr>
                                                <tr><th>Số tiền:</th><td className="fw-bold text-success">{formatCurrency(selectedPayment.amount)}</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div className="col-md-6">
                                        <table className="table table-borderless">
                                            <tbody>
                                                <tr><th>Phương thức:</th><td>{getMethodBadge(selectedPayment.payment_method)}</td></tr>
                                                <tr><th>Trạng thái:</th><td>{getStatusBadge(selectedPayment.status)}</td></tr>
                                                <tr><th>Mã giao dịch:</th><td style={{ wordBreak: 'break-all' }}>{selectedPayment.transaction_id || 'N/A'}</td></tr>
                                                <tr><th>Ngày tạo:</th><td>{formatDate(selectedPayment.created_at)}</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                {selectedPayment.status !== 'completed' && (
                                    <div className="border-top pt-3 mt-3">
                                        <h6>Cập nhật trạng thái:</h6>
                                        <div className="btn-group">
                                            <button className="btn btn-success btn-sm" onClick={() => handleUpdateStatus(selectedPayment.id, 'completed')}>
                                                <i className="fas fa-check me-1"></i>Hoàn thành
                                            </button>
                                            <button className="btn btn-danger btn-sm" onClick={() => handleUpdateStatus(selectedPayment.id, 'failed')}>
                                                <i className="fas fa-times me-1"></i>Thất bại
                                            </button>
                                            <button className="btn btn-info btn-sm" onClick={() => handleUpdateStatus(selectedPayment.id, 'refunded')}>
                                                <i className="fas fa-undo me-1"></i>Hoàn tiền
                                            </button>
                                        </div>
                                    </div>
                                )}
                            </div>
                            <div className="modal-footer">
                                <button className="btn btn-secondary" onClick={() => setShowModal(false)}>Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Invoice Modal */}
            {showInvoice && selectedPayment && (
                <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog modal-lg">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5>Hóa đơn #{String(selectedPayment.id).padStart(6, '0')}</h5>
                                <button className="btn-close" onClick={() => setShowInvoice(false)}></button>
                            </div>
                            <div className="modal-body" ref={invoiceRef}>
                                <div className="invoice-container">
                                    {/* Header */}
                                    <div className="invoice-header row mb-4">
                                        <div className="col-md-6">
                                            <h2 className="text-primary"><i className="fas fa-utensils me-2"></i>Công Thức Nấu Ăn</h2>
                                            <p className="mb-1"><i className="fas fa-map-marker-alt me-2"></i>123 Đường ABC, Quận XYZ, TP. Đà Nẵng</p>
                                            <p className="mb-1"><i className="fas fa-phone me-2"></i>(0236) 123-4567</p>
                                            <p className="mb-0"><i className="fas fa-envelope me-2"></i>info@congthucnauan.com</p>
                                        </div>
                                        <div className="col-md-6 text-end">
                                            <h1 className="display-6">HÓA ĐƠN</h1>
                                            <p className="mb-1"><strong>Số:</strong> #{String(selectedPayment.id).padStart(6, '0')}</p>
                                            <p className="mb-1"><strong>Ngày:</strong> {new Date(selectedPayment.created_at).toLocaleDateString('vi-VN')}</p>
                                            <p className="mb-0">
                                                <strong>Trạng thái:</strong> {getStatusBadge(selectedPayment.status)}
                                            </p>
                                        </div>
                                    </div>

                                    <hr className="border-primary border-2" />

                                    {/* Customer & Payment Info */}
                                    <div className="row mb-4">
                                        <div className="col-md-6">
                                            <h6 className="text-primary text-uppercase"><i className="fas fa-user me-2"></i>Thông tin khách hàng</h6>
                                            <p className="mb-1"><strong>Họ tên:</strong> {selectedPayment.user?.name}</p>
                                            <p className="mb-0"><strong>Email:</strong> {selectedPayment.user?.email}</p>
                                        </div>
                                        <div className="col-md-6">
                                            <h6 className="text-primary text-uppercase"><i className="fas fa-credit-card me-2"></i>Thông tin thanh toán</h6>
                                            <p className="mb-1"><strong>Phương thức:</strong> {getMethodText(selectedPayment.payment_method)}</p>
                                            <p className="mb-1"><strong>Mã giao dịch:</strong> {selectedPayment.transaction_id || 'N/A'}</p>
                                            <p className="mb-0"><strong>Ngày:</strong> {formatDate(selectedPayment.created_at)}</p>
                                        </div>
                                    </div>

                                    {/* Invoice Table */}
                                    <table className="table invoice-table">
                                        <thead>
                                            <tr className="table-primary">
                                                <th>#</th>
                                                <th>Khóa học</th>
                                                <th className="text-end">Giá</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>{selectedPayment.enrollment?.course?.title || 'Khóa học'}</td>
                                                <td className="text-end">{formatCurrency(selectedPayment.amount)}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colSpan="2" className="text-end fw-bold">Tổng cộng:</td>
                                                <td className="text-end fw-bold text-primary fs-4">{formatCurrency(selectedPayment.amount)}</td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    {/* Footer */}
                                    <div className="text-center mt-4 pt-3 border-top">
                                        <p className="fw-bold mb-1">Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi!</p>
                                        <p className="text-muted small mb-0">Hóa đơn này được tạo tự động và có giá trị mà không cần chữ ký.</p>
                                    </div>
                                </div>
                            </div>
                            <div className="modal-footer">
                                <button className="btn btn-secondary" onClick={() => setShowInvoice(false)}>Đóng</button>
                                <button className="btn btn-primary" onClick={handlePrint}>
                                    <i className="fas fa-print me-2"></i>In hóa đơn
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            <style>{`
                .border-left-primary { border-left: 4px solid #0d6efd !important; }
                .border-left-success { border-left: 4px solid #198754 !important; }
                .border-left-warning { border-left: 4px solid #ffc107 !important; }
                .border-left-info { border-left: 4px solid #0dcaf0 !important; }
            `}</style>
        </div>
    );
}
