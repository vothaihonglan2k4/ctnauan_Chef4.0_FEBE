import React from 'react';
import { Link } from 'react-router-dom';

const LessonSidebar = ({
    course,
    lessons,
    currentLesson,
    courseId,
    isEnrolled,
    completedLessons,
    enrollment,
    sidebarOpen,
    onToggleSidebar
}) => {
    return (
        <div className={`col-lg-3 ${sidebarOpen ? 'd-block' : 'd-none d-lg-block'}`}>
            <div className="card shadow-sm position-sticky" style={{ top: '20px', height: 'calc(100vh - 40px)', overflowY: 'auto' }}>
                {/* Header */}
                <div className="card-header bg-white d-flex justify-content-between align-items-center sticky-top">
                    <h5 className="mb-0 text-truncate" title={course.title}>
                        {course.title}
                    </h5>
                    <button
                        className="btn btn-sm btn-outline-primary d-lg-none"
                        onClick={() => onToggleSidebar(false)}
                    >
                        <i className="fas fa-times"></i>
                    </button>
                </div>

                {/* Lessons List */}
                <div className="list-group list-group-flush">
                    {lessons.map((lesson, index) => {
                        const isLocked = !isEnrolled && !lesson.is_free;
                        const isActive = lesson.id === currentLesson?.id;
                        const isLessonCompleted = completedLessons.includes(lesson.id);

                        return (
                            <Link
                                key={lesson.id}
                                to={isLocked ? '#' : `/courses/learn/${courseId}/${lesson.id}`}
                                className={`list-group-item list-group-item-action ${isActive ? 'active' : ''} ${isLocked ? 'disabled' : ''}`}
                                onClick={(e) => {
                                    if (isLocked) e.preventDefault();
                                    if (window.innerWidth < 992) onToggleSidebar(false);
                                }}
                                style={isLocked ? { cursor: 'not-allowed', opacity: 0.6 } : {}}
                            >
                                <div className="d-flex w-100 justify-content-between align-items-center">
                                    <h6 className="mb-1 text-truncate">
                                        <span className="me-2">{index + 1}.</span>
                                        {lesson.title}
                                        {isLocked ? (
                                            <i className="fas fa-lock text-warning ms-2"></i>
                                        ) : lesson.is_free === 1 ? (
                                            <span className="badge bg-success ms-1">Miễn phí</span>
                                        ) : null}
                                        {isLessonCompleted && (
                                            <i className="fas fa-check-circle text-success ms-2"></i>
                                        )}
                                    </h6>
                                </div>
                                <small className={isActive ? 'text-light' : 'text-muted'}>
                                    {lesson.duration_minutes} phút
                                </small>
                            </Link>
                        );
                    })}
                </div>

                {/* Progress Footer */}
                {enrollment && (
                    <div className="card-footer bg-white sticky-bottom">
                        <div className="progress mb-2" style={{ height: '8px' }}>
                            <div
                                className="progress-bar bg-success"
                                role="progressbar"
                                style={{ width: `${enrollment.progress}%` }}
                                aria-valuenow={enrollment.progress}
                                aria-valuemin="0"
                                aria-valuemax="100"
                            ></div>
                        </div>
                        <small className="text-muted">Tiến độ: {enrollment.progress}%</small>
                    </div>
                )}
            </div>
        </div>
    );
};

export default LessonSidebar;
