<footer class="app-footer text-center text-muted small py-4">
    <div class="container">
        &copy; <?= date('Y') ?> <?= e(config('app_name')) ?> &middot; Mini Social Networking Web Application &middot;
        Saint Michael College of Caraga
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>window.APP_MAX_UPLOAD = <?= (int) config('max_upload_bytes', 2097152) ?>;</script>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
