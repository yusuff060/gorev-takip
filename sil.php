<?php
// =============================================================
// sil.php - Görev silme
// Sadece POST isteği kabul eder. Adres çubuğundan (GET) açılırsa
// hiçbir şey silmeden ana sayfaya geri döner.
// =============================================================
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flashMesajAyarla('hata', 'Silme işlemi yalnızca liste üzerindeki "Sil" butonu ile yapılabilir.');
    header('Location: index.php');
    exit;
}

// Gizli alandan gelen id'yi sayıya çevir (sayı değilse 0 olur, 0 id'li görev yoktur)
$gelenId = $_POST['id'] ?? '';
$id = is_numeric($gelenId) ? (int) $gelenId : 0;

if (gorevSil($id)) {
    flashMesajAyarla('basari', 'Görev başarıyla silindi.');
} else {
    flashMesajAyarla('hata', 'Silinmek istenen görev bulunamadı.');
}

// Post/Redirect/Get: ana sayfaya yönlendir
header('Location: index.php');
exit;
