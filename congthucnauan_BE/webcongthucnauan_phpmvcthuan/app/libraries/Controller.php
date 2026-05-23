<?php
/**
 * Base Controller
 * Loads the models and views
 */
class Controller {
    // Load model
    public function model($model) {
        // Require model file
        if(file_exists('../app/models/' . $model . '.php')) {
            require_once '../app/models/' . $model . '.php';
            
            // Instantiate model
            return new $model();
        } else {
            debug_to_file("Model not found", ['model' => $model]);
            die("Model $model not found");
        }
    }

    // Load view
    public function view($view, $data = []) {
        // Check for view file
        if(file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            debug_to_file("View not found", ['view' => $view]);
            die("View $view not found");
        }
    }
}
?>
