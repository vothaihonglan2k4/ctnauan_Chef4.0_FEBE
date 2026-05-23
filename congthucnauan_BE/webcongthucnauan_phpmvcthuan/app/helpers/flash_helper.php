<?php
/**
 * Flash message helper
 * 
 * Format: flash('register_success', 'You are now registered', 'alert alert-success');
 * EXAMPLE - flash('register_success', 'You are now registered');
 * DISPLAY IN VIEW - <?php echo flash('register_success'); ?>
 */
function flash($name = '', $message = '', $class = 'alert alert-success alert-dismissible fade show'){
    if(!empty($name)){
        // No message, create it
        if(!empty($message) && empty($_SESSION[$name])){
            if(!empty($_SESSION[$name])){
                unset($_SESSION[$name]);
            }

            if(!empty($_SESSION[$name . '_class'])){
                unset($_SESSION[$name . '_class']);
            }

            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        }
        // Message exists, display it
        elseif(!empty($_SESSION[$name]) && empty($message)){
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
            echo '<div class="'.$class.'" role="alert">';
            echo $_SESSION[$name];
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}

/**
 * Flash message for admin panel
 */
function adminFlash($name = '', $message = '', $class = 'alert alert-success alert-dismissible fade show'){
    flash('admin_' . $name, $message, $class);
} 