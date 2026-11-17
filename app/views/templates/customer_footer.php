</main><!-- /.sc-consumer-main -->

<!-- ── Consumer Footer ───────────────────────────────────── -->
<footer class="sc-consumer-footer">
    <div class="container-xl">
        <div class="row g-4 align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 mb-2">
                    <span class="fw-bold text-dark" style="font-family:'Outfit',sans-serif;font-size:1rem;">Skin<span style="color:var(--primary-color);">Check</span></span>
                    <span class="badge sc-badge-gold px-2 py-1 rounded-pill" style="font-size:0.7rem;">Clinical Health System</span>
                </div>
                <p class="mb-0 text-muted" style="font-size:0.78rem; line-height:1.5;">
                    Platform analisis kesehatan kulit mandiri. Bukan pengganti diagnosa medis dokter Sp.D.V.E.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <div class="d-flex justify-content-center justify-content-md-end gap-3 mb-2 small">
                    <a href="<?php echo URLROOT; ?>/customer/dashboard" class="sc-footer-link">Dashboard</a>
                    <a href="<?php echo URLROOT; ?>/scan/faceScan" class="sc-footer-link">Face Scan</a>
                    <a href="<?php echo URLROOT; ?>/sunscreen/check" class="sc-footer-link">Sunscreen Check</a>
                    <a href="<?php echo URLROOT; ?>/customer/aiAssistant" class="sc-footer-link">Konsultasi</a>
                </div>
                <p class="mb-0 text-muted" style="font-size:0.75rem;">
                    &copy; <?php echo date('Y'); ?> SkinCheck Health Technologies. Hak cipta dilindungi undang-undang.
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5.3 Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<!-- Main JS with cache buster -->
<script src="<?php echo URLROOT; ?>/public/js/main.js?v=<?php echo time(); ?>"></script>

<?php if (isset($extraScript)) echo $extraScript; ?>

<!-- Global Floating Toast Notification System -->
<?php require APPROOT . '/views/templates/toast.php'; ?>

<!-- SkinCheck Chatbot Widget (Hidden on dedicated AI Assistant page) -->
<?php 
$currentUrl = $_GET['url'] ?? '';
if (stripos($currentUrl, 'aiAssistant') === false && stripos($currentUrl, 'assistant') === false) {
    require APPROOT . '/views/templates/chatbot_widget.php';
}
?>
</body>
</html>
