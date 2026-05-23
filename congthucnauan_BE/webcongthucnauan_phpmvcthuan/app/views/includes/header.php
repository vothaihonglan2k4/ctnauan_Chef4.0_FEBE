<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? $data['title'] . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/public/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- End of CSS links -->
</head>
<body>
    <?php require_once APP_ROOT . '/views/includes/navbar.php'; ?>
    
    <?php if(isset($_SESSION['success_msg'])): ?>
        <div class="container mt-4">
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error_msg'])): ?>
        <div class="container mt-4">
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>
