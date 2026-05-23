<?php

// API Loader - Load các class cần thiết cho API
class ApiLoader {
    
    private static $loaded = false;
    
    public static function init() {
        if (self::$loaded) {
            return;
        }
        
        // Base path - từ public/api/helpers/ lên root project  
        $basePath = dirname(dirname(dirname(__DIR__)));
        
        // Load Config first
        require_once $basePath . '/app/config/config.php';
        
        // Load Core Classes
        require_once $basePath . '/app/core/Database.php';
        
        // Load Models
        $models = [
            'User', 'Recipe', 'Category', 'Course', 
            'CourseLesson', 'Rating', 'Payment', 'Contact'
        ];
        
        foreach ($models as $model) {
            $file = $basePath . '/app/models/' . $model . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
        
        self::$loaded = true;
    }
    
    // Helper method để test xem classes có loaded không
    public static function checkClasses() {
        $classes = ['Database', 'User', 'Recipe', 'Category', 'Course', 'Rating'];
        $result = [];
        
        foreach ($classes as $class) {
            $result[$class] = class_exists($class) ? 'OK' : 'MISSING';
        }
        
        return $result;
    }
}

// Auto init khi file này được include
ApiLoader::init(); 