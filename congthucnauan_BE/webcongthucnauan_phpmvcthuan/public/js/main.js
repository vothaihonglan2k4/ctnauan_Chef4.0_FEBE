/**
 * Main JavaScript file for the Cooking Recipes website
 */

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Auto close alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const alertInstance = new bootstrap.Alert(alert);
            alertInstance.close();
        }, 5000);
    });
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(function(tooltip) {
        new bootstrap.Tooltip(tooltip);
    });
    
    // Payment method selection highlight
    const paymentMethods = document.querySelectorAll('.payment-method-item input');
    paymentMethods.forEach(function(method) {
        method.addEventListener('change', function() {
            // Remove active class from all
            document.querySelectorAll('.payment-method-item').forEach(function(item) {
                item.classList.remove('active');
            });
            // Add active class to selected
            if (this.checked) {
                this.closest('.payment-method-item').classList.add('active');
            }
        });
    });
    
    // Select all checkboxes in admin tables
    const selectAllCheckboxes = document.querySelectorAll('.select-all-checkbox');
    selectAllCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll(this.dataset.target);
            checkboxes.forEach(function(item) {
                item.checked = checkbox.checked;
            });
        });
    });
    
    // Recipe rating functionality
    const ratingInputs = document.querySelectorAll('.rating-input');
    if (ratingInputs.length > 0) {
        ratingInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                const value = this.value;
                const stars = this.closest('.rating-container').querySelectorAll('.rating-stars i');
                
                stars.forEach(function(star, index) {
                    if (index < value) {
                        star.classList.add('fas');
                        star.classList.remove('far');
                    } else {
                        star.classList.add('far');
                        star.classList.remove('fas');
                    }
                });
            });
        });
        
        // Star click functionality
        const ratingStars = document.querySelectorAll('.rating-stars i');
        ratingStars.forEach(function(star) {
            star.addEventListener('click', function() {
                const value = this.dataset.value;
                const container = this.closest('.rating-container');
                const input = container.querySelector('.rating-input');
                
                input.value = value;
                
                // Trigger the change event
                const event = new Event('change');
                input.dispatchEvent(event);
            });
        });
    }
    
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Image preview for recipe add/edit
    const recipeImageInput = document.getElementById('image');
    const recipeImagePreview = document.getElementById('image-preview');
    
    if (recipeImageInput && recipeImagePreview) {
        recipeImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    recipeImagePreview.src = e.target.result;
                    recipeImagePreview.classList.remove('d-none');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // Confirm delete modals
    const deleteButtons = document.querySelectorAll('[data-confirm="true"]');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            if (!confirm('Bạn có chắc chắn muốn xóa?')) {
                event.preventDefault();
            }
        });
    });
});

// Function to handle ingredient quantity calculations
function calculateIngredients(servings, defaultServings = 4) {
    const ratio = servings / defaultServings;
    const ingredients = document.querySelectorAll('.ingredient-quantity');
    
    ingredients.forEach(function(ingredient) {
        const defaultValue = parseFloat(ingredient.dataset.default);
        const newValue = (defaultValue * ratio).toFixed(1).replace(/\.0$/, '');
        ingredient.textContent = newValue;
    });
}

// Function to handle print recipe
function printRecipe() {
    window.print();
}
