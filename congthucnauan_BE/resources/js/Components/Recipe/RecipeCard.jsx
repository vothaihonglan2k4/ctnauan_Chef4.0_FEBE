import React from 'react';
import { Link } from 'react-router-dom';

const RecipeCard = ({ recipe }) => {
    const imageUrl = recipe.image 
        ? `http://localhost:8000/uploads/${recipe.image}`
        : 'http://localhost:8000/img/congthucnauan.jpg';

    return (
        <div className="col">
            <div className="card h-100">
                <img 
                    src={imageUrl}
                    className="card-img-top recipe-thumbnail" 
                    alt={recipe.title}
                    onError={(e) => {
                        e.target.src = 'http://localhost:8000/img/congthucnauan.jpg';
                    }}
                />
                <div className="card-body">
                    <h5 className="card-title">{recipe.title}</h5>
                    <p className="card-text text-truncate-3">
                        {recipe.description}
                    </p>
                    <p className="text-muted">
                        <small>
                            <i className="fas fa-user"></i> {recipe.user?.name || 'Anonymous'} |{' '}
                            <i className="fas fa-folder"></i> {recipe.category?.name || 'Uncategorized'}
                        </small>
                    </p>
                </div>
                <div className="card-footer">
                    <Link 
                        to={`/recipes/${recipe.id}`} 
                        className="btn btn-sm btn-primary"
                    >
                        Xem chi tiết
                    </Link>
                </div>
            </div>
        </div>
    );
};

export default RecipeCard;