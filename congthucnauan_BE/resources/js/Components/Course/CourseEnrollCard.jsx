import React from 'react';
import { Link } from 'react-router-dom';
import { formatCurrency, formatDate } from '../../utils/helpers';

const CourseEnrollCard = ({
    course,
    isEnrolled,
    enrollment,
    onEnroll
}) => {
    return (
        <div className="card shadow-sm position-sticky" style={{ top: '20px' }}>
            <div className="card-body">
                <h5 className="card-title">Thông tin khóa học</h5>

                {isEnrolled ? (
                    <>
                        {/* Already Enrolled */}
                        <div className="alert alert-success">
                            <i className="fas fa-check-circle me-2"></i> Bạn đã đăng ký khóa học này
                        </div>

                        {/* Progress */}
                        {enrollment && (
                            <div className="mb-3">
                                <label className="form-label">Tiến độ học tập</label>
                                <div className="progress">
                                    <div
                                        className="progress-bar"
                                        role="progressbar"
                                        style={{ width: `${enrollment.progress}%` }}
                                        aria-valuenow={enrollment.progress}
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                    >
                                        {enrollment.progress}%
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Continue Learning Button */}
                        <Link
                            to={`/courses/learn/${course.id}`}
                            className="btn btn-primary btn-lg w-100 mb-3"
                        >
                            <i className="fas fa-play-circle me-2"></i> Tiếp tục học
                        </Link>
                    </>
                ) : (
                    <>
                        {/* Price */}
                        <div className="mb-3">
                            <h3 className="card-text text-primary">
                                {course.price > 0 ? (
                                    formatCurrency(course.price)
                                ) : (
                                    <span className="text-success">Miễn phí</span>
                                )}
                            </h3>
                        </div>

                        {/* Enroll Button */}
                        <button
                            onClick={onEnroll}
                            className="btn btn-primary btn-lg w-100 mb-3"
                        >
                            {course.price > 0 ? (
                                <>
                                    <i className="fas fa-shopping-cart me-2"></i> Đăng ký ngay
                                </>
                            ) : (
                                <>
                                    <i className="fas fa-hand-point-right me-2"></i> Đăng ký miễn phí
                                </>
                            )}
                        </button>
                    </>
                )}

                {/* Course Metadata */}
                <div className="card-text">
                    <ul className="list-group list-group-flush">
                        <li className="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i className="fas fa-calendar-alt me-2"></i> Cập nhật</span>
                            <span>{formatDate(course.updated_at || course.created_at)}</span>
                        </li>
                        <li className="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i className="fas fa-globe me-2"></i> Ngôn ngữ</span>
                            <span>Tiếng Việt</span>
                        </li>
                        <li className="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i className="fas fa-info-circle me-2"></i> Trạng thái</span>
                            <span>
                                {course.status === 'published' ? (
                                    <span className="badge bg-success">Đang mở</span>
                                ) : (
                                    <span className="badge bg-warning text-dark">Sắp ra mắt</span>
                                )}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    );
};

export default CourseEnrollCard;
