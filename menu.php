<?php
// menu.php
require 'koneksi.php';

// Ambil produk dari DB
$stmt = $pdo->query("SELECT * FROM products ORDER BY name ASC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Ambil list kategori unik dari tabel products
$catsStmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = $catsStmt->fetchAll(PDO::FETCH_COLUMN);
// Ambil daftar kategori unik
$cats = $pdo->query("SELECT DISTINCT category FROM products")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Amorad Caffe - Menu</title>
  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="menu.css">
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
          <li class="nav-item"><a class="nav-link" href="#">Tentang Kami</a></li>
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

  <section class="py-5" id="menu">
  <div class="container">
    <!-- Judul Section -->
    <div class="text-center mb-4">
      <h2 class="fw-bold">Menu Kami</h2>
      <p class="text-muted">Temukan berbagai pilihan kopi premium dengan cita rasa khas</p>
    </div>
    <div class="row">
      <!-- Sidebar -->
      <aside class="col-lg-3 mb-4">
        <!-- Search Box -->
        <div class="card p-3 shadow-sm mb-4">
          <div class="input-group">
            <span class="input-group-text bg-white">
              <i class="bi bi-search text-muted"></i>
            </span>
            <input id="searchInput" type="text" class="form-control" placeholder="Cari menu…">
          </div>
        </div>
        <!-- Kategori (diambil Dari DB) -->
        <div class="card p-3 shadow-sm mb-4">
          <h5 class="fw-bold">Kategori</h5>
          <ul class="list-unstyled">
            <!-- Tombol Semua Menu -->
            <li>
              <button class="btn btn-sm w-100 text-start category-btn active" data-cat="all">
                Semua Menu
              </button>
            </li>
            <!-- Loop Kategori -->
            <?php foreach($categories as $cat): 
              $key = strtolower(str_replace(' ', '-', $cat));
            ?>
            <li>
              <button
                class="btn btn-sm w-100 text-start category-btn"
                data-cat="<?= htmlspecialchars($key) ?>">
                <?= htmlspecialchars($cat) ?>
              </button>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <!-- Filter Harga & Urutkan -->
        <div class="card p-3 shadow-sm">
          <h5 class="fw-bold">Filter</h5>
          <div class="mb-3">
            <label class="form-label small">Rentang Harga</label>
            <div class="d-flex gap-2">
              <input id="minPrice" type="number" class="form-control form-control-sm" placeholder="Min">
              <input id="maxPrice" type="number" class="form-control form-control-sm" placeholder="Max">
            </div>
          </div>
          <div>
            <label class="form-label small">Urutkan</label>
            <select id="sortSelect" class="form-select form-select-sm">
              <option value="date_desc">Terbaru</option>
              <option value="price_asc">Harga Terendah</option>
              <option value="price_desc">Harga Tertinggi</option>
              <option value="rating_desc">Rating</option>
            </select>
          </div>
        </div>
      </aside>

         <!-- Grid Produk -->
<!-- Grid Produk -->
<div class="col-lg-9">
  <div id="productGrid" class="row g-4">
    <?php foreach($products as $p): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm product-card"
             data-category="<?= htmlspecialchars(strtolower(str_replace(' ', '-', $p['category']))) ?>"
             data-price="<?= $p['price'] ?>"
             data-rating="<?= $p['rating'] ?>"
             data-date="<?= $p['created_at'] ?>">
          <div class="img-wrapper position-relative">
            <img src="<?= htmlspecialchars($p['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>">
            <div class="overlay" role="button" data-bs-toggle="modal" data-bs-target="#modal<?= $p['id'] ?>">
              <span class="btn-detail">Detail</span>
            </div>
          </div>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">
              <?= htmlspecialchars($p['name']) ?>
              <span class="text-warning">
                <?php
                  $full = floor($p['rating']);
                  for ($i = 0; $i < $full; $i++) echo '<i class="bi bi-star-fill"></i>';
                  if ($p['rating'] - $full >= 0.5) echo '<i class="bi bi-star-half"></i>';
                ?>
              </span>
            </h5>
            <p class="card-text text-muted small mb-3"><?= htmlspecialchars($p['short_desc']) ?></p>
            <div class="mt-auto d-flex justify-content-between align-items-center">
              <h6 class="mb-0 fw-bold">Rp <?= number_format($p['price'], 0, ',', '.') ?></h6>
              <div class="input-group input-group-sm qty-group">
                <button class="btn btn-outline-secondary btn-minus">−</button>
                <input type="text" class="form-control qty-input text-center" value="0" readonly>
                <button class="btn btn-outline-secondary btn-plus">+</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>


      <!-- Modal Detail Produk (Diletakkan di luar product-card agar tidak hilang saat filter dijalankan) -->
      <?php foreach($products as $p): ?>
  <div class="modal fade" id="modal<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold">Detail Produk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <img src="<?= htmlspecialchars($p['image_url']) ?>" class="img-fluid rounded mb-3 detail-image" alt="<?= htmlspecialchars($p['name']) ?>">

          <h5><?= htmlspecialchars($p['name']) ?></h5>
          <p><?= htmlspecialchars($p['modal_desc']) ?></p>
          <p><strong>Asal Biji:</strong> <?= htmlspecialchars($p['origin']) ?></p>
          <p><strong>Intensitas:</strong> <?= htmlspecialchars($p['intensity']) ?></p>
          <p><strong>Level Roasting:</strong> <?= htmlspecialchars($p['roast_level']) ?></p>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>

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
            <li><a href="#" class="text-white text-decoration-none">Tentang Kami</a></li>
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

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="menu.js"></script>
</body>
</html>
