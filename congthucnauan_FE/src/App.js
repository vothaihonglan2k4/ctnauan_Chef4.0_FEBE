import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { AuthProvider } from './contexts/AuthContext';

// Layout
import Layout from './Components/Layout/Layout';
import AdminLayout from './Components/Layout/AdminLayout';
import ManagerLayout from './Components/Layout/ManagerLayout';

// Pages - Main
import HomePage from './Pages/HomePage';
import AboutPage from './Pages/AboutPage';
import ContactPage from './Pages/ContactPage';

// Pages - Forum
import ForumPage from './Pages/ForumPage';
import ForumDetailPage from './Pages/ForumDetailPage';
import ForumCreatePostPage from './Pages/ForumCreatePostPage';
import ForumTagPage from './Pages/ForumTagPage';

// Pages - Recipe
import RecipesPage from './Pages/RecipesPage';
import RecipeDetailPage from './Pages/RecipeDetailPage';
import AddRecipePage from './Pages/AddRecipePage';
import EditRecipePage from './Pages/EditRecipePage';

// Pages - User
import ProfilePage from './Pages/ProfilePage';
import ChangePasswordPage from './Pages/ChangePasswordPage';

// Pages - Payment
import PaymentsPage from './Pages/PaymentsPage';
import CheckoutPage from './Pages/CheckoutPage';
import PaymentSuccessPage from './Pages/PaymentSuccessPage';
import PaymentCancelPage from './Pages/PaymentCancelPage';
import VNPayReturnPage from './Pages/VNPayReturnPage';
import MomoReturnPage from './Pages/MomoReturnPage';
import SePayReturnPage from './Pages/SePayReturnPage';

// Pages - Course
import CoursesPage from './Pages/CoursesPage';
import CourseDetailPage from './Pages/CourseDetailPage';
import CourseLearnPage from './Pages/CourseLearnPage';
import MyCoursesPage from './Pages/MyCoursesPage';
import CourseCheckoutPage from './Pages/CourseCheckoutPage';
import CourseSchedulePage from './Pages/CourseSchedulePage';

// Pages - Auth
import LoginPage from './Pages/Auth/LoginPage';
import RegisterPage from './Pages/Auth/RegisterPage';

// Pages - Admin
import AdminDashboardPage from './Pages/Admin/AdminDashboardPage';
import AdminUsersPage from './Pages/Admin/AdminUsersPage';
import AdminCategoriesPage from './Pages/Admin/AdminCategoriesPage';
import AdminClassroomsPage from './Pages/Admin/AdminClassroomsPage';
import AdminCoursesPage from './Pages/Admin/AdminCoursesPage';
import AdminPaymentsPage from './Pages/Admin/AdminPaymentsPage';
import AdminReportsPage from './Pages/Admin/AdminReportsPage';
import AdminPermissionsPage from './Pages/Admin/AdminPermissionsPage';
import AdminContactsPage from './Pages/Admin/AdminContactsPage';
import AdminManageRecipesPage from './Pages/Admin/AdminManageRecipesPage';
import AdminManageCommentsPage from './Pages/Admin/AdminManageCommentsPage';

// Pages - Manager
import ManagerDashboardPage from './Pages/Manager/ManagerDashboardPage';
import ManagerRecipesPage from './Pages/Manager/ManagerRecipesPage';
import ManagerCoursesPage from './Pages/Manager/ManagerCoursesPage';
import ManagerContactsPage from './Pages/Manager/ManagerContactsPage';
import ManagerReportsPage from './Pages/Manager/ManagerReportsPage';
import ManagerUsersPage from './Pages/Manager/ManagerUsersPage';
import ManagerCategoriesPage from './Pages/Manager/ManagerCategoriesPage';
import ManagerLessonManagePage from './Pages/Manager/ManagerLessonManagePage';

function App() {
  return (
    // 2.1) BrowserRouter: bá»c toÃ n bá»™ app Ä‘á»ƒ kÃ­ch hoáº¡t routing
    <BrowserRouter>
      <AuthProvider>
        {/* 2.2) Routes: chá»©a táº¥t cáº£ cÃ¡c Route, chá»‰ render Route khá»›p vá»›i URL */}
        <Routes>

          {/* ===== MAIN ROUTES (cÃ³ Navbar + Footer) ===== */}
          {/* Route cha dÃ¹ng Layout (Navbar + Footer + <Outlet/>) */}
          <Route path="/" element={<Layout />}>
            {/* index: tÆ°Æ¡ng Ä‘Æ°Æ¡ng exact - trang chá»§ "/" */}
            <Route index element={<HomePage />} />

            {/* path: Ã¡nh xáº¡ URL -> Component */}
            <Route path="about" element={<AboutPage />} />
            <Route path="contact" element={<ContactPage />} />

            {/* Forum routes */}
            <Route path="forum" element={<ForumPage />} />
            <Route path="forum/create" element={<ForumCreatePostPage />} />
            {/* 2.6) Match - :id lÃ  tham sá»‘ Ä‘á»™ng láº¥y qua useParams() */}
            <Route path="forum/:id" element={<ForumDetailPage />} />
            <Route path="forum/tag/:tagName" element={<ForumTagPage />} />

            {/* Recipe routes */}
            <Route path="recipes" element={<RecipesPage />} />
            <Route path="recipes/search" element={<RecipesPage />} />
            <Route path="recipes/add" element={<AddRecipePage />} />
            <Route path="recipes/:id" element={<RecipeDetailPage />} />
            <Route path="recipes/edit/:id" element={<EditRecipePage />} />

            {/* User routes */}
            <Route path="profile" element={<ProfilePage />} />
            <Route path="change-password" element={<ChangePasswordPage />} />

            {/* Payment routes */}
            <Route path="payments" element={<PaymentsPage />} />
            <Route path="payments/checkout" element={<CheckoutPage />} />
            <Route path="payments/success" element={<PaymentSuccessPage />} />
            <Route path="payments/cancel" element={<PaymentCancelPage />} />
            <Route path="payments/vnpay-return" element={<VNPayReturnPage />} />
            <Route path="payment/success" element={<MomoReturnPage />} />
            <Route path="payments/momo-return" element={<MomoReturnPage />} />
            <Route path="payments/sepay-return" element={<SePayReturnPage />} />

            {/* Course routes */}
            <Route path="courses" element={<CoursesPage />} />
            <Route path="courses/:id" element={<CourseDetailPage />} />
            {/* :lessonId? = tham sá»‘ tÃ¹y chá»n */}
            <Route path="courses/learn/:courseId/:lessonId" element={<CourseLearnPage />} />
            <Route path="courses/learn/:courseId" element={<CourseLearnPage />} />
            <Route path="courses/:id/checkout" element={<CourseCheckoutPage />} />
            <Route path="my-courses" element={<MyCoursesPage />} />
            <Route path="my-courses/schedule" element={<CourseSchedulePage />} />

            {/* Auth routes */}
            <Route path="login" element={<LoginPage />} />
            <Route path="register" element={<RegisterPage />} />
          </Route>

          {/* ===== ADMIN ROUTES (cÃ³ AdminLayout riÃªng) ===== */}
          <Route path="/admin" element={<AdminLayout />}>
            <Route index element={<AdminDashboardPage />} />
            <Route path="users" element={<AdminUsersPage />} />
            <Route path="categories" element={<AdminCategoriesPage />} />
            <Route path="classrooms" element={<AdminClassroomsPage />} />
            <Route path="courses" element={<AdminCoursesPage />} />
            <Route path="payments" element={<AdminPaymentsPage />} />
            <Route path="reports" element={<AdminReportsPage />} />
            <Route path="permissions" element={<AdminPermissionsPage />} />
            <Route path="contacts" element={<AdminContactsPage />} />
            <Route path="manageRecipes" element={<AdminManageRecipesPage />} />
            <Route path="manageComments" element={<AdminManageCommentsPage />} />
          </Route>

          {/* ===== MANAGER ROUTES (cÃ³ ManagerLayout riÃªng) ===== */}
          <Route path="/manager" element={<ManagerLayout />}>
            <Route index element={<ManagerDashboardPage />} />
            <Route path="recipes" element={<ManagerRecipesPage />} />
            <Route path="courses" element={<ManagerCoursesPage />} />
            <Route path="courses/:courseId/lessons" element={<ManagerLessonManagePage />} />
            <Route path="contacts" element={<ManagerContactsPage />} />
            <Route path="reports" element={<ManagerReportsPage />} />
            <Route path="users" element={<ManagerUsersPage />} />
            <Route path="categories" element={<ManagerCategoriesPage />} />
          </Route>

        </Routes>
      </AuthProvider>
    </BrowserRouter>
  );
}

export default App;


