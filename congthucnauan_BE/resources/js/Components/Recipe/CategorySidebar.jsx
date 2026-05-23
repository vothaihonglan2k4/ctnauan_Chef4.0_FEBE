import React from 'react';
import { Link } from 'react-router-dom';

const CategorySidebar = ({ categories }) => {
    if (categories.length === 0) return null;

    return (
        <div className="card mb-4">
            <div className="card-header bg-light">
                <h5 className="mb-0">Danh mục</h5>
            </div>
            <ul className="list-group list-group-flush">
                {categories.map(category => (
                    <li className="list-group-item" key={category.id}>
                        <Link to={`/recipes?category=${category.id}`} className="text-decoration-none">
                            {category.name}
                        </Link>
                    </li>
                ))}
            </ul>
        </div>
    );
};

export default CategorySidebar;
