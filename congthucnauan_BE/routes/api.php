<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\ForumController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\StripePaymentController;
use App\Http\Controllers\Api\VNPayPaymentController;
use App\Http\Controllers\Api\MomoPaymentController;
use App\Http\Controllers\Api\SePayPaymentController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AdminCategoryController;
use App\Http\Controllers\Api\AdminClassroomController;
use App\Http\Controllers\Api\AdminCourseController;
use App\Http\Controllers\Api\AdminPaymentController;
use App\Http\Controllers\Api\AdminReportController;
use App\Http\Controllers\Api\AdminContactController;
use App\Http\Controllers\Api\AdminRecipeController;
use App\Http\Controllers\Api\AdminCommentController;
use App\Http\Controllers\Api\ManagerDashboardController;
use App\Http\Controllers\Api\ManagerRecipeController;
use App\Http\Controllers\Api\ManagerContactController;
use App\Http\Controllers\Api\ManagerCourseController;
use App\Http\Controllers\Api\ManagerReportController;
use App\Http\Controllers\Api\AdminRbacController;
use App\Http\Controllers\Api\ChatbotController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('v1')->group(function () {
    // Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('/resend-verification', [AuthController::class, 'resendVerificationCode']);

    // Recipes - Public
    Route::get('/recipes', [RecipeController::class, 'index']);
    Route::get('/recipes/{id}', [RecipeController::class, 'show']);
    Route::get('/categories', [RecipeController::class, 'categories']);

    // Courses - Public
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);

    // Forum - Public
    Route::get('/forum/posts', [ForumController::class, 'index']);
    Route::get('/forum/posts/{id}', [ForumController::class, 'show']);
    Route::get('/forum/tags', [ForumController::class, 'tags']);
    Route::get('/forum/tags/{tagName}', [ForumController::class, 'getByTag']);

    // Ratings - Public (read only)
    Route::get('/recipes/{recipeId}/ratings', [RatingController::class, 'index']);
    
    // Contact - Public
    Route::post('/contact', [ContactController::class, 'submit']);

    // AI Chatbot - Public
    Route::post('/chatbot', [ChatbotController::class, 'send']);
});

// Protected routes (require authentication)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Recipes - Authenticated
    Route::post('/recipes', [RecipeController::class, 'store']);
    Route::put('/recipes/{id}', [RecipeController::class, 'update']);
    Route::delete('/recipes/{id}', [RecipeController::class, 'destroy']);
    Route::get('/my-recipes', [RecipeController::class, 'myRecipes']);

    // Ratings
    Route::post('/recipes/{recipeId}/ratings', [RatingController::class, 'store']);
    Route::get('/recipes/{recipeId}/my-rating', [RatingController::class, 'getUserRating']);
    Route::delete('/recipes/{recipeId}/ratings/{ratingId}', [RatingController::class, 'destroy']);

    // Courses - Authenticated
    Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll']);
    Route::get('/my-courses', [CourseController::class, 'myCourses']);
    Route::get('/my-courses/schedule', [CourseController::class, 'mySchedule']);
    Route::get('/courses/{courseId}/lessons/{lessonId}', [CourseController::class, 'getLesson']);
    Route::post('/courses/{courseId}/lessons/{lessonId}/complete', [CourseController::class, 'completeLesson']);

    // Forum - Authenticated
    Route::post('/forum/posts', [ForumController::class, 'store']);
    Route::put('/forum/posts/{id}', [ForumController::class, 'update']);
    Route::delete('/forum/posts/{id}', [ForumController::class, 'destroy']);
    Route::post('/forum/posts/{id}/comments', [ForumController::class, 'addComment']);
    Route::delete('/forum/posts/{postId}/comments/{commentId}', [ForumController::class, 'deleteComment']);
    Route::post('/forum/posts/{id}/like', [ForumController::class, 'toggleLike']);

    // Payments - Authenticated
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/{id}', [PaymentController::class, 'show']);
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::put('/payments/{id}/status', [PaymentController::class, 'updateStatus']);
    Route::get('/payments/statistics', [PaymentController::class, 'statistics']);

    // Stripe Payments
    Route::post('/stripe/create-checkout-session', [StripePaymentController::class, 'createCheckoutSession']);
    Route::post('/stripe/verify-payment', [StripePaymentController::class, 'verifyPayment']);
    Route::get('/stripe/config', [StripePaymentController::class, 'getConfig']);

    // VNPay Payments
    Route::post('/vnpay/create-payment-url', [VNPayPaymentController::class, 'createPaymentUrl']);
    Route::get('/vnpay/config', [VNPayPaymentController::class, 'getConfig']);

    // MoMo Payments
    Route::post('/momo/create-payment', [MomoPaymentController::class, 'createPayment']);
    Route::get('/momo/config', [MomoPaymentController::class, 'getConfig']);

    // SePay Payments
    Route::post('/sepay/create-payment', [SePayPaymentController::class, 'createPayment']);
    Route::post('/sepay/verify-payment', [SePayPaymentController::class, 'verifyPayment']);
    Route::get('/sepay/config', [SePayPaymentController::class, 'getConfig']);
});

// Stripe Webhook (no auth required)
Route::post('/v1/stripe/webhook', [StripePaymentController::class, 'webhook']);

// VNPay Return (no auth required)
Route::get('/v1/vnpay/return', [VNPayPaymentController::class, 'handleReturn']);

// MoMo Return/IPN (no auth required)
Route::get('/v1/momo/return', [MomoPaymentController::class, 'handleReturn']);
Route::post('/v1/momo/ipn', [MomoPaymentController::class, 'handleIpn']);
Route::post('/payment/momo/ipn', [MomoPaymentController::class, 'handleIpn']);

// SePay Return/IPN (no auth required)
Route::get('/v1/sepay/return', [SePayPaymentController::class, 'handleReturn']);
Route::post('/v1/sepay/ipn', [SePayPaymentController::class, 'handleIpn']);

// Admin routes
Route::prefix('v1/admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'getStats']);
    Route::get('/dashboard/recent-users', [AdminDashboardController::class, 'getRecentUsers']);
    Route::get('/dashboard/recent-payments', [AdminDashboardController::class, 'getRecentPayments']);
    Route::get('/dashboard/recent-courses', [AdminDashboardController::class, 'getRecentCourses']);
    
    // User Management
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/{id}', [AdminUserController::class, 'show']);
    Route::post('/users', [AdminUserController::class, 'store']);
    Route::put('/users/{id}', [AdminUserController::class, 'update']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);
    
    // Category Management
    Route::get('/categories', [AdminCategoryController::class, 'index']);
    Route::post('/categories', [AdminCategoryController::class, 'store']);
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update']);
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy']);
    
    // Classroom Management
    Route::get('/classrooms', [AdminClassroomController::class, 'index']);
    Route::post('/classrooms', [AdminClassroomController::class, 'store']);
    Route::put('/classrooms/{id}', [AdminClassroomController::class, 'update']);
    Route::delete('/classrooms/{id}', [AdminClassroomController::class, 'destroy']);
    
    // Course Management
    Route::get('/courses', [AdminCourseController::class, 'index']);
    Route::post('/courses', [AdminCourseController::class, 'store']);
    Route::put('/courses/{id}', [AdminCourseController::class, 'update']);
    Route::delete('/courses/{id}', [AdminCourseController::class, 'destroy']);
    Route::get('/courses/classrooms', [AdminCourseController::class, 'classrooms']);
    
    // Payment Management
    Route::get('/payments', [AdminPaymentController::class, 'index']);
    Route::get('/payments/statistics', [AdminPaymentController::class, 'statistics']);
    Route::get('/payments/{id}', [AdminPaymentController::class, 'show']);
    Route::put('/payments/{id}/status', [AdminPaymentController::class, 'updateStatus']);
    
    // Reports
    Route::get('/reports/overview', [AdminReportController::class, 'overview']);
    Route::get('/reports/recipes-by-category', [AdminReportController::class, 'recipesByCategory']);
    Route::get('/reports/revenue-by-month', [AdminReportController::class, 'revenueByMonth']);
    Route::get('/reports/user-registrations', [AdminReportController::class, 'userRegistrations']);
    Route::get('/reports/payments-by-method', [AdminReportController::class, 'paymentsByMethod']);
    Route::get('/reports/popular-recipes', [AdminReportController::class, 'popularRecipes']);
    Route::get('/reports/top-courses', [AdminReportController::class, 'topCourses']);

    // Contact Management
    Route::get('/contacts', [AdminContactController::class, 'index']);
    Route::get('/contacts/{id}', [AdminContactController::class, 'show']);
    Route::put('/contacts/{id}/status', [AdminContactController::class, 'updateStatus']);
    Route::delete('/contacts/{id}', [AdminContactController::class, 'destroy']);

    // Recipe Management
    Route::get('/recipes', [AdminRecipeController::class, 'index']);
    Route::post('/recipes/{id}/approve', [AdminRecipeController::class, 'approve']);
    Route::post('/recipes/{id}/reject', [AdminRecipeController::class, 'reject']);
    Route::delete('/recipes/{id}', [AdminRecipeController::class, 'destroy']);

    // Comment Management
    Route::get('/comments', [AdminCommentController::class, 'index']);
    Route::get('/comments/{id}', [AdminCommentController::class, 'show']);
    Route::delete('/comments/{id}', [AdminCommentController::class, 'destroy']);
    Route::post('/comments/delete-multiple', [AdminCommentController::class, 'destroyMultiple']);

    // RBAC - Role Management
    Route::get('/rbac/roles', [AdminRbacController::class, 'getRoles']);
    Route::get('/rbac/roles/{roleId}', [AdminRbacController::class, 'getRole']);
    Route::post('/rbac/roles', [AdminRbacController::class, 'createRole']);
    Route::put('/rbac/roles/{roleId}', [AdminRbacController::class, 'updateRole']);
    Route::delete('/rbac/roles/{roleId}', [AdminRbacController::class, 'deleteRole']);
    
    // RBAC - Permission Management
    Route::get('/rbac/permissions', [AdminRbacController::class, 'getPermissions']);
    Route::get('/rbac/permissions/by-module', [AdminRbacController::class, 'getPermissionsByModule']);
    Route::post('/rbac/permissions', [AdminRbacController::class, 'createPermission']);
    Route::put('/rbac/permissions/{permissionId}', [AdminRbacController::class, 'updatePermission']);
    Route::delete('/rbac/permissions/{permissionId}', [AdminRbacController::class, 'deletePermission']);
    
    // RBAC - Role Permissions
    Route::get('/rbac/roles/{roleId}/permissions', [AdminRbacController::class, 'getRolePermissions']);
    Route::put('/rbac/roles/{roleId}/permissions', [AdminRbacController::class, 'updateRolePermissions']);
    
    // RBAC - User Roles
    Route::get('/rbac/users/{userId}/roles', [AdminRbacController::class, 'getUserRoles']);
    Route::post('/rbac/users/{userId}/roles', [AdminRbacController::class, 'assignUserRole']);
    Route::delete('/rbac/users/{userId}/roles', [AdminRbacController::class, 'removeUserRole']);
    
    // RBAC - Utility
    Route::get('/rbac/all-roles-permissions', [AdminRbacController::class, 'getAllRolesWithPermissions']);
    Route::get('/rbac/users-with-roles', [AdminRbacController::class, 'getUsersWithRoles']);
});

// Manager routes
Route::prefix('v1/manager')->middleware(['auth:sanctum', 'manager'])->group(function () {
    // Dashboard
    Route::get('/dashboard/stats', [ManagerDashboardController::class, 'getStats']);
    Route::get('/dashboard/recent-contacts', [ManagerDashboardController::class, 'getRecentContacts']);
    Route::get('/dashboard/pending-recipes', [ManagerDashboardController::class, 'getPendingRecipes']);
    
    // Recipe Management (approve/reject)
    Route::get('/recipes', [ManagerRecipeController::class, 'index']);
    Route::put('/recipes/{id}/approve', [ManagerRecipeController::class, 'approve']);
    Route::put('/recipes/{id}/reject', [ManagerRecipeController::class, 'reject']);
    
    // Contact Management
    Route::get('/contacts', [ManagerContactController::class, 'index']);
    Route::get('/contacts/{id}', [ManagerContactController::class, 'show']);
    Route::put('/contacts/{id}/status', [ManagerContactController::class, 'updateStatus']);
    Route::delete('/contacts/{id}', [ManagerContactController::class, 'destroy']);
    
    // Course Management
    Route::get('/courses', [ManagerCourseController::class, 'index']);
    Route::post('/courses', [ManagerCourseController::class, 'store']);
    Route::put('/courses/{id}', [ManagerCourseController::class, 'update']);
    Route::delete('/courses/{id}', [ManagerCourseController::class, 'destroy']);
    Route::put('/courses/{id}/status', [ManagerCourseController::class, 'updateStatus']);
    Route::get('/courses/classrooms', [ManagerCourseController::class, 'classrooms']);
    
    // Reports
    Route::get('/reports/overview', [ManagerReportController::class, 'overview']);

    // User Management (read-only)
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/{id}', [AdminUserController::class, 'show']);
    Route::put('/users/{id}', [AdminUserController::class, 'update']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);

    // Category Management
    Route::get('/categories', [AdminCategoryController::class, 'index']);
    Route::post('/categories', [AdminCategoryController::class, 'store']);
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update']);
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy']);
});
