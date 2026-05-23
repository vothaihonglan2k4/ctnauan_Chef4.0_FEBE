import React from 'react';
import { Link } from 'react-router-dom';

const ScheduleEmptyState = () => {
    return (
        <div className="card shadow-sm">
            <div className="card-body text-center py-5">
                <i className="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <h3>Bạn chưa có lịch học offline nào</h3>
                <p className="lead">Các khóa học của bạn hiện chưa có lịch học offline kèm theo</p>
                <Link to="/courses" className="btn btn-primary btn-lg mt-3">
                    <i className="fas fa-search me-2"></i> Khám phá thêm khóa học
                </Link>
            </div>
        </div>
    );
};

export default ScheduleEmptyState;
