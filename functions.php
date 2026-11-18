<?php
// =============================================================
// functions.php
// Bu dosya her sayfanın en başında require_once ile dahil edilir.
// İçinde: oturum başlatma, sabit listeler, örnek veriler ve
// tüm yardımcı fonksiyonlar bulunur.
// Veriye erişen kodların hepsi burada olduğu için ileride
// MySQL/PDO'ya geçmek istersek sadece bu dosyayı değiştirmemiz yeterli.
// =============================================================

// Tarih karşılaştırmaları için Türkiye saatini kullan
date_default_timezone_set('Europe/Istanbul');

// Oturumu başlat. Görevler veritabanı yerine $_SESSION içinde tutulur.
session_start();

// -------------------------------------------------------------
// Sabit listeler (formda, doğrulamada ve filtrede buradan okunur)
// Yeni bir ders eklemek için sadece bu diziye eklemek yeterlidir.
// -------------------------------------------------------------
const DERSLER = ['Matematik', 'Fizik', 'İnternet Programcılığı', 'Veritabanı', 'Diğer'];
const ONCELIKLER = ['Düşük', 'Orta', 'Yüksek'];
const DURUMLAR = ['Bekliyor', 'Devam Ediyor', 'Tamamlandı'];

// Oturumda hiç görev yoksa örnek görevleri yükle
ornekVerileriYukle();


// Ekrana basılacak her kullanıcı verisini güvenli hale getirir.
// Örneğin "<script>" yazısı "&lt;script&gt;" olur ve çalışmaz, sadece görünür.
function e($metin)
{
    return htmlspecialchars((string) $metin, ENT_QUOTES, 'UTF-8');
}

// Oturum boşsa 5 tane örnek görev ekler.
// Tarihler bugüne göre hesaplanır, böylece "Gecikti" etiketi her zaman görülebilir.
function ornekVerileriYukle()
{
    if (isset($_SESSION['gorevler'])) {
        return; // Zaten veri var, hiçbir şey yapma
    }

    $_SESSION['gorevler'] = [
        [
            'id' => 1,
            'baslik' => 'Türev konusu alıştırmaları',
            'ders' => 'Matematik',
            'aciklama' => 'Ders kitabındaki 3. bölümün sonundaki 20 soruyu çöz.',
            'oncelik' => 'Yüksek',
            'durum' => 'Devam Ediyor',
            'son_tarih' => date('Y-m-d', strtotime('+2 days')),
        ],
        [
            'id' => 2,
            'baslik' => 'Serbest düşme deney raporu',
            'ders' => 'Fizik',
            'aciklama' => 'Laboratuvarda alınan ölçümlerle grafik çiz ve raporu yaz.',
            'oncelik' => 'Orta',
            'durum' => 'Bekliyor',
            'son_tarih' => date('Y-m-d', strtotime('-3 days')),
        ],
        [
            'id' => 3,
            'baslik' => 'PHP form ödevi',
            'ders' => 'İnternet Programcılığı',
            'aciklama' => 'GET ve POST kullanan basit bir iletişim formu hazırla.',
            'oncelik' => 'Yüksek',
            'durum' => 'Bekliyor',
            'son_tarih' => date('Y-m-d', strtotime('+7 days')),
        ],
        [
            'id' => 4,
            'baslik' => 'ER diyagramı çizimi',
            'ders' => 'Veritabanı',
            'aciklama' => 'Kütüphane sistemi için varlık-ilişki diyagramı hazırla.',
            'oncelik' => 'Düşük',
            'durum' => 'Tamamlandı',
            'son_tarih' => date('Y-m-d', strtotime('-5 days')),
        ],
        [
            'id' => 5,
            'baslik' => 'Staj başvuru dilekçesi',
            'ders' => 'Diğer',
            'aciklama' => '',
            'oncelik' => 'Orta',
            'durum' => 'Devam Ediyor',
            'son_tarih' => date('Y-m-d', strtotime('-1 day')),
        ],
    ];

    // Yeni eklenecek görevin id'si buradan devam eder
    $_SESSION['son_id'] = 5;
}

// Tüm görevleri dizi olarak döndürür.
function gorevleriGetir()
{
    return $_SESSION['gorevler'];
}

// Yeni bir görevi listeye ekler. Her görev benzersiz, artan bir id alır.
function gorevEkle($veri)
{
    $_SESSION['son_id'] = $_SESSION['son_id'] + 1;

    $_SESSION['gorevler'][] = [
        'id' => $_SESSION['son_id'],
        'baslik' => $veri['baslik'],
        'ders' => $veri['ders'],
        'aciklama' => $veri['aciklama'],
        'oncelik' => $veri['oncelik'],
        'durum' => $veri['durum'],
        'son_tarih' => $veri['son_tarih'],
    ];
}

// Verilen id'ye sahip görevi siler.
// Silerse true, böyle bir görev yoksa false döndürür.
function gorevSil($id)
{
    foreach ($_SESSION['gorevler'] as $sira => $gorev) {
        if ($gorev['id'] === $id) {
            unset($_SESSION['gorevler'][$sira]);
            return true;
        }
    }
    return false;
}

// Tarih "YYYY-AA-GG" biçiminde ve takvimde gerçekten var mı? (örn. 2025-02-30 geçersiz)
function gecerliTarihMi($tarih)
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tarih)) {
        return false;
    }
    $parcalar = explode('-', $tarih);
    return checkdate((int) $parcalar[1], (int) $parcalar[2], (int) $parcalar[0]);
}

// Formdan gelen verileri kontrol eder.
// Hata yoksa boş dizi, hata varsa ['alan_adi' => 'Hata mesajı'] şeklinde dizi döndürür.
function gorevDogrula($veri)
{
    $hatalar = [];

    // Başlık: zorunlu, 3-100 karakter
    $baslikUzunlugu = mb_strlen($veri['baslik']);
    if ($veri['baslik'] === '') {
        $hatalar['baslik'] = 'Görev başlığı boş bırakılamaz.';
    } elseif ($baslikUzunlugu < 3 || $baslikUzunlugu > 100) {
        $hatalar['baslik'] = 'Görev başlığı 3 ile 100 karakter arasında olmalıdır.';
    }

    // Ders: zorunlu ve listede olmalı
    if ($veri['ders'] === '') {
        $hatalar['ders'] = 'Lütfen bir ders seçin.';
    } elseif (!in_array($veri['ders'], DERSLER, true)) {
        $hatalar['ders'] = 'Seçilen ders listede yok.';
    }

    // Açıklama: isteğe bağlı, en fazla 500 karakter
    if (mb_strlen($veri['aciklama']) > 500) {
        $hatalar['aciklama'] = 'Açıklama en fazla 500 karakter olabilir.';
    }

    // Öncelik ve durum: listede olmalı
    if (!in_array($veri['oncelik'], ONCELIKLER, true)) {
        $hatalar['oncelik'] = 'Lütfen geçerli bir öncelik seçin.';
    }
    if (!in_array($veri['durum'], DURUMLAR, true)) {
        $hatalar['durum'] = 'Lütfen geçerli bir durum seçin.';
    }

    // Son tarih: zorunlu ve geçerli bir tarih olmalı
    if ($veri['son_tarih'] === '') {
        $hatalar['son_tarih'] = 'Son tarih boş bırakılamaz.';
    } elseif (!gecerliTarihMi($veri['son_tarih'])) {
        $hatalar['son_tarih'] = 'Geçerli bir tarih girin (YYYY-AA-GG).';
    }

    return $hatalar;
}

// Son tarihi geçmiş ve henüz tamamlanmamış görevler "gecikmiş" sayılır.
// "Y-m-d" biçimindeki tarihler metin olarak da doğru karşılaştırılır.
function gecikmisMi($gorev)
{
    return $gorev['durum'] !== 'Tamamlandı' && $gorev['son_tarih'] < date('Y-m-d');
}

// Görevleri duruma ve/veya derse göre süzer.
// Boş bırakılan filtre dikkate alınmaz, ikisi birlikte de kullanılabilir.
function gorevleriFiltrele($gorevler, $durum, $ders)
{
    $sonuc = [];
    foreach ($gorevler as $gorev) {
        if ($durum !== '' && $gorev['durum'] !== $durum) {
            continue; // Durum uymuyor, bu görevi atla
        }
        if ($ders !== '' && $gorev['ders'] !== $ders) {
            continue; // Ders uymuyor, bu görevi atla
        }
        $sonuc[] = $gorev;
    }
    return $sonuc;
}

// Sayaçlar için görevleri sayar: toplam, her durum ve gecikenler.
function gorevSayilariniHesapla($gorevler)
{
    $sayilar = [
        'toplam' => count($gorevler),
        'bekliyor' => 0,
        'devam' => 0,
        'tamamlandi' => 0,
        'geciken' => 0,
    ];

    foreach ($gorevler as $gorev) {
        switch ($gorev['durum']) {
            case 'Bekliyor':
                $sayilar['bekliyor']++;
                break;
            case 'Devam Ediyor':
                $sayilar['devam']++;
                break;
            case 'Tamamlandı':
                $sayilar['tamamlandi']++;
                break;
        }

        if (gecikmisMi($gorev)) {
            $sayilar['geciken']++;
        }
    }

    return $sayilar;
}

// Durum ve öncelik değerine göre renkli etiket için CSS sınıfı döndürür.
function etiketSinifi($deger)
{
    return match ($deger) {
        'Bekliyor' => 'etiket-bekliyor',
        'Devam Ediyor' => 'etiket-devam',
        'Tamamlandı' => 'etiket-tamamlandi',
        'Düşük' => 'etiket-dusuk',
        'Orta' => 'etiket-orta',
        'Yüksek' => 'etiket-yuksek',
        default => 'etiket-diger',
    };
}

// Bir sonraki sayfa açılışında bir kez gösterilecek mesajı kaydeder.
// $tur: 'basari' (yeşil kutu) veya 'hata' (kırmızı kutu)
function flashMesajAyarla($tur, $mesaj)
{
    $_SESSION['flash'] = ['tur' => $tur, 'mesaj' => $mesaj];
}

// Kayıtlı mesajı döndürür ve siler (böylece sayfa yenilenince tekrar görünmez).
// Mesaj yoksa null döndürür.
function flashMesajGetir()
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}
