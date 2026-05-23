import React from 'react';

const WorkingHours = () => {
    const schedule = [
        { day: 'Thứ Hai - Thứ Sáu:', hours: '8:00 - 17:30', isOpen: true },
        { day: 'Thứ Bảy:', hours: '8:00 - 12:00', isOpen: true },
        { day: 'Chủ Nhật:', hours: 'Đóng cửa', isOpen: false }
    ];

    return (
        <div className="card border-0 shadow-sm rounded-3 mb-4">
            <div className="card-body p-4">
                <h4 className="text-primary fw-bold mb-4">Giờ làm việc</h4>
                <ul className="list-unstyled mb-0">
                    {schedule.map((item, index) => (
                        <li key={index} className={`d-flex justify-content-between py-2 ${index < schedule.length - 1 ? 'border-bottom' : ''}`}>
                            <span className="text-muted">{item.day}</span>
                            <span className={item.isOpen ? 'fw-medium' : 'fw-medium text-danger'}>
                                {item.hours}
                            </span>
                        </li>
                    ))}
                </ul>
            </div>
        </div>
    );
};

export default WorkingHours;
