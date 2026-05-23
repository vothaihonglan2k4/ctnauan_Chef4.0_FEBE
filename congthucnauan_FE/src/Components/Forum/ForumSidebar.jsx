import React from 'react';
import { Link } from 'react-router-dom';

const ForumSidebar = ({ popularTags }) => {
    return (
        <>
            {/* Popular Tags */}
            <div className="card mb-3">
                <div className="card-header bg-light">
                    <h6 className="mb-0">
                        <i className="fas fa-tags me-2"></i>Tags phổ biến
                    </h6>
                </div>
                <div className="card-body">
                    {popularTags && popularTags.length > 0 ? (
                        <div className="d-flex flex-wrap gap-2">
                            {popularTags.map(tag => (
                                <Link
                                    key={tag.id}
                                    to={`/forum/tag/${tag.slug}`}
                                    className="badge bg-primary text-decoration-none"
                                >
                                    {tag.name}
                                    {tag.posts_count && (
                                        <span className="badge bg-light text-dark ms-1">
                                            {tag.posts_count}
                                        </span>
                                    )}
                                </Link>
                            ))}
                        </div>
                    ) : (
                        <p className="text-muted small mb-0">Chưa có tag nào</p>
                    )}
                </div>
            </div>

            {/* Rules/Guidelines */}
            <div className="card">
                <div className="card-header bg-light">
                    <h6 className="mb-0">
                        <i className="fas fa-info-circle me-2"></i>Quy tắc diễn đàn
                    </h6>
                </div>
                <div className="card-body">
                    <ul className="small mb-0">
                        <li>Tôn trọng các thành viên khác</li>
                        <li>Không spam hoặc quảng cáo</li>
                        <li>Chia sẻ nội dung hữu ích</li>
                        <li>Sử dụng ngôn ngữ lịch sự</li>
                    </ul>
                </div>
            </div>
        </>
    );
};

export default ForumSidebar;
