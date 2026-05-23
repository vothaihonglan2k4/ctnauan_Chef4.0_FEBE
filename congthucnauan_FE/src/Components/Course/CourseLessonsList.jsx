import React from 'react';
import { Link } from 'react-router-dom';

const CourseLessonsList = ({ lessons, courseId, isEnrolled }) => {
    if (lessons.length === 0) {
        return (
            <div className="card shadow-sm mb-4">
                <div className="card-body text-center py-4">
                    <p className="text-muted mb-0">Chưa có bài học nào được thêm vào khóa học này.</p>
                </div>
            </div>
        );
    }

    return (
        <div className="card shadow-sm mb-4">
            <div className="card-header bg-white">
                <h4 className="card-title h5 mb-0">Nội dung khóa học</h4>
            </div>
            <div className="card-body p-0">
                <div className="list-group list-group-flush">
                    {lessons.map((lesson, index) => (
                        <div
                            key={lesson.id}
                            className="list-group-item d-flex justify-content-between align-items-center"
                        >
                            <div>
                                <span className="badge bg-secondary me-2">{index + 1}</span>
                                {lesson.title}
                                {lesson.is_free === 1 && (
                                    <span className="badge bg-success ms-2">Miễn phí</span>
                                )}
                            </div>
                            <div className="d-flex align-items-center">
                                <small className="text-muted me-3">{lesson.duration_minutes} phút</small>
                                {isEnrolled || lesson.is_free === 1 ? (
                                    <Link
                                        to={`/courses/learn/${courseId}/${lesson.id}`}
                                        className="btn btn-sm btn-outline-primary"
                                    >
                                        <i className="fas fa-play-circle"></i>
                                    </Link>
                                ) : (
                                    <i className="fas fa-lock text-muted"></i>
                                )}
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default CourseLessonsList;
