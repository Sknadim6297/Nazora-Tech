      </main>
    </div>
  </div>

  <?php if (!empty($includeChartJs)) : ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <?php endif; ?>
  <?php if (!empty($includeEditor)) : ?>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
  <?php endif; ?>
  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/admin.js?v=<?php echo (int) (@filemtime(__DIR__ . '/../assets/js/admin.js') ?: time()); ?>"></script>
</body>
</html>
