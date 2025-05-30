document.addEventListener('DOMContentLoaded', () => {
  // Elemen kontrol
  const searchInput = document.getElementById('searchInput');
  const categoryBtns = document.querySelectorAll('.category-btn');
  const minInput = document.getElementById('minPrice');
  const maxInput = document.getElementById('maxPrice');
  const sortSelect = document.getElementById('sortSelect');
  const grid = document.getElementById('productGrid');

  // Ambil semua elemen col yang berisi product-card agar render ulang tetap utuh (beserta modals)
  const cards = Array.from(grid.querySelectorAll('.product-card')).map(card => card.closest('.col-md-6, .col-lg-4'));

  // State
  let currentCategory = 'all';
  let minPrice = 0;
  let maxPrice = Infinity;
  let sortBy = 'date_desc';
  let searchQuery = '';

  function applyFilters() {
    const filtered = cards.filter(col => {
      const card = col.querySelector('.product-card');
      const price = parseFloat(card.dataset.price) || 0;
      const category = card.dataset.category || '';
      const rating = parseFloat(card.dataset.rating) || 0;
      const date = new Date(card.dataset.date);
      const name = card.querySelector('.card-title').textContent.toLowerCase();

      return (
        price >= minPrice &&
        price <= maxPrice &&
        (currentCategory === 'all' || category === currentCategory) &&
        name.includes(searchQuery)
      );
    });

    filtered.sort((a, b) => {
      const cardA = a.querySelector('.product-card');
      const cardB = b.querySelector('.product-card');
      const priceA = parseFloat(cardA.dataset.price) || 0;
      const priceB = parseFloat(cardB.dataset.price) || 0;
      const ratingA = parseFloat(cardA.dataset.rating) || 0;
      const ratingB = parseFloat(cardB.dataset.rating) || 0;
      const dateA = new Date(cardA.dataset.date);
      const dateB = new Date(cardB.dataset.date);

      switch (sortBy) {
        case 'price_asc':   return priceA - priceB;
        case 'price_desc':  return priceB - priceA;
        case 'rating_desc': return ratingB - ratingA;
        case 'date_asc':    return dateA - dateB;
        case 'date_desc':
        default:            return dateB - dateA;
      }
    });

    // Render ulang grid
    grid.innerHTML = '';
    filtered.forEach(col => grid.appendChild(col));
  }

  // Event: Pencarian
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      searchQuery = searchInput.value.trim().toLowerCase();
      applyFilters();
    });
  }

  // Event: Filter Kategori
  categoryBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      categoryBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentCategory = btn.dataset.cat || 'all';  // pastikan pakai data-cat di HTML
      applyFilters();
    });
  });

  // Event: Min & Max harga
  if (minInput) {
    minInput.addEventListener('input', () => {
      const val = parseFloat(minInput.value);
      minPrice = isNaN(val) ? 0 : val;
      applyFilters();
    });
  }

  if (maxInput) {
    maxInput.addEventListener('input', () => {
      const val = parseFloat(maxInput.value);
      maxPrice = isNaN(val) ? Infinity : val;
      applyFilters();
    });
  }

  // Event: Sorting
  if (sortSelect) {
    sortSelect.addEventListener('change', () => {
      sortBy = sortSelect.value;
      applyFilters();
    });
  }

  // Event: Qty Controls
  document.querySelectorAll('.qty-group').forEach(group => {
    const minus = group.querySelector('.btn-minus');
    const plus  = group.querySelector('.btn-plus');
    const input = group.querySelector('.qty-input');

    [minus, plus].forEach(btn =>
      btn.addEventListener('click', e => e.stopPropagation())
    );

    plus.addEventListener('click', () => {
      input.value = (parseInt(input.value) || 0) + 1;
    });

    minus.addEventListener('click', () => {
      const val = parseInt(input.value) || 0;
      if (val > 0) input.value = val - 1;
    });
  });

  // Inisialisasi
  sortBy = sortSelect?.value || 'date_desc';
  applyFilters();
});
