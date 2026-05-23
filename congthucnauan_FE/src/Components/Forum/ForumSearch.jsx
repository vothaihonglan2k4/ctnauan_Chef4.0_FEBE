import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';

const ForumSearch = () => {
    const [searchQuery, setSearchQuery] = useState('');
    const navigate = useNavigate();

    const handleSearch = (e) => {
        e.preventDefault();
        if (searchQuery.trim()) {
            navigate(`/forum?search=${encodeURIComponent(searchQuery)}`);
        }
    };

    return (
        <div className="card mb-3">
            <div className="card-body">
                <form onSubmit={handleSearch} className="d-flex">
                    <input
                        type="text"
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        className="form-control me-2"
                        placeholder="Tìm kiếm bài viết..."
                        required
                    />
                    <button type="submit" className="btn btn-primary">
                        <i className="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    );
};

export default ForumSearch;
