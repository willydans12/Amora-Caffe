<?php
// index.php
require 'koneksi.php';

// Ambil semua produk
$stmt = $pdo->query("SELECT * FROM products WHERE id IN (1,2,3)");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Amorad Caffe</title>
  <!-- Bootstrap CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="index.css">
</head>
<body>
 <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container justify-content-center">
      <a class="navbar-brand fw-bold me-auto" href="#">Amorad Caffe</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-center" id="navMenu">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
          <li class="nav-item"><a class="nav-link" href="about.html">Tentang Kami</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Profile</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Kontak</a></li>
        </ul>
      </div>
      <div class="ms-auto d-flex align-items-center gap-3">
        <a class="nav-link p-0" href="#"><i class="bi bi-search fs-5"></i></a>
        <a class="nav-link position-relative p-0" href="#">
          <i class="bi bi-cart3 fs-5"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">0</span>
        </a>
      </div>
    </div>
  </nav>

  <!-- Carousel Hero -->
  <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
      <div class="carousel-item active" style="background: url('https://www.jurnal.id/wp-content/uploads/2019/10/shutterstock_747461182.jpg') no-repeat center/cover; height: 80vh;">
        <div class="carousel-caption d-none d-md-block">
          <span class="badge bg-dark">Special Offer</span>
          <h1>Selamat Datang di<br>Amorad Caffe</h1>
          <p>Amorad Caffe adalah destinasi kopi premium yang menghadirkan pengalaman kopi autentik.</p>
          <a href="menu.php" class="btn btn-brown">Lihat Menu Kami</a>
        </div>
      </div>
      <div class="carousel-item" style="background: url('https://static.tripzilla.id/media/14530/conversions/Preview-Kedai-Kopi-Kekinian-Jakarta-w768.webp') no-repeat center/cover; height: 80vh;">
        <div class="carousel-caption d-none d-md-block">
          <span class="badge bg-dark">Special Offer</span>
          <h1>Diskon 20%<br>Pembelian Pertama</h1>
          <p>Kunjungi kami dan dapatkan diskon 20% untuk pembelian pertama Anda.</p>
          <a href="menu.php" class="btn btn-brown">Pesan Sekarang</a>
        </div>
      </div>
      <div class="carousel-item" style="background: url('https://pinterplan.com/wp-content/uploads/2021/10/Cafe-Coffee-Shop-1024x716.jpg') no-repeat center/cover; height: 80vh;">
        <div class="carousel-caption d-none d-md-block">
          <span class="badge bg-dark">Special Offer</span>
          <h1>Suasana Nyaman<br>Aroma Menggoda</h1>
          <p>Nikmati suasana cafe yang nyaman dengan aroma kopi yang menggoda.</p>
          <a href="menu.php" class="btn btn-brown">Kunjungi Kami</a>
        </div>
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
  <!-- Menu Favorit -->
  <section id="menu" class="py-5">
    <div class="container">
      <h2 class="text-center mb-4">Menu Favorit Kami</h2>
      <p class="text-center text-muted mb-5">
        Temukan berbagai pilihan kopi premium dengan cita rasa khas
      </p>
      <div class="row g-4">
        <?php foreach($products as $p): ?>
        <div class="col-md-4">
          <div class="card position-relative shadow-sm h-100">
            <img src="<?=htmlspecialchars($p['image_url'])?>" class="card-img-top" alt="<?=htmlspecialchars($p['name'])?>">
            <div class="detail-hover position-absolute top-0 end-0 m-2">
              <button class="btn btn-sm btn-light border"
                      data-bs-toggle="modal"
                      data-bs-target="#modal<?=$p['id']?>">
                Detail
              </button>
            </div>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?=htmlspecialchars($p['name'])?></h5>
              <div class="mb-2">
                <?php
                  $full = floor($p['rating']);
                  for($i=0;$i<$full;$i++) echo '<i class="bi bi-star-fill text-warning"></i>';
                  if($p['rating'] - $full >= .5) echo '<i class="bi bi-star-half text-warning"></i>';
                ?>
              </div>
              <p class="card-text text-muted"><?=htmlspecialchars($p['short_desc'])?></p>
              <div class="mt-auto d-flex justify-content-between align-items-center">
                <span class="fw-bold">Rp <?=number_format($p['price'],0,',','.')?></span>
                <div class="qty-control d-flex">
                  <button class="btn btn-outline-secondary btn-sm btn-decrement">−</button>
                  <input type="text" class="form-control form-control-sm text-center mx-1" value="0" readonly>
                  <button class="btn btn-outline-secondary btn-sm btn-increment">+</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Dinamis -->
        <div class="modal fade" id="modal<?=$p['id']?>" tabindex="-1" aria-labelledby="modalLabel<?=$p['id']?>" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header bg-brown text-white">
                <h5 class="modal-title" id="modalLabel<?=$p['id']?>">Detail Produk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <img src="<?=htmlspecialchars($p['image_url'])?>" class="img-fluid rounded mb-4" alt="">
                <h5 class="mb-3"><?=htmlspecialchars($p['name'])?></h5>
                <p class="text-muted mb-4"><?=htmlspecialchars($p['modal_desc'])?></p>
                <p><strong>Asal Biji:</strong> <?=htmlspecialchars($p['origin'])?></p>
                <p><strong>Intensitas:</strong> <?=htmlspecialchars($p['intensity'])?></p>
                <p><strong>Level Roasting:</strong> <?=htmlspecialchars($p['roast_level'])?></p>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

 <!-- About -->
  <section id="about" class="py-5 bg-light">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <img src="https://d1r9hss9q19p18.cloudfront.net/uploads/2016/01/IMG_3792-1.jpg" class="img-fluid rounded" alt="Tentang Amorad Caffe">
        </div>
        <div class="col-lg-6">
          <h2 class="mb-4">Tentang Amorad Caffe</h2>
          <p>Amorad Caffe adalah tempat di mana passion untuk kopi bertemu dengan keahlian barista profesional. Kami berkomitmen untuk menghadirkan pengalaman kopi terbaik melalui pemilihan biji kopi premium dan teknik brewing yang presisi.</p>
          <ul class="list-unstyled mt-4">
            <li class="mb-3 d-flex">
              <div class="icon-circle me-3">
                <i class="bi bi-cup-straw text-brown fs-4"></i>
              </div>
              <div>
                <h6 class="mb-1">Biji Kopi Premium</h6>
                <p class="mb-0 text-muted">Kami hanya menggunakan biji kopi berkualitas tinggi dari berbagai origin terbaik.</p>
              </div>
            </li>
            <li class="mb-3 d-flex">
              <div class="icon-circle me-3">
                <i class="bi bi-person-badge text-brown fs-4"></i>
              </div>
              <div>
                <h6 class="mb-1">Barista Berpengalaman</h6>
                <p class="mb-0 text-muted">Tim barista kami telah terlatih dan bersertifikasi internasional.</p>
              </div>
            </li>
            <li class="mb-3 d-flex">
              <div class="icon-circle me-3">
                <i class="bi bi-award text-brown fs-4"></i>
              </div>
              <div>
                <h6 class="mb-1">Kualitas Terjamin</h6>
                <p class="mb-0 text-muted">Setiap cangkir kopi kami dibuat dengan standar kualitas tinggi.</p>
              </div>
            </li>
            <li class="mb-3 d-flex">
              <div class="icon-circle me-3">
                <i class="bi bi-clock text-brown fs-4"></i>
              </div>
              <div>
                <h6 class="mb-1">Buka Setiap Hari</h6>
                <p class="mb-0 text-muted">Senin - Minggu: 08.00 - 22.00 WIB.</p>
              </div>
            </li>
            <li class="d-flex">
              <div class="icon-circle me-3">
                <i class="bi bi-geo-alt text-brown fs-4"></i>
              </div>
              <div>
                <h6 class="mb-1">Lokasi Strategis</h6>
                <p class="mb-0 text-muted">Berada di pusat kota dengan akses yang mudah dijangkau.</p>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
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
            <li><a href="about.html" class="text-white text-decoration-none">Tentang Kami</a></li>
            <li><a href="#" class="text-white text-decoration-none">Profile</a></li>
            <li><a href="#" class="text-white text-decoration-none">Karir</a></li>
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
    <!-- Floating Cart -->
    <a href="#" class="cart-float d-flex justify-content-center align-items-center">
      <i class="bi bi-cart3 fs-4 text-white"></i>
      <span class="badge bg-warning text-dark position-absolute top-0 start-100 translate-middle">0</span>
    </a>
  </footer>

  <!-- Bootstrap & Custom JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="index.js"></script>
</body>
</html>
