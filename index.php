<?php
// =============================================================
// index.php - Ana sayfa
// Sayaçları, filtre formunu ve görev listesini gösterir.
// =============================================================
require_once 'functions.php';

// Filtre değerleri adres çubuğundan (GET) okunur. Örn: index.php?durum=Bekliyor&ders=Fizik
$secilenDurum = trim($_GET['durum'] ?? '');
$secilenDers = trim($_GET['ders'] ?? '');

// Listede olmayan bir değer gelirse filtreyi yok say
if (!in_array($secilenDurum, DURUMLAR, true)) {
    $secilenDurum = '';
}
if (!in_array($secilenDers, DERSLER, true)) {
    $secilenDers = '';
}

$tumGorevler = gorevleriGetir();
$sayilar = gorevSayilariniHesapla($tumGorevler);

// Filtreyi uygula ve en yeni görev en üstte olacak şekilde ters çevir
$gorevler = gorevleriFiltrele($tumGorevler, $secilenDurum, $secilenDers);
$gorevler = array_reverse($gorevler);

// Ekleme/silme sonrası gelen tek seferlik mesaj
$flash = flashMesajGetir();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenci Görev Takip Sistemi</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="ust-bolum">
        <div class="kapsayici">
            <h1>Öğrenci Görev Takip Sistemi</h1>
            <p class="aciklama-metni">Derslerine ait ödev ve görevleri tek yerden ekle, filtrele ve takip et.</p>
            <nav class="menu">
                <a href="index.php" class="aktif">Ana Sayfa</a>
                <a href="ekle.php">Yeni Görev Ekle</a>
            </nav>
        </div>
    </header>

    <main class="kapsayici">
        <?php if ($flash !== null): ?>
            <div class="mesaj mesaj-<?= e($flash['tur']) ?>"><?= e($flash['mesaj']) ?></div>
        <?php endif; ?>

        <!-- Sayaçlar -->
        <section class="sayaclar">
            <div class="sayac"><span class="sayi"><?= e($sayilar['toplam']) ?></span>Toplam</div>
            <div class="sayac sayac-bekliyor"><span class="sayi"><?= e($sayilar['bekliyor']) ?></span>Bekliyor</div>
            <div class="sayac sayac-devam"><span class="sayi"><?= e($sayilar['devam']) ?></span>Devam Ediyor</div>
            <div class="sayac sayac-tamamlandi"><span class="sayi"><?= e($sayilar['tamamlandi']) ?></span>Tamamlandı</div>
            <div class="sayac sayac-geciken"><span class="sayi"><?= e($sayilar['geciken']) ?></span>Geciken</div>
        </section>

        <!-- Filtre formu (GET) -->
        <form method="get" action="index.php" class="filtre">
            <label>
                Durum
                <select name="durum">
                    <option value="">Tümü</option>
                    <?php foreach (DURUMLAR as $durum): ?>
                        <option value="<?= e($durum) ?>" <?= $durum === $secilenDurum ? 'selected' : '' ?>><?= e($durum) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Ders
                <select name="ders">
                    <option value="">Tümü</option>
                    <?php foreach (DERSLER as $ders): ?>
                        <option value="<?= e($ders) ?>" <?= $ders === $secilenDers ? 'selected' : '' ?>><?= e($ders) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit" class="buton">Filtrele</button>
            <?php if ($secilenDurum !== '' || $secilenDers !== ''): ?>
                <a href="index.php" class="filtre-temizle">Filtreyi temizle</a>
            <?php endif; ?>
        </form>

        <!-- Görev listesi -->
        <?php if (count($gorevler) === 0): ?>
            <p class="bos-liste">Kayıt bulunamadı.</p>
        <?php else: ?>
            <section class="gorev-listesi">
                <?php foreach ($gorevler as $gorev): ?>
                    <article class="gorev-karti <?= gecikmisMi($gorev) ? 'gecikmis' : '' ?>">
                        <div class="kart-ust">
                            <h2><?= e($gorev['baslik']) ?></h2>
                            <form method="post" action="sil.php" class="sil-formu"
                                  onsubmit="return confirm('Bu görevi silmek istediğine emin misin?');">
                                <input type="hidden" name="id" value="<?= e($gorev['id']) ?>">
                                <button type="submit" class="buton buton-sil">Sil</button>
                            </form>
                        </div>
                        <p class="ders-adi"><?= e($gorev['ders']) ?></p>

                        <div class="etiketler">
                            <span class="etiket <?= etiketSinifi($gorev['durum']) ?>"><?= e($gorev['durum']) ?></span>
                            <span class="etiket <?= etiketSinifi($gorev['oncelik']) ?>"><?= e($gorev['oncelik']) ?> öncelik</span>
                            <?php if (gecikmisMi($gorev)): ?>
                                <span class="etiket etiket-gecikti">Gecikti</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($gorev['aciklama'] !== ''): ?>
                            <p class="gorev-aciklama"><?= e($gorev['aciklama']) ?></p>
                        <?php endif; ?>

                        <p class="son-tarih">Son tarih: <strong><?= e(date('d.m.Y', strtotime($gorev['son_tarih']))) ?></strong></p>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>

    <footer class="alt-bolum">Öğrenci Görev Takip Sistemi &middot; PHP + HTML + CSS</footer>
</body>
</html>
