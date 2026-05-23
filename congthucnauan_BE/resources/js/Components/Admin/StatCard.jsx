import { Link } from 'react-router-dom';

export default function StatCard({ 
    title, 
    value, 
    icon, 
    iconColor = 'primary', 
    borderColor = 'primary',
    footerLink,
    footerText = 'Xem chi tiết',
    subText 
}) {
    return (
        <div className={`card border-left-${borderColor} h-100`}>
            <div className="card-body">
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        <div className="text-xs text-uppercase mb-1 text-muted">
                            {title}
                        </div>
                        <div className="h4 mb-0 fw-bold">
                            {value}
                        </div>
                        {subText && (
                            <div className="small text-success mt-1">
                                {subText}
                            </div>
                        )}
                    </div>
                    <div className={`p-3 rounded-circle bg-${iconColor} bg-opacity-10`}>
                        <i className={`${icon} fa-2x text-${iconColor}`}></i>
                    </div>
                </div>
            </div>
            {footerLink && (
                <div className="card-footer bg-light py-2">
                    <Link to={footerLink} className="text-decoration-none small">
                        <i className="fas fa-arrow-right me-1"></i> {footerText}
                    </Link>
                </div>
            )}
            
            <style>{`
                .border-left-primary {
                    border-left: 4px solid #0d6efd;
                }
                .border-left-success {
                    border-left: 4px solid #28a745;
                }
                .border-left-info {
                    border-left: 4px solid #17a2b8;
                }
                .border-left-warning {
                    border-left: 4px solid #ffc107;
                }
                .card {
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                }
                .card:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
                }
                .text-xs {
                    font-size: 0.75rem;
                    font-weight: 600;
                    letter-spacing: 0.5px;
                }
            `}</style>
        </div>
    );
}
