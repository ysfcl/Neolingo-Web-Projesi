<?php
session_start();
require_once 'config.php';

$host = "localhost";  // Genelde localhost olur
$db_user = "dbusr22360859012";
$db_pass = "oreLBuIvYV3o";
$db_name = "dbstorage22360859012";

$mysqli = new mysqli($host,$db_user ,$db_pass,$db_name);
if ($mysqli->connect_errno) {
    die("Veritabanına bağlanılamadı: " . $mysqli->connect_error);
}

if (isset($_POST['register'])) {

$username = htmlspecialchars(trim($_POST['kullanici_adi']));
$email = htmlspecialchars(trim($_POST['email']));

$stmt = $mysqli->prepare("SELECT * FROM kullanicilar WHERE kullanici_adi = ? OR email = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $register_error = "Bu kullanıcı adı veya e-posta zaten kayıtlı!";
    return;
}else{
    $fname = htmlspecialchars(trim($_POST['ad']));
    $lname = htmlspecialchars(trim($_POST['soyad']));
    
    $gsm = htmlspecialchars(trim($_POST['telefon']));
    $birth = htmlspecialchars(trim($_POST['dogum_tarihi']));
    $password = password_hash($_POST['sifre'], PASSWORD_BCRYPT);
}

    

    $stmt = $mysqli->prepare("INSERT INTO kullanicilar (kullanici_adi, ad, soyad, email, telefon, dogum_tarihi, sifre_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $username, $fname, $lname, $email, $gsm, $birth, $password);

    if($stmt->execute()){
$_SESSION['user'] = [
    'kullanici_id' => $mysqli->insert_id,
    'kullanici_adi' => $username,
    'ad' => $fname,
    'soyad' => $lname,
    'email' => $email,
    'telefon' => $gsm,
    'dogum_tarihi' => $birth,
    'sifre_hash' => $password,
    'ogrenilen_dil' => null
];
}else {
    $register_error = "Kayıt başarısız oldu!";
}

}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT * FROM kullanicilar WHERE kullanici_adi = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['sifre_hash'])) {
            $_SESSION['user'] = $row;
        } else {
            $login_error = "Şifre hatalı!";
        }
    } else {
        $login_error = "Kullanıcı bulunamadı!";
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['bilgi_ekle']) && isset($_SESSION['user'])) {
    $stmt = $mysqli->prepare("INSERT INTO bilgiler (kullanici_id, baslik, icerik, tarih) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $_SESSION['user']['kullanici_id'], $_POST['baslik'], $_POST['icerik'], $_POST['tarih']);
    $stmt->execute();
}

if (isset($_POST['sil_id'])) {
    $stmt = $mysqli->prepare("DELETE FROM bilgiler WHERE bilgi_id = ? AND kullanici_id = ?");
    $stmt->bind_param("ii", $_POST['sil_id'], $_SESSION['user']['kullanici_id']);
    $stmt->execute();
}

if (isset($_POST['dil_sec']) && isset($_SESSION['user'])) {
    $dil = $_POST['dil'];
    $stmt = $mysqli->prepare("UPDATE kullanicilar SET ogrenilen_dil = ? WHERE kullanici_id = ?");
    $stmt->bind_param("si", $dil, $_SESSION['user']['kullanici_id']);
    $stmt->execute();
    $_SESSION['user']['ogrenilen_dil'] = $dil;
}

if (isset($_POST['dil_sifirla']) && isset($_SESSION['user'])) {
    $stmt = $mysqli->prepare("UPDATE kullanicilar SET ogrenilen_dil = NULL WHERE kullanici_id = ?");
    $stmt->bind_param("i", $_SESSION['user']['kullanici_id']);
    $stmt->execute();
    $_SESSION['user']['ogrenilen_dil'] = null;
}

if (isset($_POST['bilgi_duzenle']) && isset($_SESSION['user'])) {
    $stmt = $mysqli->prepare("UPDATE bilgiler SET baslik = ?, icerik = ?, tarih = ? WHERE bilgi_id = ? AND kullanici_id = ?");
    $stmt->bind_param("sssii", $_POST['baslik'], $_POST['icerik'], $_POST['tarih'], $_POST['duzenle_id'], $_SESSION['user']['kullanici_id']);
    $stmt->execute();
}


?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Neolingo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('images/kartal.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class=" d-flex ">
  <?php if (!isset($_SESSION['user'])): ?>
    <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#registerModal">Kayıt Ol</button>
    <button class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#loginModal">Giriş Yap</button>
        <a class="navbar-brand mx-auto position-absolute start-50 translate-middle-x" href="#"><?= SITE_NAME ?></a>
    <?php else: ?>
    <form method="post">
      <button name="logout" class="btn btn-outline-danger">Çıkış Yap</button>
    </form>
  <?php endif; ?>
</div>
</nav>

<div class="container py-4">
<?php if (!isset($_SESSION['user'])): ?>
  <div class="bg-primary text-white text-center p-5 rounded mb-4" style="background: rgba(0,0,0,0.5);">
      <h2>Hoş Geldiniz!</h2>
      <p class="lead">Bu platformda İngilizce, Fransızca, Türkçe gibi pek çok dili kolaylıkla öğrenebilirsiniz.</p>
      <p>Öğrenmeye başlamak için yukarıdaki "Giriş Yap" ya da "Kayıt Ol" butonlarını kullanın.</p>
  </div>
<?php endif; ?>    

    <?php if (isset($_SESSION['user'])): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">Hoş geldiniz, <?= $_SESSION['user']['ad'] ?> <?= $_SESSION['user']['soyad'] ?></h4>
                <p><strong>Kullanıcı Adı:</strong> <?= $_SESSION['user']['kullanici_adi'] ?> <br>
                   <strong>Email:</strong> <?= $_SESSION['user']['email'] ?></p>
            </div>
        </div>

    <?php if (empty($_SESSION['user']['ogrenilen_dil'])): ?>
        <!-- Dil seçimi ekranı -->
        <h4 class="text-white text-center mb-4">Hangi dili öğrenmek istiyorsunuz?</h4>
<div class="row row-cols-1 row-cols-md-3 g-4">
    <?php
    $diller = [
    ['isim' => 'İngilizce', 'resim' => 'images/ingilizce.png', 'aciklama' => 'Dünya genelinde en çok konuşulan ve uluslararası geçerliliğe sahip olan dildir.'],
    ['isim' => 'Almanca', 'resim' => 'images/almanca.jpg', 'aciklama' => 'Avrupa’da yaygın olarak konuşulan, bilim ve mühendislikte güçlü bir dildir.'],
    ['isim' => 'Fransızca', 'resim' => 'images/fransizca.png', 'aciklama' => 'Sanat, moda ve diplomasi alanlarında önemlidir.'],
    ['isim' => 'İspanyolca', 'resim' => 'images/ispanyolca.png', 'aciklama' => 'Latin Amerika ve İspanya’da yaygındır,ritmik ve eğlenceli olmasından dolayı hızlı öğrenilebilen bir dildir.'],
    ['isim' => 'Türkçe', 'resim' => 'images/turkce.png', 'aciklama' => 'Türk halklarının ortak dilidir, Türkiye Cumhuriyetinde konuşulur .']
];
    
    foreach ($diller as $dil): ?>
        <div class="col">
            <form method="post">
                <input type="hidden" name="dil" value="<?= $dil['isim'] ?>">
                <div class="card h-100 text-center bg-light">
                    <img src="<?= $dil['resim'] ?>" class="card-img-top" alt="<?= $dil['isim'] ?>" style="height: 180px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?= $dil['isim'] ?></h5>
                        <p class="card-text small"><?= $dil['aciklama'] ?></p>
                        <button type="submit" name="dil_sec" class="btn btn-primary">Bu Dili Seç</button>
                    </div>
                </div>
            </form>
        </div>
    <?php endforeach; ?>
</div>
    
    <?php else: ?>
        <!-- Öğrenilen dili göster -->
        <div class="alert alert-success text-center">
            Öğrenilen Dil: <strong><?= $_SESSION['user']['ogrenilen_dil'] ?></strong>
        </div>

        <form method="post" class="text-center mt-2">
            <button type="submit" name="dil_sifirla" class="btn btn-outline-warning btn-sm">Farklı bir dil seç</button>
        </form>

        <!-- Yeni bilgi ekleme formu -->
        <div class="card mb-3">
            <div class="card-header">Yeni Bilgi Ekle</div>
            <div class="card-body">
                <form method="post">
                    <input type="text" name="baslik" class="form-control mb-2" placeholder="Başlık" required>
                    <textarea name="icerik" class="form-control mb-2" placeholder="İçerik" required></textarea>
                    <input type="date" name="tarih" class="form-control mb-2" required>
                    <button type="submit" name="bilgi_ekle" class="btn btn-success">Bilgiyi Kaydet</button>
                </form>
            </div>
        </div>
        
<?php
    $stmt = $mysqli->prepare("SELECT * FROM bilgiler WHERE kullanici_id = ?");
    $stmt->bind_param("i", $_SESSION['user']['kullanici_id']);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<?php if ($result->num_rows > 0): ?>
  <div class="card">
    <div class="card-header">Eklediğiniz Bilgiler</div>
    <div class="card-body">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="border-bottom mb-3">
          <h5><?= htmlspecialchars($row['baslik']) ?></h5>
          <p><?= nl2br(htmlspecialchars($row['icerik'])) ?></p>
          <small class="text-muted">Tarih: <?= $row['tarih'] ?></small>
          <form method="post" class="mt-1">
              <input type="hidden" name="sil_id" value="<?= $row['bilgi_id'] ?>">
              <button type="submit" class="btn btn-sm btn-outline-danger">Sil</button>
          </form>

        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#duzenleModal<?= $row['bilgi_id'] ?>">Düzenle</button>
            </div>
        </div>
        <div class="modal fade" id="duzenleModal<?= $row['bilgi_id'] ?>" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="post">
            <div class="modal-header">
              <h5 class="modal-title">Bilgiyi Düzenle</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="duzenle_id" value="<?= $row['bilgi_id'] ?>">
              <input type="text" name="baslik" class="form-control mb-2" value="<?= htmlspecialchars($row['baslik']) ?>" required>
              <textarea name="icerik" class="form-control mb-2" required><?= htmlspecialchars($row['icerik']) ?></textarea>
              <input type="date" name="tarih" class="form-control mb-2" value="<?= $row['tarih'] ?>" required>
            </div>
            <div class="modal-footer">
              <button type="submit" name="bilgi_duzenle" class="btn btn-primary">Güncelle</button>
            </div>
          </form>
        </div>
      </div>
    </div>
      <?php endwhile; ?>
    </div>
  </div>
<?php else: ?>
  <div class="alert alert-info">Henüz hiç bilgi eklemediniz.</div>
<?php endif; ?>
    <?php endif; ?>

<?php else: ?>
    <p>Lütfen giriş yapın veya kayıt olun.</p>
<?php endif; ?>
        

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title">Giriş Yap</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" name="username" class="form-control mb-2" placeholder="Kullanıcı Adı" required>
          <input type="password" name="password" class="form-control mb-2" placeholder="Şifre" required>
        </div>
        <div class="modal-footer">
          <button type="submit" name="login" class="btn btn-success">Giriş Yap</button>
        </div>
        <?php if (isset($login_error)): ?>
  <div class="alert alert-danger"><?= $login_error ?></div>
<?php endif; ?>
      </form>
    </div>
  </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title">Kayıt Ol</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" name="kullanici_adi" class="form-control mb-2" placeholder="Kullanıcı Adı" required>
          <input type="text" name="ad" class="form-control mb-2" placeholder="Ad">
          <input type="text" name="soyad" class="form-control mb-2" placeholder="Soyad">
          <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
          <input type="text" name="telefon" class="form-control mb-2" placeholder="Telefon">
          <input type="date" name="dogum_tarihi" class="form-control mb-2">
          <input type="password" name="sifre" class="form-control mb-2" placeholder="Şifre" required>
          
          <?php if (isset($register_error)): ?>
            <div class="alert alert-danger"><?= $register_error ?></div>
          <?php endif; ?>

        </div>
        <div class="modal-footer">
          <button type="submit" name="register" class="btn btn-primary">Kayıt Ol</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>