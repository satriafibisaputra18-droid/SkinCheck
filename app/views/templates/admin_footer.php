                <!-- Admin Footer -->
                <footer class="mt-auto pt-4 border-top text-muted small bg-transparent text-start py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="mb-0">&copy; <?php echo date('Y'); ?> SkinCheck Administration Portal.</p>
                        <div>
                            <span class="me-2 text-success"><i class="fa-solid fa-circle text-success me-1"></i>Server: Online</span>
                            <span>PHP MVC Core</span>
                        </div>
                    </div>
                </footer>

            </div><!-- End Main Content -->
        </div><!-- End Row -->
    </div><!-- End Container -->

    <!-- Bootstrap 5.3 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- Main Application Scripts -->
    <script src="<?php echo URLROOT; ?>/public/js/main.js"></script>

    <!-- Global Floating Toast Notification System -->
    <?php require APPROOT . '/views/templates/toast.php'; ?>

    <!-- SkinCheck AI Chatbot Widget (Node.js & Gemini AI Studio) -->
    <?php require APPROOT . '/views/templates/chatbot_widget.php'; ?>
</body>
</html>
