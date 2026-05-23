import React from 'react';
import { Link } from 'react-router-dom';

const RelatedRecipes = ({ recipes }) => {
    if (recipes.length === 0) return null;

    return (
        <div className="card mb-4">
            <div className="card-header bg-light">
                <h5 className="mb-0">Công thức liên quan</h5>
            </div>
            <div className="card-body">
                {recipes.map(related => (
                    <div className="mb-3" key={related.id}>
                        <Link to={`/recipes/${related.id}`} className="text-decoration-none">
                            <div className="row g-0">
                                <div className="col-4">
                                    <img
                                        src={`http://localhost:8000/uploads/${related.image}`}
                                        className="img-fluid rounded"
                                        alt={related.title}
                                        onError={(e) => e.target.src = 'https://via.placeholder.com/100?text=No+Image'}
                                    />
                                </div>
                                <div className="col-8 ps-2">
                                    <p className="mb-0 fw-bold">{related.title}</p>
                                    <small className="text-muted">{related.category?.name}</small>
                                </div>
                            </div>
                        </Link>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default RelatedRecipes;
