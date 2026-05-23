import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import LoadingSpinner from '../Components/Common/LoadingSpinner';
import ForumPostCard from '../Components/Forum/ForumPostCard';
import ForumSearch from '../Components/Forum/ForumSearch';
import ForumSidebar from '../Components/Forum/ForumSidebar';

export default function ForumTagPage() {
    const { tagName } = useParams();
    const [posts, setPosts] = useState([]);
    const [tag, setTag] = useState(null);
    const [popularTags, setPopularTags] = useState([]);
    const [loading, setLoading] = useState(true);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const { user } = useAuth();

    useEffect(() => {
        fetchPosts();
        fetchPopularTags();
    }, [tagName, currentPage]);

    const fetchPosts = async () => {
        setLoading(true);
        try {
            const response = await fetch(`/api/v1/forum/tags/${tagName}?page=${currentPage}`);
            const data = await response.json();
            if (response.ok) {
                setPosts(data.posts || []);
                setTag(data.tag);
                setTotalPages(data.pagination?.last_page || 1);
            }
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
        return <LoadingSpinner size="large" message="Đang tải..." />;
    }

    return (
        <div className="container mt-4">
            {/* Header Section */}
            <div className="row mb-4">
                <div className="col-md-8">
                    <h1>
                        <i className="fas fa-tag me-2"></i>
                        Tag: {tag?.name || tagName}
                    </h1>
                    <p className="text-muted">Danh sách bài viết gắn thẻ "{tag?.name || tagName}"</p>
                </div>
                <div className="col-md-4 text-end">
                    <Link to="/forum" className="btn btn-outline-secondary">
                        <i className="fas fa-arrow-left me-2"></i>Quay lại diễn đàn
                    </Link>
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
                            Chưa có bài viết nào với tag này.
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
