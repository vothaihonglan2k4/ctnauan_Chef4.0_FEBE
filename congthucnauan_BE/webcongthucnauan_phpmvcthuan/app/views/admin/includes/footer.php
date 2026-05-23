            </div><!-- End of main-content -->
            
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <span>© <?php echo date('Y'); ?> Hệ thống Công thức nấu ăn</span>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span>Phiên bản 1.0</span>
                        </div>
                    </div>
                </div>
            </footer>
            
        </div><!-- End of main-content-wrapper -->
    </div><!-- End of wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Enhanced flash message handling
        $(document).ready(function() {
            // Auto-close alerts after 5 seconds
            setTimeout(function() {
                $(".alert").alert('close');
            }, 5000);

            // Confirm delete with SweetAlert2 (for both old links and new form buttons)
            $('.btn-delete, .btn-delete-form').on('click', function(e) {
                e.preventDefault();
                
                // Get URL and classroom info
                const url = $(this).attr('href') || $(this).closest('form').attr('action');
                const classroomName = $(this).data('classroom-name') || 'phòng học này';
                const courseCount = $(this).data('course-count') || 0;
                
                console.log('Delete button clicked:', url);
                console.log('Classroom name:', classroomName);
                console.log('Course count:', courseCount);
                
                // Check if classroom has courses
                if (courseCount > 0) {
                    Swal.fire({
                        title: 'Không thể xóa!',
                        text: `Phòng học "${classroomName}" đang có ${courseCount} khóa học sử dụng. Hãy xóa hoặc chuyển các khóa học trước.`,
                        icon: 'error',
                        confirmButtonText: 'Đã hiểu'
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Bạn có chắc chắn?',
                    text: `Xóa phòng học "${classroomName}"? Dữ liệu đã xóa không thể khôi phục!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa ngay',
                    cancelButtonText: 'Hủy bỏ'
                }).then((result) => {
                    console.log('SweetAlert result:', result);
                    if (result.isConfirmed) {
                        // If it's a form button, submit the form directly
                        if ($(this).hasClass('btn-delete-form')) {
                            console.log('Submitting existing form');
                            $(this).closest('form')[0].submit();
                        } else {
                            console.log('Creating form for POST request to:', url);
                            // Create a temporary form to send POST request
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = url;
                            form.style.display = 'none';
                            document.body.appendChild(form);
                            console.log('Form created and submitted');
                            form.submit();
                        }
                    } else {
                        console.log('Delete cancelled by user');
                    }
                });
            });
            
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            
            // Toggle sidebar on mobile
            const sidebarCollapse = document.getElementById('sidebarCollapse');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.overlay');
            const mainContentWrapper = document.querySelector('.main-content-wrapper');
            
            if (sidebarCollapse) {
                sidebarCollapse.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                });
            }
            
            // Close sidebar when clicking on overlay
            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });
            }
            
            // Sidebar active class based on URL
            const currentLocation = window.location.pathname;
            $('.sidebar a').each(function() {
                const linkPath = $(this).attr('href');
                if (currentLocation.includes(linkPath) && linkPath !== '<?php echo URL_ROOT; ?>/admin') {
                    $(this).addClass('active');
                } else if (currentLocation === '<?php echo URL_ROOT; ?>/admin' && linkPath === '<?php echo URL_ROOT; ?>/admin') {
                    $(this).addClass('active');
                }
            });
            
            // Image preview on file selection
            $('input[type="file"]').on('change', function(e) {
                const file = e.target.files[0];
                const imgPreview = $(this).closest('form').find('img#image-preview');
                
                if (file && imgPreview.length > 0) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imgPreview.attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
    
    <!-- Cloudflare Scripts will be injected here -->
</body>
</html> 