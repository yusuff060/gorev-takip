# Öğrenci Görev Takip Sistemi

Öğrencilerin derslerine ait ödev ve görevleri tek bir yerden **ekleyip, listeleyip,
filtreleyip ve silebildiği** basit bir web uygulaması.

Proje; framework, veritabanı veya hazır yönetim paneli kullanmadan **saf PHP + HTML + CSS**
ile yazılmıştır. Veriler bir PHP dizisi olarak `$_SESSION` içinde tutulur.

## Özellikler

- **Listeleme:** Görevler kartlar halinde `foreach` ile listelenir. En yeni görev en üsttedir.
- **Renkli etiketler:** Durum (Bekliyor / Devam Ediyor / Tamamlandı) ve öncelik
  (Düşük / Orta / Yüksek) değerine göre farklı renkler.
- **"Gecikti" etiketi:** Son tarihi geçmiş ve tamamlanmamış görevler kırmızı ile işaretlenir.
- **Görev ekleme (POST):** Form PHP'de doğrulanır; hatalar Türkçe ve alanın altında gösterilir,
  girilen değerler formda korunur. Başarılı eklemeden sonra Post/Redirect/Get ile ana
  sayfaya dönülür ve tek seferlik "Görev başarıyla eklendi" mesajı gösterilir.
- **Doğrulama kuralları:** Başlık zorunlu ve 3–100 karakter, ders zorunlu ve listeden,
  açıklama en fazla 500 karakter, öncelik/durum listeden, son tarih zorunlu ve geçerli
  `YYYY-AA-GG` tarihi. Tüm değerlere `trim()` uygulanır.
- **Görev silme (POST):** Her kartta gizli `id` alanı olan küçük bir form vardır.
  `sil.php` GET isteğinde hiçbir şey silmez. Olmayan id için hata mesajı gösterilir.
  Silmeden önce küçük bir JavaScript onayı ("Silmek istediğine emin misin?") çıkar.
- **Filtreleme (GET):** Duruma ve/veya derse göre filtreleme, seçim formda seçili kalır,
  "Filtreyi temizle" bağlantısı.
- **Sayaçlar:** Toplam, Bekliyor, Devam Ediyor, Tamamlandı ve Geciken görev sayıları.
- **Güvenli çıktı:** Ekrana basılan her kullanıcı verisi `e()` fonksiyonu
  (`htmlspecialchars`) ile kaçışlanır. `<script>` içeren bir başlık çalışmaz, metin olarak görünür.
- **Örnek veriler:** Oturum boşsa 5 örnek görev otomatik yüklenir.
- **Mobil uyumlu:** Flex ve grid ile düzen, CSS kütüphanesi yok.

## Klasör yapısı

```
gorev-takip/
├── index.php        → Ana sayfa: başlık, sayaçlar, filtre formu, görev listesi, silme butonları
├── ekle.php         → Yeni görev formu, POST ile gelen veriyi doğrulama ve kaydetme
├── sil.php          → Sadece POST kabul eder, id'ye göre siler, index.php'ye yönlendirir
├── functions.php    → Oturum başlatma, sabit listeler, örnek veriler ve tüm yardımcı fonksiyonlar
├── assets/
│   └── style.css    → Tüm sayfaların görünümü
├── video/
│   └── demo.mp4     → 30 saniyelik çalışma videosu
├── .gitignore
└── README.md
```

`functions.php` içindeki fonksiyonlar:

| Fonksiyon | Görevi |
|---|---|
| `e()` | Metni `htmlspecialchars` ile güvenli hale getirir |
| `ornekVerileriYukle()` | Oturum boşsa 5 örnek görev ekler |
| `gorevleriGetir()` | Tüm görevleri döndürür |
| `gorevEkle()` | Yeni görevi artan bir id ile ekler |
| `gorevSil()` | id'ye göre siler, bulamazsa `false` döndürür |
| `gecerliTarihMi()` | Tarihin `YYYY-AA-GG` biçiminde ve gerçek bir tarih olduğunu kontrol eder |
| `gorevDogrula()` | Form verisini kontrol eder, hata mesajlarını dizi olarak döndürür |
| `gecikmisMi()` | Görevin süresi geçmiş ve tamamlanmamış mı? |
| `gorevleriFiltrele()` | Duruma ve/veya derse göre süzer |
| `gorevSayilariniHesapla()` | Sayaçlar için sayıları hesaplar |
| `etiketSinifi()` | Durum/önceliğe göre CSS sınıfı döndürür |
| `flashMesajAyarla()` / `flashMesajGetir()` | Tek seferlik başarı/hata mesajı |

Veriye erişen kodların hepsi `functions.php` içinde olduğu için ileride MySQL/PDO'ya
geçilirse sadece bu dosyanın değişmesi yeterlidir.

## XAMPP'te çalıştırma

1. `gorev-takip` klasörünü XAMPP'in `htdocs` klasörüne kopyalayın
   (Windows'ta genellikle `C:\xampp\htdocs\gorev-takip`).
2. XAMPP Control Panel'i açın ve **Apache**'yi başlatın (MySQL gerekmez).
3. Tarayıcıda şu adrese gidin: <http://localhost/gorev-takip/>

Alternatif (XAMPP olmadan, PHP 8 kurulu ise):

```bash
cd gorev-takip
php -S localhost:8000
```

Ardından <http://localhost:8000> adresini açın.

## Bilinen eksikler

- Veriler `$_SESSION` içinde tutulduğu için **tarayıcı kapanınca veya oturum sona erince silinir**;
  her kullanıcı yalnızca kendi görevlerini görür.
- Görev **güncelleme (düzenleme)** özelliği yok; durum değiştirmek için silip yeniden eklemek gerekir.
- Kullanıcı girişi / üyelik sistemi yok.
- Sıralama seçeneği ve sayfalama yok.
- Formlarda CSRF koruması yok (ders kapsamı dışında bırakıldı).

## Çalışma videosu

Uygulamanın ana sayfa, hatalı ekleme, başarılı ekleme, filtreleme ve silme adımlarını
gösteren 30 saniyelik video: [video/demo.mp4](video/demo.mp4)

## Yapay zekâ kullanımı

- **Kullanılan araç:** Claude Code (Anthropic)
- **Kullanım amaçları:**
  - Proje iskeletinin ve dosya yapısının oluşturulması
  - PHP fonksiyonlarının, formun ve doğrulama kodlarının yazılması
  - CSS ile arayüz tasarımı
  - Testlerin (ekleme, hatalı ekleme, `<script>` kaçışı, silme, filtreleme) yapılması
  - Demo videosunun otomatik kaydedilmesi ve README taslağının hazırlanması
- **Örnek istem:**
  > "PHP ile Öğrenci Görev Takip Sistemi yap. Framework ve veritabanı kullanma, veriler
  > PHP dizisi ve $_SESSION ile tutulsun. Ekleme ve silme POST, filtreleme GET ile olsun.
  > Kullanıcı verilerini htmlspecialchars ile güvenli bas. Kod başlangıç seviyesinde,
  > yorumlar Türkçe olsun."

### Öğrencinin kendi katkıları

[BURAYI ÖĞRENCİ DOLDURACAK]
