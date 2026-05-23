import React from 'react';

const CourseInfo = ({ course, lessons }) => {
    return (
        <div className="card mb-4 shadow-sm">
            <img
                src={course.image ? `/uploads/courses/${course.image}` : '/img/congthucnauan.jpg'}
                className="card-img-top"
                alt={course.title}
                style={{ maxHeight: '400px', objectFit: 'cover' }}
            />
            <div className="card-body">
                <h1 className="card-title h2">{course.title}</h1>

                {/* Course Stats */}
                <div className="d-flex flex-wrap align-items-center mb-3">
                    <div className="me-4 mb-2">
                        <i className="fas fa-users me-1 text-muted"></i>
                        <span>{course.enrollments_count || 0} học viên</span>
                    </div>
                    <div className="me-4 mb-2">
                        <i className="fas fa-film me-1 text-muted"></i>
                        <span>{lessons.length} bài học</span>
                    </div>
                    <div className="me-4 mb-2">
                        <i className="fas fa-clock me-1 text-muted"></i>
                        <span>{course.total_duration || 0} phút</span>
                    </div>
                    <div className="mb-2">
                        <i className="fas fa-user me-1 text-muted"></i>
                        <span>{course.instructor_name || 'Admin'}</span>
                    </div>
                </div>

                {/* Description */}
                <div className="alert alert-light border">
                    <h5 className="alert-heading">Mô tả khóa học</h5>
                    <p style={{ whiteSpace: 'pre-line' }}>{course.description}</p>
                </div>

                {/* Requirements */}
                {course.requirements && (
                    <div className="mt-4">
                        <h5>Yêu cầu</h5>
                        <p style={{ whiteSpace: 'pre-line' }}>{course.requirements}</p>
                    </div>
                )}

                {/* What you will learn */}
                {course.what_you_will_learn && (
                    <div className="mt-4">
                        <h5>Bạn sẽ học được gì</h5>
                        <div className="row">
                            {course.what_you_will_learn.split('\n').map((item, index) => (
                                item.trim() && (
                                    <div key={index} className="col-md-6 mb-2">
                                        <div className="d-flex">
                                            <i className="fas fa-check text-success me-2 mt-1"></i>
                                            <span>{item.trim()}</span>
                                        </div>
                                    </div>
                                )
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default CourseInfo;
