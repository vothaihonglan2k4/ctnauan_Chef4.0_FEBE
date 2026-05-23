import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import LoadingSpinner from '../Components/Common/LoadingSpinner';
import LessonSidebar from '../Components/Course/LessonSidebar';
import LessonNavigation from '../Components/Course/LessonNavigation';
import LessonContent from '../Components/Course/LessonContent';

export default function CourseLearnPage() {
    const { courseId, lessonId } = useParams();
    const navigate = useNavigate();
    const { isAuthenticated } = useAuth();

    const [course, setCourse] = useState(null);
    const [lessons, setLessons] = useState([]);
    const [currentLesson, setCurrentLesson] = useState(null);
    const [isEnrolled, setIsEnrolled] = useState(false);
    const [enrollment, setEnrollment] = useState(null);
    const [completedLessons, setCompletedLessons] = useState([]);
    const [loading, setLoading] = useState(true);
    const [sidebarOpen, setSidebarOpen] = useState(false);

    useEffect(() => {
        fetchCourseContent();
    }, [courseId]);

    useEffect(() => {
        if (lessons.length > 0) {
            if (lessonId) {
                const found = lessons.find(l => l.id == lessonId);
                if (found) {
                    setCurrentLesson(found);
                } else {
                    // Invalid lesson ID, redirect to first lesson
                    navigate(`/courses/learn/${courseId}/${lessons[0].id}`);
                }
            } else {
                // No lesson ID provided, redirect to first lesson
                navigate(`/courses/learn/${courseId}/${lessons[0].id}`);
            }
        }
    }, [lessonId, lessons]);

    const fetchCourseContent = async () => {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                navigate('/login');
                return;
            }

            const response = await fetch(`/api/v1/courses/${courseId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                if (response.status === 403 || response.status === 401) {
                    alert('Bạn chưa đăng ký khóa học này hoặc chưa đăng nhập');
                    navigate(`/courses/${courseId}`);
                    return;
                }
                throw new Error('Failed to fetch course content');
            }

            const data = await response.json();

            if (data.course) {
                setCourse(data.course);
                setLessons(data.lessons);
                setIsEnrolled(data.is_enrolled);
                setEnrollment(data.enrollment);

                // Extract completed lessons
                const completed = data.lessons
                    .filter(l => l.is_completed)
                    .map(l => l.id);
                setCompletedLessons(completed);
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleMarkComplete = async () => {
        if (!currentLesson) return;

        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`/api/v1/courses/${courseId}/lessons/${currentLesson.id}/complete`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                setCompletedLessons([...completedLessons, currentLesson.id]);
                setEnrollment(prev => ({ ...prev, progress: data.progress }));

                // Auto advance to next lesson
                const nextLesson = getNextLesson();
                if (nextLesson) {
                    setTimeout(() => {
                        navigate(`/courses/learn/${courseId}/${nextLesson.id}`);
                    }, 1500);
                } else {
                    alert('Chúc mừng! Bạn đã hoàn thành khóa học.');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật tiến độ');
        }
    };

    const getNextLesson = () => {
        if (!currentLesson || lessons.length === 0) return null;
        const index = lessons.findIndex(l => l.id === currentLesson.id);
        return index < lessons.length - 1 ? lessons[index + 1] : null;
    };

    const getPrevLesson = () => {
        if (!currentLesson || lessons.length === 0) return null;
        const index = lessons.findIndex(l => l.id === currentLesson.id);
        return index > 0 ? lessons[index - 1] : null;
    };

    if (loading) {
        return <LoadingSpinner size="large" message="Đang tải nội dung khóa học..." />;
    }

    if (!course || !currentLesson) return null;

    const nextLesson = getNextLesson();
    const prevLesson = getPrevLesson();
    const isCompleted = completedLessons.includes(currentLesson.id);

    return (
        <div className="container-fluid py-4">
            <div className="row">
                {/* Lesson Sidebar */}
                <LessonSidebar
                    course={course}
                    lessons={lessons}
                    currentLesson={currentLesson}
                    courseId={courseId}
                    isEnrolled={isEnrolled}
                    completedLessons={completedLessons}
                    enrollment={enrollment}
                    sidebarOpen={sidebarOpen}
                    onToggleSidebar={setSidebarOpen}
                />

                {/* Main Content */}
                <div className="col-lg-9">
                    {/* Navigation */}
                    <LessonNavigation
                        courseId={courseId}
                        prevLesson={prevLesson}
                        nextLesson={nextLesson}
                        showSidebarButton={true}
                        onToggleSidebar={setSidebarOpen}
                    />

                    {/* Lesson Content */}
                    <LessonContent
                        lesson={currentLesson}
                        courseId={courseId}
                        isCompleted={isCompleted}
                        nextLesson={nextLesson}
                        onMarkComplete={handleMarkComplete}
                    />
                </div>
            </div>
        </div>
    );
}
