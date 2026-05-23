import React from 'react';
import RecipeCard from './RecipeCard';

const RecipeGrid = ({ recipes, loading, emptyMessage = 'Chưa có công thức nào.' }) => {
    if (loading) {
        return (
            <div className="text-center py-5">
                <div className="spinner-border text-primary spinner-border-custom" role="status">
                    <span className="visually-hidden">Đang tải...</span>
                </div>
            </div>
        );
    }

    if (!recipes || recipes.length === 0) {
        return (
            <div className="col-12">
                <p className="text-center">{emptyMessage}</p>
            </div>
        );
    }

    return (
        <>
            {recipes.map(recipe => (
                <RecipeCard key={recipe.id} recipe={recipe} />
            ))}
        </>
    );
};

export default RecipeGrid;
