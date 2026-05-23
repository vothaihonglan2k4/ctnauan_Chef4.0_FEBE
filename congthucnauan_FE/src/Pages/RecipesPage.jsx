import React, { useState, useEffect } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import CategoryFilter from '../Components/Recipe/CategoryFilter';
import RecipeGrid from '../Components/Recipe/RecipeGrid';

const RecipesPage = () => {
    const [recipes, setRecipes] = useState([]);
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(true);
    const [selectedCategory, setSelectedCategory] = useState(null);
    const [searchParams] = useSearchParams();
    const searchTerm = (searchParams.get('term') || searchParams.get('search') || '').trim();
    const isLoggedIn = localStorage.getItem('token') !== null;

    useEffect(() => {
        // Fetch categories
        fetch('/api/v1/categories')
            .then(res => res.json())
            .then(data => {
                setCategories(data.categories || []);
            })
            .catch(err => console.error('Error fetching categories:', err));
    }, []);

    useEffect(() => {
        fetchRecipes();
    }, [selectedCategory, searchTerm]);

    const fetchRecipes = () => {
        setLoading(true);
        const params = new URLSearchParams();

        if (selectedCategory) {
            params.append('category_id', selectedCategory);
        }

        if (searchTerm) {
            params.append('search', searchTerm);
        }

        const queryString = params.toString();
        const url = queryString ? `/api/v1/recipes?${queryString}` : '/api/v1/recipes';

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
                    <h1>{searchTerm ? `Kết quả tìm kiếm: ${searchTerm}` : 'Tất cả công thức nấu ăn'}</h1>
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