<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?php echo URL_ROOT; ?>">
            <i class="fas fa-utensils me-2"></i>Công Thức Nấu Ăn
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_ROOT; ?>">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_ROOT; ?>/recipes">Công thức</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_ROOT; ?>/courses">Học nấu ăn online</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_ROOT; ?>/home/about">Giới thiệu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_ROOT; ?>/home/contact">Liên hệ</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="moreDropdown" role="button" data-bs-toggle="dropdown">
                        Xem thêm
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/forum">Diễn đàn</a></li>
                    </ul>
                </li>
            </ul>
            
            <form class="d-flex me-2" action="<?php echo URL_ROOT; ?>/recipes/search" method="GET">
                <input class="form-control me-2" name="term" type="search" placeholder="Tìm công thức" required>
                <button class="btn btn-outline-light" type="submit">Tìm</button>
            </form>
            
            <ul class="navbar-nav">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($_SESSION['user_role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo URL_ROOT; ?>/admin">
                                <i class="fas fa-cog me-1"></i>Quản trị
                            </a>
                        </li>
                    <?php elseif($_SESSION['user_role'] == 'manager'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo URL_ROOT; ?>/manager">
                                <i class="fas fa-user-tie me-1"></i>Quản lý
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo $_SESSION['user_name']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/users/profile">Hồ sơ</a></li>
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/recipes/add">Thêm công thức</a></li>
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/payments">Thanh toán</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/users/logout">Đăng xuất</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URL_ROOT; ?>/users/login">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URL_ROOT; ?>/users/register">Đăng ký</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
