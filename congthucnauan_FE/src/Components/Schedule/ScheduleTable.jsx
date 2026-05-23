import React from 'react';
import { Link } from 'react-router-dom';

const ScheduleTable = ({ schedules, showTime = true }) => {
    if (!schedules || schedules.length === 0) {
        return (
            <div className="text-center py-4">
                <p className="text-muted mb-0">Không có lịch học nào</p>
            </div>
        );
    }

    return (
        <div className="table-responsive">
            <table className="table table-hover">
                <thead>
                    <tr>
                        <th>Khóa học</th>
                        <th>Địa điểm</th>
                        <th>{showTime ? 'Thời gian' : 'Lịch học'}</th>
                        <th>Giảng viên</th>
                        <th>Lịch học kế tiếp</th>
                    </tr>
                </thead>
                <tbody>
                    {schedules.map((schedule, index) => (
                        <tr key={index}>
                            <td>
                                <Link to={`/courses/${schedule.course_id}`}>
                                    {schedule.course_title}
                                </Link>
                            </td>
                            <td>{schedule.location}</td>
                            <td>{schedule.schedule_time}</td>
                            <td>{schedule.instructor}</td>
                            <td>
                                <span className="badge bg-primary">
                                    {schedule.next_class}
                                </span>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default ScheduleTable;
