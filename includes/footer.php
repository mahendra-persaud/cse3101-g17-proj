<?php
// session stuff IS handled by header... hopefully
?>
</div> <!-- end main content wrapper -->
<footer style="clear:both;padding:12px;border-top:1px solid #eee;margin-top:16px;text-align:center">
    <small>&copy; <?php echo date('Y'); ?> Primary School Management System</small>
</footer>
<?php if (!isset($no_container) || !$no_container): ?>
</div> <!-- .container -->
<?php endif; ?>
</body>
</html>