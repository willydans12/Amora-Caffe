<?php
// Mulai atau lanjutkan session yang ada
session_start();

// Cek apakah 'user_id' ada di dalam session.
// Jika tidak ada, artinya user belum login.
if (!isset($_SESSION['user_id'])) {
    // Arahkan (redirect) user kembali ke halaman login
    header('Location: login.php?error=auth'); // 'auth' artinya butuh otentikasi
    exit; // Pastikan script berhenti dieksekusi setelah redirect
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Amorad Caffe - Abous us</title>
  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&family=Great+Vibes&display=swap" rel="stylesheet">
 <link rel="stylesheet" href="about.css">
</head>
<body>

<header>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container justify-content-center">
      <a class="navbar-brand fw-bold me-auto" href="index.php">Amorad Caffe</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-center" id="navMenu">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">Tentang Kami</a></li>
          <li class="nav-item"><a class="nav-link" href="profil.php">Profile</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Kontak</a></li>
        </ul>
      </div>
      <div class="ms-auto d-flex align-items-center gap-3">
       
       <!-- Navbar Cart -->
<a class="nav-link position-relative p-0"
   href="#"
   data-bs-toggle="offcanvas"
   data-bs-target="#cartOffcanvas"
   aria-controls="cartOffcanvas">
  <i class="bi bi-cart3 fs-5"></i>
  <span class="cart-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
    0
  </span>
</a>

      </div>
    </div>
  </nav>
</header>


<main>
  <section>
    <h3>Our Story</h3>
    <p class="about-text">
      Di Amora Caffe, setiap cangkir kopi menceritakan kisah — tentang dedikasi, seni, dan kebersamaan.
      Didirikan pada tahun 2025, kami menyajikan aroma yang kaya dan suasana hangat untuk menciptakan tempat nyaman di mana teman berkumpul dan saat-saat indah dinikmati.
    </p>
  </section>

  <section>
    <h3>Our Value</h3>
    <div class="values-cards">
      <div class="card">
        <h4>Kualitas</h4>
        <p>Biji kopi terbaik & bahan segar kami pilih untuk rasa terbaik di setiap cangkir.</p>
      </div>
      <div class="card">
        <h4>Komunitas</h4>
        <p>Dukung petani dan seniman lokal, jadi pusat kreativitas dan kebersamaan.</p>
      </div>
      <div class="card">
        <h4>Keberlanjutan</h4>
        <p>Praktik ramah lingkungan: gelas terurai & pengadaan bertanggung jawab.</p>
      </div>
    </div>
  </section>

  <section>
    <h3>Our Team</h3>
    <div class="team">
      <div class="team-member">
        <img src="wildan.jpeg" alt="Ghaisan Wildan Bathsya">
        <h5>Ghaisan Wildan Bathsya</h5>
        <p>2317051054</p>
      </div>
      <div class="team-member">
        <img src="dilvi.jpg" alt="Anggota 2">
        <h5>Dilvi Yola Ferlyanda</h5>
        <p>2317051035</p>
      </div>
      <div class="team-member">
        <img src="dwi.jpg" alt="Anggota 3">
        <h5> Dwi Andini</h5>
        <p>2317051025</p>
      </div>
      <div class="team-member">
        <img src="citra.jpg" alt="Anggota 4">
        <h5>Citra Fardiani</h5>
        <p>2317051101</p>
      </div>
    </div>
  </section>

  <section>
    <h3>Kontak & Lokasi</h3>
    <p class="contact">
      Kunjungi kami di <a href="#">Jl. Sudirman No. 123
Jakarta Pusat, 10220</a><br />
      Telepon: <a href="tel:+1234567890">(021) 555-0123</a><br />
      Email: <a href="mailto:info@amoracaffe.com">info@amoradcaffe.com</a>
    </p>
  </section>
</main>

<footer>
  <!-- Footer -->
  <footer class="bg-brown text-white pt-5 pb-3">
    <div class="container">
      <div class="row">
        <div class="col-md-3 mb-4">
          <h5 class="fw-bold">Amorad Caffe</h5>
          <p class="small mb-1">Jl. Sudirman No. 123<br>Jakarta Pusat, 10220</p>
          <p class="small mb-1">Telp: (021) 555-0123</p>
          <p class="small">Email: info@amoradcaffe.com</p>
        </div>
        <div class="col-md-3 mb-4">
          <h5 class="fw-bold">Jam Buka</h5>
          <p class="small mb-1">Senin – Jumat: 07:00 – 22:00</p>
          <p class="small">Sabtu – Minggu: 08:00 – 23:00</p>
        </div>
        <div class="col-md-3 mb-4">
          <h5 class="fw-bold">Menu Cepat</h5>
          <ul class="list-unstyled small">
            <li><a href="menu.php" class="text-white text-decoration-none">Menu</a></li>
            <li><a href="about.php" class="text-white text-decoration-none">Tentang Kami</a></li>
            <li><a href="profil.php" class="text-white text-decoration-none">Profile</a></li>
            <li><a href="contact.php" class="text-white text-decoration-none">Kontak</a></li>
          </ul>
        </div>
        <div class="col-md-3 mb-4">
          <h5 class="fw-bold">Ikuti Kami</h5>
          <div class="d-flex gap-2">
            <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="bi bi-instagram"></i></a>
            <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="bi bi-facebook"></i></a>
            <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="bi bi-twitter"></i></a>
          </div>
        </div>
      </div>
      <hr class="border-light">
      <div class="text-center small">© 2025 Amorad Caffe. Hak Cipta Dilindungi.</div>
    </div>
</footer>

</body>
</html>
