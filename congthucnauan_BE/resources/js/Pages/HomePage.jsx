import React, { useState, useEffect } from 'react';
import HeroSection from '../Components/Home/HeroSection';
import FeaturesSection from '../Components/Home/FeaturesSection';
import RecipeGrid from '../Components/Recipe/RecipeGrid';

const HomePage = () => {
    const [recipes, setRecipes] = useState([]);
    const [loading, setLoading] = useState(true);
    const isLoggedIn = localStorage.getItem('token') !== null;

    useEffect(() => {
        // Fetch featured recipes from API
        fetch('/api/v1/recipes?limit=6')
            .then(res => res.json())
            .then(data => {
                console.log('API Response:', data);
                // API trả về data.recipes thay vì data.data
                if (data.recipes && data.recipes.length > 0) {
                    setRecipes(data.recipes);
                } else {
                    setRecipes([]);
                }
                setLoading(false);
            })
            .catch(err => {
                console.error('Error fetching recipes:', err);
                setLoading(false);
            });
    }, []);

    return (
        <div className="container mt-4">
            {/* Hero Section */}
            <HeroSection isLoggedIn={isLoggedIn} />

            {/* Featured Recipes Section */}
            <div className="container">
                <div className="row mb-4">
                    <div className="col">
                        <h2>Công Thức Nổi Bật</h2>
                        <hr />
                    </div>
                </div>

                <div className="row row-cols-1 row-cols-md-3 g-4">
                    <RecipeGrid
                        recipes={recipes}
                        loading={loading}
                        emptyMessage="Chưa có công thức nào."
                    />
                </div>

                {/* Features Section */}
                <FeaturesSection isLoggedIn={isLoggedIn} />
            </div>
        </div>
    );
};

export default HomePage;