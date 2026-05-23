import { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import LoadingSpinner from '../Components/Common/LoadingSpinner';
import CourseInfo from '../Components/Course/CourseInfo';
import CourseLessonsList from '../Components/Course/CourseLessonsList';
import CourseEnrollCard from '../Components/Course/CourseEnrollCard';

export default function CourseDetailPage() {
    const { id } = useParams();
    const navigate = useNavigate();
    const { isAuthenticated, user } = useAuth();

    const [course, setCourse] = useState(null);
    const [lessons, setLessons] = useState([]);
    const [isEnrolled, setIsEnrolled] = useState(false);
    const [enrollment, setEnrollment] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        fetchCourseDetails();
    }, [id]);

    const fetchCourseDetails = async () => {
        try {
            const token = localStorage.getItem('token');
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };

            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }

            const response = await fetch(`/api/v1/courses/${id}`, { headers });

            if (!response.ok) {
                throw new Error('Failed to fetch course details');
            }

            const data = await response.json();

            if (data.course) {
                setCourse(data.course);
                setLessons(data.lessons || []);
                setIsEnrolled(data.is_enrolled || false);
                setEnrollment(data.enrollment || null);
            } else {
                setError(data.message || 'Failed to load course');
            }
        } catch (err) {
            console.error('Error:', err);
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    const handleEnroll = async () => {
        if (!isAuthenticated) {
            navigate('/login', { state: { from: `/courses/${id}` } });
            return;
        }

        if (course.price > 0) {
            navigate(`/courses/${id}/checkout`);
            return;
        }

        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`/api/v1/courses/${id}/enroll`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            const data = await response.json();

            if (data.success) {
                setIsEnrolled(true);
                setEnrollment(data.enrollment);
                alert('Đăng ký thành công!');
            } else {
                alert(data.message || 'Đăng ký thất bại');
            }
        } catch (err) {
            console.error('Error:', err);
            alert('Có lỗi xảy ra khi đăng ký');
        }
    };

    if (loading) {
        return <LoadingSpinner size="large" message="Đang tải thông tin khóa học..." />;
    }

    if (error || !course) {
        return (
            <div className="container py-5">
                <div className="alert alert-danger">
                    <i className="fas fa-exclamation-triangle me-2"></i>
                    {error || 'Không tìm thấy khóa học'}
                </div>
                <Link to="/courses" className="btn btn-outline-primary">
                    <i className="fas fa-arrow-left me-2"></i> Quay lại danh sách
                </Link>
            </div>
        );
    }

    return (
        <div className="container py-5">
            {/* Breadcrumb */}
            <nav aria-label="breadcrumb">
                <ol className="breadcrumb">
                    <li className="breadcrumb-item"><Link to="/">Trang chủ</Link></li>
                    <li className="breadcrumb-item"><Link to="/courses">Khóa học online</Link></li>
                    <li className="breadcrumb-item active">{course.title}</li>
                </ol>
            </nav>

            <div className="row">
                {/* Main Content */}
                <div className="col-lg-8">
                    {/* Course Info */}
                    <CourseInfo course={course} lessons={lessons} />

                    {/* Lessons List */}
                    <CourseLessonsList
                        lessons={lessons}
                        courseId={course.id}
                        isEnrolled={isEnrolled}
                    />
                </div>

                {/* Sidebar */}
                <div className="col-lg-4">
                    <CourseEnrollCard
                        course={course}
                        isEnrolled={isEnrolled}
                        enrollment={enrollment}
                        onEnroll={handleEnroll}
                    />
                </div>
            </div>
        </div>
    );
}
