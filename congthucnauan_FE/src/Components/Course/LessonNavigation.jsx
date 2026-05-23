import React from 'react';
import { Link } from 'react-router-dom';

const LessonNavigation = ({
    courseId,
    prevLesson,
    nextLesson,
    showSidebarButton = false,
    onToggleSidebar
}) => {
    return (
        <div className="d-flex justify-content-between align-items-center mb-3">
            {/* Left side - Back and Sidebar buttons */}
            <div>
                {showSidebarButton && (
                    <button
                        className="btn btn-outline-primary me-2 d-lg-none"
                        onClick={() => onToggleSidebar(true)}
                    >
                        <i className="fas fa-list"></i>
                    </button>
                )}
                <Link to={`/courses/${courseId}`} className="btn btn-outline-secondary">
                    <i className="fas fa-arrow-left me-1"></i> Về trang khóa học
                </Link>
            </div>

            {/* Right side - Prev/Next buttons */}
            <div className="d-flex">
                {prevLesson && (
                    <Link
                        to={`/courses/learn/${courseId}/${prevLesson.id}`}
                        className="btn btn-outline-primary me-2"
                    >
                        <i className="fas fa-step-backward me-1"></i> Bài trước
                    </Link>
                )}
                {nextLesson && (
                    <Link
                        to={`/courses/learn/${courseId}/${nextLesson.id}`}
                        className="btn btn-primary"
                    >
                        Bài tiếp theo <i className="fas fa-step-forward ms-1"></i>
                    </Link>
                )}
            </div>
        </div>
    );
};

export default LessonNavigation;
