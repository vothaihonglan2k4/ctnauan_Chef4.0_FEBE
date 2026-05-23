import React, { useState, useEffect } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import LoadingSpinner from '../Components/Common/LoadingSpinner';
import ForumPostCard from '../Components/Forum/ForumPostCard';
import ForumSearch from '../Components/Forum/ForumSearch';
import ForumSidebar from '../Components/Forum/ForumSidebar';

export default function ForumPage() {
    const [posts, setPosts] = useState([]);
    const [popularTags, setPopularTags] = useState([]);
    const [loading, setLoading] = useState(true);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const { user } = useAuth();
    const [searchParams] = useSearchParams();
    const searchQuery = searchParams.get('search');

    useEffect(() => {
        fetchPosts();
        fetchPopularTags();
    }, [currentPage, searchQuery]);

    const fetchPosts = async () => {
        try {
            let url = `/api/v1/forum/posts?page=${currentPage}`;
            if (searchQuery) {
                url += `&search=${encodeURIComponent(searchQuery)}`;
            }
            const response = await fetch(url);
            const data = await response.json();
            setPosts(data.posts || []);
            setTotalPages(data.pagination?.last_page || 1);
        } catch (error) {
            console.error('Error fetching posts:', error);
        } finally {
            setLoading(false);
        }
    };

    const fetchPopularTags = async () => {
        try {
            const response = await fetch('/api/v1/forum/tags');
            const data = await response.json();
            setPopularTags(data.tags || []);
        } catch (error) {
            console.error('Error fetching tags:', error);
        }
    };

    if (loading) {
        return <LoadingSpinner size="large" message="Đang tải diễn đàn..." />;
    }

    return (
        <div className="container mt-4">
            {/* Header Section */}
            <div className="row mb-4">
                <div className="col-md-8">
                    <h1>
                        <i className="fas fa-comments me-2"></i>
                        Diễn đàn chia sẻ công thức nấu ăn
                    </h1>
                    <p className="text-muted">Nơi cộng đồng chia sẻ kinh nghiệm, bí quyết nấu ăn</p>
                </div>
                <div className="col-md-4 text-end">
                    {user ? (
                        <Link to="/forum/create" className="btn btn-primary btn-lg">
                            <i className="fas fa-plus-circle me-2"></i>Tạo bài viết mới
                        </Link>
                    ) : (
                        <Link to="/login" className="btn btn-outline-primary btn-lg">
                            <i className="fas fa-sign-in-alt me-2"></i>Đăng nhập để đăng bài
                        </Link>
                    )}
                </div>
            </div>

            <div className="row">
                {/* Main Content */}
                <div className="col-md-9">
                    <ForumSearch />

                    {/* Post List */}
                    {posts.length > 0 ? (
                        <>
                            {posts.map(post => (
                                <ForumPostCard key={post.id} post={post} />
                            ))}

                            {/* Pagination */}
                            {totalPages > 1 && (
                                <nav aria-label="Page navigation">
                                    <ul className="pagination justify-content-center">
                                        {[...Array(totalPages)].map((_, index) => (
                                            <li key={index + 1} className={`page-item ${currentPage === index + 1 ? 'active' : ''}`}>
                                                <button
                                                    className="page-link"
                                                    onClick={() => setCurrentPage(index + 1)}
                                                >
                                                    {index + 1}
                                                </button>
                                            </li>
                                        ))}
                                    </ul>
                                </nav>
                            )}
                        </>
                    ) : (
                        <div className="alert alert-info text-center">
                            <i className="fas fa-info-circle me-2"></i>
                            Chưa có bài viết nào. Hãy là người đầu tiên chia sẻ!
                        </div>
                    )}
                </div>

                {/* Sidebar */}
                <div className="col-md-3">
                    <ForumSidebar popularTags={popularTags} />
                </div>
            </div>
        </div>
    );
}
