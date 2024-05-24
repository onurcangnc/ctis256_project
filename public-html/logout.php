<?php
session_start();
session_unset();//verileri temizleme
session_destroy();//sesiondan çıkma veya yok etmk
header("Location: login.php");
exit();
?>
