import React from 'react';

const ScheduleNotice = () => {
    return (
        <div className="alert alert-info">
            <h5 className="alert-heading">
                <i className="fas fa-info-circle me-2"></i> Lưu ý về lịch học
            </h5>
            <p>
                Lịch học có thể thay đổi tùy theo tình hình thực tế.
                Vui lòng kiểm tra thường xuyên để có thông tin cập nhật nhất.
            </p>
            <hr />
            <p className="mb-0">
                Nếu bạn không thể tham gia buổi học, vui lòng thông báo trước ít nhất 24 giờ
                theo hotline: <strong>0123.456.789</strong>
            </p>
        </div>
    );
};

export default ScheduleNotice;
