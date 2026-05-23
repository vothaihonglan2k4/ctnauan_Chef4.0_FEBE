<?php
class Controller {
    // Load model
    public function model($model) {
        require_once 'app/models/' . $model . '.php';
        return new $model();
    }

    // Load view
    public function view($view, $data = []) {
        if(file_exists('app/views/' . $view . '.php')) {
            require_once 'app/views/' . $view . '.php';
        } else {
            die('View does not exist');
        }
    }

    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Check if user is admin
    public function isAdmin() {
        return (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin');
    }

    // Redirect to specific page
    public function redirect($page) {
        header('location: ' . URL_ROOT . '/' . $page);
    }
}
?>
