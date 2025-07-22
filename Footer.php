<?php
// classes/Footer.php
class Footer {
    public static function render() {
        ?>
  <footer style="background:#a390e4;color:#fff;padding:38px 0 18px 0;border-radius:36px 36px 0 0;margin-top:55px;font-size:15px;text-align:center;">
    <div>
      <p>&copy; <?= date('Y') ?> مكتبة الإلهام الساكن — جميع الحقوق محفوظة</p>
      <p style="margin-top:8px;font-size:13px;">تطوير ريم الوعيل</p>
    </div>
  </footer>
</body>
</html>
        <?php
    }
}
?>
