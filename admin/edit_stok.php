<?php
// edit_stok.php
session_start();


require_once __DIR__ . '/../koneksi.php';

// ——————————————————————————
// 1) Mode tambah / edit
// ——————————————————————————
$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$product = [
    'name'         => '',
    'short_desc'   => '',
    'modal_desc'   => '',
    'price'        => '',
    'stock'        => 1,
    'rating'       => '0.0',
    'created_at'   => date('Y-m-d'),
    'origin'       => '',
    'image_url'    => '',
    'intensity'    => '',
    'roast_level'  => '',
    'category'     => ''
];
if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id=:id");
    $stmt->execute([':id'=>$_GET['id']]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $product = $row;
        $product['created_at'] = substr($row['created_at'],0,10);
    } else {
        header("Location: Manajemen-Stok-Admin.php");
        exit;
    }
}

// ——————————————————————————
// 2) Ambil kategori unik
// ——————————————————————————
$catsStmt   = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = $catsStmt->fetchAll(PDO::FETCH_COLUMN);

// ——————————————————————————
// 3) Proses submit
// ——————————————————————————
if ($_SERVER['REQUEST_METHOD']==='POST') {
    // ambil semua field
    $f = array_map('trim', $_POST);
    $sqlParams = [
      ':name'        => $f['name'],
      ':short_desc'  => $f['short_desc'],
      ':modal_desc'  => $f['modal_desc'],
      ':price'       => floatval($f['price']),
      ':stock'       => intval($f['stock']),
      ':rating'      => floatval($f['rating']),
      ':created_at'  => $f['created_at'],
      ':origin'      => $f['origin'],
      ':image_url'   => $f['image_url'],
      ':intensity'   => $f['intensity'],
      ':roast_level' => $f['roast_level'],
      ':category'    => $f['category'] ?: $f['new_category']
    ];

    if ($isEdit) {
        $sql = "UPDATE products SET
                  name=:name, short_desc=:short_desc, modal_desc=:modal_desc,
                  price=:price, stock=:stock, rating=:rating,
                  created_at=:created_at, origin=:origin, image_url=:image_url,
                  intensity=:intensity, roast_level=:roast_level,
                  category=:category
                WHERE id=:id";
        $sqlParams[':id'] = $_GET['id'];
    } else {
        $sql = "INSERT INTO products
                  (name,short_desc,modal_desc,price,stock,rating,
                   created_at,origin,image_url,intensity,roast_level,category)
                VALUES
                  (:name,:short_desc,:modal_desc,:price,:stock,:rating,
                   :created_at,:origin,:image_url,:intensity,:roast_level,:category)";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($sqlParams);
    header("Location: Manajemen-Stok-Admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title><?= $isEdit?'Edit':'Tambah' ?> Stok</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors:{ primary:'#8B572A', secondary:'#B7791F' },
          borderRadius:{
            DEFAULT:'8px',button:'8px'
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css"/>
  <style>
    /* remove spinner */
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
      -webkit-appearance:none; margin:0;
    }
    input[type=number] { -moz-appearance:textfield; }
    .custom-select { appearance:none; -moz-appearance:none; -webkit-appearance:none;}
    .custom-date::-webkit-calendar-picker-indicator {
      opacity:0; position:absolute; width:100%; height:100%; cursor:pointer;
    }
  </style>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-3xl mx-auto bg-white min-h-screen shadow-sm">
    <!-- header -->
    <header class="px-6 py-4 border-b flex items-center">
      <a href="Manajemen-Stok-Admin.php" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100">
        <i class="ri-arrow-left-line"></i>
      </a>
      <h1 class="ml-4 text-2xl font-semibold"><?= $isEdit?'Edit':'Tambah' ?> Stok</h1>
    </header>
    <main class="p-6">
      <form method="post" class="space-y-6">
        <!-- name -->
        <div>
          <label class="block text-sm font-medium">Nama</label>
          <input name="name" required value="<?= htmlspecialchars($product['name'])?>"
            class="w-full border px-4 py-2 rounded-button focus:ring-primary"/>
        </div>
        <!-- short_desc -->
        <div>
          <label class="block text-sm font-medium">Short Description</label>
          <input name="short_desc" value="<?= htmlspecialchars($product['short_desc'])?>"
            class="w-full border px-4 py-2 rounded-button focus:ring-primary"/>
        </div>
        <!-- modal_desc -->
        <div>
          <label class="block text-sm font-medium">Modal Description</label>
          <textarea name="modal_desc" rows="3"
            class="w-full border px-4 py-2 rounded-button focus:ring-primary"><?= htmlspecialchars($product['modal_desc'])?></textarea>
        </div>
        <!-- price, stock, rating -->
        <div class="grid grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium">Harga</label>
            <input type="number" name="price" step="0.01" min="0"
              value="<?= $product['price']?>" class="w-full border px-4 py-2 rounded-button"/>
          </div>
          <div>
            <label class="block text-sm font-medium">Stok</label>
            <input type="number" name="stock" min="0"
              value="<?= $product['stock']?>" class="w-full border px-4 py-2 rounded-button"/>
          </div>
          <div>
            <label class="block text-sm font-medium">Rating</label>
            <input type="number" name="rating" min="0" max="5" step="0.1"
              value="<?= $product['rating']?>" class="w-full border px-4 py-2 rounded-button"/>
          </div>
        </div>
        <!-- created_at, origin, image_url -->
        <div class="grid grid-cols-3 gap-4">
          <div class="relative">
            <label class="block text-sm font-medium">Tanggal</label>
            <input type="text" readonly id="dt_txt"
              value="<?= date('d/m/Y',strtotime($product['created_at']))?>"
              class="w-full border px-4 py-2 rounded-button"/>
            <input type="date" name="created_at" id="created_at"
              value="<?= $product['created_at']?>"
              class="custom-date absolute inset-0 opacity-0"/>
          </div>
          <div>
            <label class="block text-sm font-medium">Origin</label>
            <input name="origin" value="<?= htmlspecialchars($product['origin'])?>"
              class="w-full border px-4 py-2 rounded-button"/>
          </div>
          <div>
            <label class="block text-sm font-medium">Image URL</label>
            <input name="image_url" value="<?= htmlspecialchars($product['image_url'])?>"
              class="w-full border px-4 py-2 rounded-button"/>
          </div>
        </div>
        <!-- intensity & roast_level -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">Intensity</label>
            <input name="intensity" value="<?= htmlspecialchars($product['intensity'])?>"
              class="w-full border px-4 py-2 rounded-button"/>
          </div>
          <div>
            <label class="block text-sm font-medium">Roast Level</label>
            <input name="roast_level" value="<?= htmlspecialchars($product['roast_level'])?>"
              class="w-full border px-4 py-2 rounded-button"/>
          </div>
        </div>
        <!-- category + new category -->
        <div>
          <label class="block text-sm font-medium">Kategori</label>
          <div class="flex gap-2">
            <select id="category" name="category" class="flex-1 border px-4 py-2 rounded-button custom-select">
              <option value="">-- pilih --</option>
              <?php foreach($categories as $c): ?>
              <option <?= $product['category']===$c?'selected':'' ?>>
                <?= htmlspecialchars($c) ?>
              </option>
              <?php endforeach ?>
            </select>
            <button id="addCatBtn" type="button"
              class="px-4 bg-secondary text-white rounded-button">
              +Kategori
            </button>
          </div>
          <input id="newCat" name="new_category" placeholder="Kategori baru"
            class="mt-2 w-full border px-4 py-2 rounded-button hidden"/>
        </div>
        <!-- tombol -->
        <div class="flex gap-4 pt-4">
          <a href="Manajemen-Stok-Admin.php"
             class="flex-1 text-center py-2 border rounded-button">Batal</a>
          <button type="submit"
             class="flex-1 text-center py-2 bg-primary text-white rounded-button">
            Simpan
          </button>
        </div>
      </form>
    </main>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', ()=>{
      // date sync
      const dtTxt = document.getElementById('dt_txt'),
            dtInp = document.getElementById('created_at');
      function fmt(d){ return d.toLocaleDateString('en-GB'); }
      dtTxt.value = fmt(new Date(dtInp.value));
      dtInp.addEventListener('change',e=>{
        dtTxt.value = fmt(new Date(e.target.value));
      });

      // dynamic kategori
      const cat = document.getElementById('category'),
            addBtn = document.getElementById('addCatBtn'),
            newCat = document.getElementById('newCat');
      addBtn.addEventListener('click',()=>{
        newCat.classList.toggle('hidden');
        if(!newCat.classList.contains('hidden')) newCat.focus();
      });
      // saat user isi newCat, clear select
      newCat.addEventListener('input',()=>{
        if(newCat.value.trim()!=='') cat.value='';
      });
    });
  </script>
</body>
</html>
