import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import CategoryFilter from '../Components/Recipe/CategoryFilter';
import RecipeGrid from '../Components/Recipe/RecipeGrid';

const RecipesPage = () => {
    const [recipes, setRecipes] = useState([]);
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(true);
    const [selectedCategory, setSelectedCategory] = useState(null);
    const isLoggedIn = localStorage.getItem('token') !== null;

    useEffect(() => {
        // Fetch categories
        fetch('/api/v1/categories')
            .then(res => res.json())
            .then(data => {
                setCategories(data.categories || []);
            })
            .catch(err => console.error('Error fetching categories:', err));

        // Fetch recipes
        fetchRecipes();
    }, [selectedCategory]);

    const fetchRecipes = () => {
        setLoading(true);
        const url = selectedCategory
            ? `/api/v1/recipes?category_id=${selectedCategory}`
            : '/api/v1/recipes';

        fetch(url)
            .then(res => res.json())
            .then(data => {
                setRecipes(data.recipes || []);
                setLoading(false);
            })
            .catch(err => {
                console.error('Error fetching recipes:', err);
                setLoading(false);
            });
    };

    const handleCategorySelect = (categoryId) => {
        setSelectedCategory(categoryId);
    };

    return (
        <div className="container mt-4">
            {/* Header */}
            <div className="row mb-3">
                <div className="col-md-8">
                    <h1>Tất cả công thức nấu ăn</h1>
                </div>
                <div className="col-md-4 text-end">
                    {isLoggedIn && (
                        <Link to="/recipes/add" className="btn btn-primary">
                            <i className="fas fa-plus"></i> Thêm công thức
                        </Link>
                    )}
                </div>
            </div>

            <div className="row">
                {/* Sidebar - Category Filter */}
                <div className="col-md-3">
                    <CategoryFilter
                        categories={categories}
                        selectedCategory={selectedCategory}
                        onSelectCategory={handleCategorySelect}
                    />
                </div>

                {/* Main Content - Recipe Grid */}
                <div className="col-md-9">
                    <div className="row row-cols-1 row-cols-md-3 g-4">
                        <RecipeGrid
                            recipes={recipes}
                            loading={loading}
                            emptyMessage="Không tìm thấy công thức nào."
                        />
                    </div>
                </div>
            </div>
        </div>
    );
};

export default RecipesPage;