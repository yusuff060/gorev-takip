<?php
// =============================================================
// ekle.php - Yeni görev ekleme
// Sayfa ilk açıldığında (GET) boş form gösterilir.
// Form gönderildiğinde (POST) veriler doğrulanır:
//   - Hata varsa form, girilen değerlerle birlikte hatalarla tekrar gösterilir.
//   - Hata yoksa görev eklenir ve ana sayfaya yönlendirilir (Post/Redirect/Get).
// =============================================================
require_once 'functions.php';

// Formdaki alanlar ve başlangıç değerleri
$form = [
    'baslik' => '',
    'ders' => '',
    'aciklama' => '',
    'oncelik' => 'Orta',
    'durum' => 'Bekliyor',
    'son_tarih' => '',
];
$hatalar = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gelen her değeri al, metin değilse boş kabul et ve baştaki/sondaki boşlukları sil
    foreach ($form as $alan => $varsayilan) {
        $deger = $_POST[$alan] ?? '';
        if (!is_string($deger)) {
            $deger = '';
        }
        $form[$alan] = trim($deger);
    }

    $hatalar = gorevDogrula($form);

    if (count($hatalar) === 0) {
        gorevEkle($form);
        flashMesajAyarla('basari', 'Görev başarıyla eklendi.');
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Görev Ekle - Öğrenci Görev Takip Sistemi</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="ust-bolum">
        <div class="kapsayici">
            <h1>Öğrenci Görev Takip Sistemi</h1>
            <p class="aciklama-metni">Derslerine ait ödev ve görevleri tek yerden ekle, filtrele ve takip et.</p>
            <nav class="menu">
                <a href="index.php">Ana Sayfa</a>
                <a href="ekle.php" class="aktif">Yeni Görev Ekle</a>
            </nav>
        </div>
    </header>

    <main class="kapsayici">
        <h2 class="sayfa-basligi">Yeni Görev Ekle</h2>

        <?php if (count($hatalar) > 0): ?>
            <div class="mesaj mesaj-hata">
                <strong>Görev eklenemedi. Lütfen aşağıdaki hataları düzeltin:</strong>
                <ul>
                    <?php foreach ($hatalar as $hata): ?>
                        <li><?= e($hata) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="ekle.php" class="gorev-formu">
            <div class="alan <?= isset($hatalar['baslik']) ? 'hatali' : '' ?>">
                <label for="baslik">Görev başlığı *</label>
                <input type="text" id="baslik" name="baslik" value="<?= e($form['baslik']) ?>" placeholder="Örn: Türev alıştırmaları">
                <?php if (isset($hatalar['baslik'])): ?>
                    <p class="alan-hatasi"><?= e($hatalar['baslik']) ?></p>
                <?php endif; ?>
            </div>

            <div class="alan <?= isset($hatalar['ders']) ? 'hatali' : '' ?>">
                <label for="ders">Ders *</label>
                <select id="ders" name="ders">
                    <option value="">-- Ders seçin --</option>
                    <?php foreach (DERSLER as $ders): ?>
                        <option value="<?= e($ders) ?>" <?= $ders === $form['ders'] ? 'selected' : '' ?>><?= e($ders) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($hatalar['ders'])): ?>
                    <p class="alan-hatasi"><?= e($hatalar['ders']) ?></p>
                <?php endif; ?>
            </div>

            <div class="alan <?= isset($hatalar['aciklama']) ? 'hatali' : '' ?>">
                <label for="aciklama">Açıklama (isteğe bağlı, en fazla 500 karakter)</label>
                <textarea id="aciklama" name="aciklama" rows="4"><?= e($form['aciklama']) ?></textarea>
                <?php if (isset($hatalar['aciklama'])): ?>
                    <p class="alan-hatasi"><?= e($hatalar['aciklama']) ?></p>
                <?php endif; ?>
            </div>

            <div class="alan-grubu">
                <div class="alan <?= isset($hatalar['oncelik']) ? 'hatali' : '' ?>">
                    <label for="oncelik">Öncelik</label>
                    <select id="oncelik" name="oncelik">
                        <?php foreach (ONCELIKLER as $oncelik): ?>
                            <option value="<?= e($oncelik) ?>" <?= $oncelik === $form['oncelik'] ? 'selected' : '' ?>><?= e($oncelik) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($hatalar['oncelik'])): ?>
                        <p class="alan-hatasi"><?= e($hatalar['oncelik']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="alan <?= isset($hatalar['durum']) ? 'hatali' : '' ?>">
                    <label for="durum">Durum</label>
                    <select id="durum" name="durum">
                        <?php foreach (DURUMLAR as $durum): ?>
                            <option value="<?= e($durum) ?>" <?= $durum === $form['durum'] ? 'selected' : '' ?>><?= e($durum) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($hatalar['durum'])): ?>
                        <p class="alan-hatasi"><?= e($hatalar['durum']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="alan <?= isset($hatalar['son_tarih']) ? 'hatali' : '' ?>">
                    <label for="son_tarih">Son tarih *</label>
                    <input type="date" id="son_tarih" name="son_tarih" value="<?= e($form['son_tarih']) ?>">
                    <?php if (isset($hatalar['son_tarih'])): ?>
                        <p class="alan-hatasi"><?= e($hatalar['son_tarih']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-butonlari">
                <button type="submit" class="buton">Görevi Kaydet</button>
                <a href="index.php" class="buton buton-ikincil">Vazgeç</a>
            </div>
        </form>
    </main>

    <footer class="alt-bolum">Öğrenci Görev Takip Sistemi &middot; PHP + HTML + CSS</footer>
</body>
</html>
