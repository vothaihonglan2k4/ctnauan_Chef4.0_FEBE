import React from 'react';
import { Link } from 'react-router-dom';
import VideoPlayer from './VideoPlayer';

const LessonContent = ({
    lesson,
    courseId,
    isCompleted,
    nextLesson,
    onMarkComplete
}) => {
    if (!lesson) return null;

    const fallbackImage = lesson.image ? `/uploads/lessons/${lesson.image}` : null;

    return (
        <div className="card shadow-sm mb-4">
            <div className="card-body">
                <h2 className="card-title mb-4">{lesson.title}</h2>

                {/* Video/Image Player */}
                <VideoPlayer
                    videoUrl={lesson.video_url}
                    title={lesson.title}
                    fallbackImage={fallbackImage}
                />

                {/* Lesson Content (HTML) */}
                {lesson.content && (
                    <div
                        className="lesson-content mt-4"
                        dangerouslySetInnerHTML={{ __html: lesson.content }}
                    ></div>
                )}
            </div>

            {/* Footer with duration and action buttons */}
            <div className="card-footer bg-white d-flex justify-content-between align-items-center py-3">
                <span>
                    <i className="fas fa-clock me-1"></i> {lesson.duration_minutes} phút
                </span>

                <div>
                    {/* Mark Complete Button */}
                    <button
                        onClick={onMarkComplete}
                        className={`btn ${isCompleted ? 'btn-success disabled' : 'btn-success'}`}
                        disabled={isCompleted}
                    >
                        {isCompleted ? (
                            <>
                                <i className="fas fa-check-circle me-1"></i> Đã hoàn thành
                            </>
                        ) : (
                            <>
                                <i className="fas fa-check me-1"></i> Đánh dấu hoàn thành
                            </>
                        )}
                    </button>

                    {/* Next Lesson Button */}
                    {nextLesson && (
                        <Link
                            to={`/courses/learn/${courseId}/${nextLesson.id}`}
                            className="btn btn-primary ms-2"
                        >
                            Bài tiếp theo <i className="fas fa-arrow-right ms-1"></i>
                        </Link>
                    )}
                </div>
            </div>
        </div>
    );
};

export default LessonContent;
