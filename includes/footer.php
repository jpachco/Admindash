<?php if (!strpos($_SERVER['REQUEST_URI'], 'login.php')): ?>
        </main>
        
        <!-- Footer -->
        <footer class="footer">
            <p>© <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Todos los derechos reservados.</p>
            <p>Versión <?php echo APP_VERSION; ?></p>
        </footer>
    </div>
</div>
<?php endif; ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo APP_URL; ?>/assets/js/main.js"></script>
    
    <?php if (isset($customJS)): ?>
    <script>
        <?php echo $customJS; ?>
    </script>
    <?php endif; ?>
</body>
</html>