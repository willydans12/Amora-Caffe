

// menu.js
document.addEventListener('DOMContentLoaded', () => {
  // 1. STATE & ELEMEN UTAMA
  const searchInput   = document.getElementById('searchInput');
  const categoryBtns  = document.querySelectorAll('.category-btn');
  const minInput      = document.getElementById('minPrice');
  const maxInput      = document.getElementById('maxPrice');
  const sortSelect    = document.getElementById('sortSelect');
  const grid          = document.getElementById('productGrid');
  const cardsCols     = grid
    ? Array.from(grid.querySelectorAll('.product-card'))
        .map(card => card.closest('.col-md-6, .col-lg-4'))
    : [];

  // Offcanvas & Keranjang
  const cartBadges   = document.querySelectorAll('.cart-badge');
  const cartItems    = document.getElementById('cartItems');
  const subtotalEl   = document.getElementById('subtotalText');
  const taxEl        = document.getElementById('taxText');
  const totalEl      = document.getElementById('totalText');
  const cartFloatBtn = document.querySelector('.cart-float');
  const offcanvasEl  = document.getElementById('cartOffcanvas');

  let currentCategory = 'all';
  let minPrice        = 0;
  let maxPrice        = Infinity;
  let sortBy          = sortSelect?.value || 'date_desc';
  let searchQuery     = '';

  // Format angka ke Rupiah
  const fmtRupiah = x => 'Rp ' + x.toLocaleString('id-ID');

  // 2. FILTER & SORT
  function applyFilters() {
    if (!grid) return; // Jika grid tidak ada, lewati
    let filtered = cardsCols.filter(col => {
      const c        = col.querySelector('.product-card');
      const price    = +c.dataset.price;
      const category = c.dataset.category;
      const name     = c.querySelector('.card-title').textContent.toLowerCase();
      return price >= minPrice
          && price <= maxPrice
          && (currentCategory === 'all' || category === currentCategory)
          && name.includes(searchQuery);
    });

    filtered.sort((a, b) => {
      const A = a.querySelector('.product-card');
      const B = b.querySelector('.product-card');
      const pA = +A.dataset.price,  pB = +B.dataset.price;
      const rA = +A.dataset.rating, rB = +B.dataset.rating;
      const dA = new Date(A.dataset.date), dB = new Date(B.dataset.date);
      switch (sortBy) {
        case 'price_asc':   return pA - pB;
        case 'price_desc':  return pB - pA;
        case 'rating_desc': return rB - rA;
        case 'date_asc':    return dA - dB;
        default:            return dB - dA;
      }
    });

    // Render ulang grid
    grid.innerHTML = '';
    filtered.forEach(col => grid.appendChild(col));
  }

  if (searchInput) {
    searchInput.addEventListener('input', () => {
      searchQuery = searchInput.value.trim().toLowerCase();
      applyFilters();
    });
  }
  categoryBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      categoryBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentCategory = btn.dataset.cat || 'all';
      applyFilters();
    });
  });
  if (minInput) {
    minInput.addEventListener('input', () => {
      const v = parseFloat(minInput.value);
      minPrice = isNaN(v) ? 0 : v;
      applyFilters();
    });
  }
  if (maxInput) {
    maxInput.addEventListener('input', () => {
      const v = parseFloat(maxInput.value);
      maxPrice = isNaN(v) ? Infinity : v;
      applyFilters();
    });
  }
  if (sortSelect) {
    sortSelect.addEventListener('change', () => {
      sortBy = sortSelect.value;
      applyFilters();
    });
  }

  // 3. AMBIL & RENDER KERANJANG DARI SERVER
  function loadCartFromServer() {
    fetch('get_cart.php')
      .then(res => res.json())
      .then(json => {
        if (json.status === 'success') {
          const items = json.items;
          renderCartItems(items);
          updateBadges(items);
          syncGridQtyInputs(items);
        }
      })
      .catch(err => console.error('Error loadCart:', err));
  }

  function renderCartItems(items) {
    if (!cartItems) return;
    cartItems.innerHTML = '';
    let subtotal = 0, totalQty = 0;

    items.forEach(item => {
      subtotal += parseFloat(item.total_price);
      totalQty += parseInt(item.quantity);

      const div = document.createElement('div');
      div.className = 'd-flex align-items-center mb-3';
      div.innerHTML = `
        <img
          src="${item.image_url}"
          class="rounded me-2"
          style="width:60px; height:60px; object-fit:cover;"
          alt="${item.product_name}"
        />
        <div class="flex-grow-1">
          <div class="fw-bold">${item.product_name}</div>
          <div class="input-group input-group-sm mt-1" style="width:110px;">
            <button class="btn btn-outline-secondary btn-sm btn-decr" data-id="${item.product_id}">−</button>
            <input class="form-control text-center" value="${item.quantity}" readonly>
            <button class="btn btn-outline-secondary btn-sm btn-incr" data-id="${item.product_id}">+</button>
          </div>
        </div>
        <div class="text-end">
          <div class="fw-bold">${fmtRupiah(item.total_price)}</div>
          <button class="btn btn-link btn-sm text-danger btn-remove" data-id="${item.product_id}">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      `;

      // Event untuk tombol + di offcanvas
      div.querySelector('.btn-incr').onclick = e => {
        const pid = e.currentTarget.dataset.id;
        adjustCartOnServer(pid, 'add');
      };
      // Event untuk tombol - di offcanvas
      div.querySelector('.btn-decr').onclick = e => {
        const pid = e.currentTarget.dataset.id;
        adjustCartOnServer(pid, 'remove');
      };
      // Event untuk tombol hapus (removeAll)
      div.querySelector('.btn-remove').onclick = e => {
        const pid = e.currentTarget.dataset.id;
        adjustCartOnServer(pid, 'removeAll');
      };

      cartItems.appendChild(div);
    });

    const tax = Math.round(subtotal * 0.10);
    subtotalEl.textContent = fmtRupiah(subtotal);
    taxEl.textContent      = fmtRupiah(tax);
    totalEl.textContent    = fmtRupiah(subtotal + tax);
  }

  function updateBadges(items) {
    let totalQty = 0;
    items.forEach(item => totalQty += parseInt(item.quantity));
    cartBadges.forEach(b => b.textContent = totalQty);
  }

  function syncGridQtyInputs(items) {
    const qtyMap = {};
    items.forEach(item => {
      qtyMap[item.product_id] = parseInt(item.quantity);
    });
    if (!grid) return;
    grid.querySelectorAll('.product-card').forEach(card => {
      const id    = card.dataset.id;
      const input = card.querySelector('.qty-input');
      input.value  = qtyMap[id] || 0;
    });
  }

  // 4. AJAX Tambah/Kurangi/Hapus
  function adjustCartOnServer(productId, action) {
    const urlAdd    = 'add_to_cart.php';
    const urlRemove = 'remove_from_cart.php';

    if (action === 'add') {
      // Ambil elemen kartu produk
      const card  = document.querySelector(`.product-card[data-id="${productId}"]`);
      if (!card) return;
      const name  = card.querySelector('.card-title').firstChild.textContent.trim();
      const price = +card.dataset.price;

      const data = new URLSearchParams();
      data.append('product_id', productId);
      data.append('product_name', name);
      data.append('unit_price', price);

      fetch(urlAdd, {
        method: 'POST',
        body: data
      })
      .then(res => res.json())
      .then(json => {
        if (json.status === 'success') {
          loadCartFromServer();
        } else {
          alert('Gagal menambah ke keranjang: ' + json.message);
        }
      })
      .catch(err => console.error('Fetch add_to_cart error:', err));

    } else if (action === 'remove') {
      const data = new URLSearchParams();
      data.append('product_id', productId);

      fetch(urlRemove, {
        method: 'POST',
        body: data
      })
      .then(res => res.json())
      .then(json => {
        if (json.status === 'success') {
          loadCartFromServer();
        } else {
          console.error('Error remove:', json.message);
        }
      })
      .catch(err => console.error('Fetch remove_from_cart error:', err));

    } else if (action === 'removeAll') {
      // Menghapus semua quantity: kirim remove berulang kali
      function loopDelete() {
        const data2 = new URLSearchParams();
        data2.append('product_id', productId);
        fetch(urlRemove, {
          method: 'POST',
          body: data2
        })
        .then(res => res.json())
        .then(json => {
          if (json.status === 'success') {
            loadCartFromServer();
            loopDelete(); // ulangi hingga tidak ada baris
          } else {
            loadCartFromServer();
          }
        })
        .catch(err => console.error('Fetch removeAll error:', err));
      }
      loopDelete();
    }
  }

  // 5. PASANG EVENT LISTENER “+” / “−” DI GRID
  function attachGridListeners() {
    if (!grid) return;
    grid.querySelectorAll('.product-card').forEach(card => {
      const id    = card.dataset.id;
      const plus  = card.querySelector('.btn-plus');
      const minus = card.querySelector('.btn-minus');

      plus.addEventListener('click', e => {
        e.stopPropagation();
        adjustCartOnServer(id, 'add');
      });
      minus.addEventListener('click', e => {
        e.stopPropagation();
        adjustCartOnServer(id, 'remove');
      });
    });
  }

  // 6. INISIALISASI AWAL
  applyFilters();
  attachGridListeners();
  loadCartFromServer();
});
