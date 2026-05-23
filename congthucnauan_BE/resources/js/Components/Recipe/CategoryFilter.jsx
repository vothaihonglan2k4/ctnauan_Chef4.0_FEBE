import React from 'react';

const CategoryFilter = ({ categories, selectedCategory, onSelectCategory }) => {
    return (
        <div className="card mb-4">
            <div className="card-header bg-light">
                <h5 className="mb-0">Danh mục</h5>
            </div>
            <div className="card-body">
                <ul className="list-group list-group-flush">
                    <li
                        className={`list-group-item ${!selectedCategory ? 'active' : ''}`}
                        style={{ cursor: 'pointer' }}
                        onClick={() => onSelectCategory(null)}
                    >
                        Tất cả công thức
                    </li>
                    {categories.map(category => (
                        <li
                            key={category.id}
                            className={`list-group-item ${selectedCategory === category.id ? 'active' : ''}`}
                            style={{ cursor: 'pointer' }}
                            onClick={() => onSelectCategory(category.id)}
                        >
                            {category.name}
                        </li>
                    ))}
                </ul>
            </div>
        </div>
    );
};

export default CategoryFilter;
