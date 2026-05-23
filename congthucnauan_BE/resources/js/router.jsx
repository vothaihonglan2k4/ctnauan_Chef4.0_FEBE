import { createBrowserRouter } from 'react-router-dom';
import { AuthProvider } from './contexts/AuthContext';
import Layout from './Components/Layout/Layout';
import HomePage from './Pages/HomePage';
import AboutPage from './Pages/AboutPage';
import ContactPage from './Pages/ContactPage';
import ForumPage from './Pages/ForumPage';
import ForumDetailPage from './Pages/ForumDetailPage';
import ForumCreatePostPage from './Pages/ForumCreatePostPage';
import ForumTagPage from './Pages/ForumTagPage';
import RecipesPage from './Pages/RecipesPage';
import RecipeDetailPage from './Pages/RecipeDetailPage';
import AddRecipePage from './Pages/AddRecipePage';
import EditRecipePage from './Pages/EditRecipePage';
import ProfilePage from './Pages/ProfilePage';
import ChangePasswordPage from './Pages/ChangePasswordPage';
import PaymentsPage from './Pages/PaymentsPage';
import CheckoutPage from './Pages/CheckoutPage';
import PaymentSuccessPage from './Pages/PaymentSuccessPage';
import PaymentCancelPage from './Pages/PaymentCancelPage';
import VNPayReturnPage from './Pages/VNPayReturnPage';
import CoursesPage from './Pages/CoursesPage';
import CourseDetailPage from './Pages/CourseDetailPage';
import CourseLearnPage from './Pages/CourseLearnPage';
import MyCoursesPage from './Pages/MyCoursesPage';
import CourseCheckoutPage from './Pages/CourseCheckoutPage';
import CourseSchedulePage from './Pages/CourseSchedulePage';
import LoginPage from './Pages/Auth/LoginPage';
import RegisterPage from './Pages/Auth/RegisterPage';
import AdminLayout from './Components/Layout/AdminLayout';
import AdminDashboardPage from './Pages/Admin/AdminDashboardPage';
import AdminUsersPage from './Pages/Admin/AdminUsersPage';
import AdminCategoriesPage from './Pages/Admin/AdminCategoriesPage';
import AdminClassroomsPage from './Pages/Admin/AdminClassroomsPage';
import AdminCoursesPage from './Pages/Admin/AdminCoursesPage';
import AdminPaymentsPage from './Pages/Admin/AdminPaymentsPage';
import AdminReportsPage from './Pages/Admin/AdminReportsPage';
import ManagerLayout from './Components/Layout/ManagerLayout';
import ManagerDashboardPage from './Pages/Manager/ManagerDashboardPage';
import ManagerRecipesPage from './Pages/Manager/ManagerRecipesPage';
import ManagerCoursesPage from './Pages/Manager/ManagerCoursesPage';
import ManagerContactsPage from './Pages/Manager/ManagerContactsPage';
import ManagerReportsPage from './Pages/Manager/ManagerReportsPage';

const router = createBrowserRouter([
    {
        path: '/',
        element: (
            <AuthProvider>
                <Layout />
            </AuthProvider>
        ),
        children: [
            {
                index: true,
                element: <HomePage />
            },
            {
                path: 'about',
                element: <AboutPage />
            },
            {
                path: 'contact',
                element: <ContactPage />
            },
            {
                path: 'forum',
                element: <ForumPage />
            },
            {
                path: 'forum/create',
                element: <ForumCreatePostPage />
            },
            {
                path: 'forum/:id',
                element: <ForumDetailPage />
            },
            {
                path: 'forum/tag/:tagName',
                element: <ForumTagPage />
            },
            {
                path: 'recipes',
                element: <RecipesPage />
            },
            {
                path: 'recipes/:id',
                element: <RecipeDetailPage />
            },
            {
                path: 'recipes/add',
                element: <AddRecipePage />
            },
            {
                path: 'recipes/edit/:id',
                element: <EditRecipePage />
            },
            {
                path: 'profile',
                element: <ProfilePage />
            },
            {
                path: 'change-password',
                element: <ChangePasswordPage />
            },
            {
                path: 'payments',
                element: <PaymentsPage />
            },
            {
                path: 'payments/checkout',
                element: <CheckoutPage />
            },
            {
                path: 'payments/success',
                element: <PaymentSuccessPage />
            },
            {
                path: 'payments/cancel',
                element: <PaymentCancelPage />
            },
            {
                path: 'payments/vnpay-return',
                element: <VNPayReturnPage />
            },
            {
                path: 'courses',
                element: <CoursesPage />
            },
            {
                path: 'courses/:id',
                element: <CourseDetailPage />
            },
            {
                path: 'courses/learn/:courseId/:lessonId?',
                element: <CourseLearnPage />
            },
            {
                path: 'courses/:id/checkout',
                element: <CourseCheckoutPage />
            },
            {
                path: 'my-courses',
                element: <MyCoursesPage />
            },
            {
                path: 'my-courses/schedule',
                element: <CourseSchedulePage />
            },
            {
                path: 'login',
                element: <LoginPage />
            },
            {
                path: 'register',
                element: <RegisterPage />
            },
        ]
    },
    // Admin routes
    {
        path: '/admin',
        element: (
            <AuthProvider>
                <AdminLayout />
            </AuthProvider>
        ),
        children: [
            {
                index: true,
                element: <AdminDashboardPage />
            },
            {
                path: 'users',
                element: <AdminUsersPage />
            },
            {
                path: 'categories',
                element: <AdminCategoriesPage />
            },
            {
                path: 'classrooms',
                element: <AdminClassroomsPage />
            },
            {
                path: 'courses',
                element: <AdminCoursesPage />
            },
            {
                path: 'payments',
                element: <AdminPaymentsPage />
            },
            {
                path: 'reports',
                element: <AdminReportsPage />
            },
        ]
    },
    // Manager routes
    {
        path: '/manager',
        element: (
            <AuthProvider>
                <ManagerLayout />
            </AuthProvider>
        ),
        children: [
            {
                index: true,
                element: <ManagerDashboardPage />
            },
            {
                path: 'recipes',
                element: <ManagerRecipesPage />
            },
            {
                path: 'courses',
                element: <ManagerCoursesPage />
            },
            {
                path: 'contacts',
                element: <ManagerContactsPage />
            },
            {
                path: 'reports',
                element: <ManagerReportsPage />
            },
        ]
    }
]);

export default router;