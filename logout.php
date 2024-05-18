<?php
// Başlamış olan session'ı sürdürmek için session'ı başlatma
session_start();

// Tüm session verilerini temizleme
session_unset();

// Session'ı yok etme
session_destroy();

// Kullanıcıyı giriş sayfasına yönlendirme
header("Location: login.php");
exit();
?>
