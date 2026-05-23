<?php
// Enable debugging mode (remove this in production)
define('DEBUG_MODE', true);

// Load Config
require_once '../app/config/config.php';

// Load Helpers
require_once '../app/helpers/url_helper.php';
require_once '../app/helpers/session_helper.php';

// Create debug_helper.php if it doesn't exist
if(!file_exists('../app/helpers/debug_helper.php')) {
    // Create basic logging function
    function debug_to_file($message, $data = null) {
        $log_file = '../app/logs/debug.log';
        
        // Create logs directory if it doesn't exist
        if(!is_dir(dirname($log_file))) {
            mkdir(dirname($log_file), 0777, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $log_message = "[$timestamp] $message";
        
        if($data !== null) {
            $log_message .= "\n" . print_r($data, true);
        }
        
        $log_message .= "\n" . str_repeat('-', 50) . "\n";
        
        // Append to log file
        file_put_contents($log_file, $log_message, FILE_APPEND);
    }
    
    function test_db_connection() {
        // placeholder
    }
    
    function db_debug($query, $params = []) {
        // placeholder
    }
} else {
    require_once '../app/helpers/debug_helper.php';
}

// Error handling
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    debug_to_file("PHP Error: [$errno] $errstr", [
        'file' => $errfile,
        'line' => $errline
    ]);
    
    if(DEBUG_MODE) {
        echo "<div style='background-color:#f8d7da;color:#721c24;padding:10px;margin:10px;border:1px solid #f5c6cb;'>";
        echo "<h3>Error Detected</h3>";
        echo "<p><strong>Message:</strong> $errstr</p>";
        echo "<p><strong>File:</strong> $errfile</p>";
        echo "<p><strong>Line:</strong> $errline</p>";
        echo "</div>";
    }
    
    return true;
});

// Autoload Core Libraries with error checking
spl_autoload_register(function($className) {
    $file = '../app/libraries/' . $className . '.php';
    
    if(file_exists($file)) {
        require_once $file;
    } else {
        debug_to_file("Autoloader Error: File not found", [
            'class' => $className,
            'expected_path' => $file
        ]);
        
        if(DEBUG_MODE) {
            echo "<div style='background-color:#f8d7da;color:#721c24;padding:10px;margin:10px;border:1px solid #f5c6cb;'>";
            echo "<h3>Class Loading Error</h3>";
            echo "<p>Could not find class file: <strong>$file</strong></p>";
            echo "<p>Please make sure the file exists and the class name matches the file name.</p>";
            echo "</div>";
        }
        
        // Check if class has a file with different casing
        $dir = '../app/libraries/';
        $files = scandir($dir);
        
        foreach($files as $existingFile) {
            if(strtolower($existingFile) == strtolower($className . '.php')) {
                echo "<p>Did you mean: <strong>{$existingFile}</strong>?</p>";
                break;
            }
        }
    }
});

try {
    // Init Core Library
    $init = new Core;
} catch (Exception $e) {
    debug_to_file("Core Initialization Error: " . $e->getMessage(), [
        'trace' => $e->getTraceAsString()
    ]);
    
    if(DEBUG_MODE) {
        echo "<div style='background-color:#f8d7da;color:#721c24;padding:10px;margin:10px;border:1px solid #f5c6cb;'>";
        echo "<h3>Application Error</h3>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "</div>";
    }
}
?>
