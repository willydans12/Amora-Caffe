document.addEventListener('DOMContentLoaded', () => {
  const categoryBtns = document.querySelectorAll('.category-btn');
  const minInput = document.querySelector('input[placeholder="Min"]');
  const maxInput = document.querySelector('input[placeholder="Max"]');
  const sortSelect = document.querySelector('.form-select-sm');
  const container = document.querySelector('.col-lg-9 .row');
  const cards = Array.from(document.querySelectorAll('.product-card'));

  let currentCategory = 'all';
  let minPrice = 0;
  let maxPrice = Infinity;
  let sortBy = 'Terbaru';

  function applyFilters() {
    let filtered = cards.filter(card => {
      const price = parseFloat(card.dataset.price || '0');
      const category = card.dataset.category || '';
      return (
        price >= minPrice &&
        price <= maxPrice &&
        (currentCategory === 'all' || category === currentCategory)
      );
    });

    filtered.sort((a, b) => {
      const priceA = parseFloat(a.dataset.price || '0');
      const priceB = parseFloat(b.dataset.price || '0');
      const ratingA = parseFloat(a.dataset.rating || '0');
      const ratingB = parseFloat(b.dataset.rating || '0');
      const dateA = new Date(a.dataset.date || '1970-01-01');
      const dateB = new Date(b.dataset.date || '1970-01-01');

      switch (sortBy) {
        case 'Harga Terendah':
          return priceA - priceB;
        case 'Harga Tertinggi':
          return priceB - priceA;
        case 'Rating':
          return ratingB - ratingA;
        case 'Terbaru':
        default:
          return dateB - dateA;
      }
    });

    container.innerHTML = '';
    filtered.forEach(card => container.appendChild(card.parentElement));
  }

  categoryBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      categoryBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const key = btn.textContent.trim().toLowerCase().replace(/\s+/g, '-');
      currentCategory = key === 'semua-menu' ? 'all' : key;
      applyFilters();
    });
  });

  minInput.addEventListener('input', () => {
    const val = parseFloat(minInput.value);
    minPrice = isNaN(val) ? 0 : val;
    applyFilters();
  });

  maxInput.addEventListener('input', () => {
    const val = parseFloat(maxInput.value);
    maxPrice = isNaN(val) ? Infinity : val;
    applyFilters();
  });

  sortSelect.addEventListener('change', () => {
    sortBy = sortSelect.value.trim();
    applyFilters();
  });

  document.querySelectorAll('.qty-group').forEach(group => {
    const minus = group.querySelector('.btn-minus');
    const plus = group.querySelector('.btn-plus');
    const input = group.querySelector('.qty-input');

    input.value = input.value || 0;

    minus.addEventListener('click', e => {
      e.stopPropagation();
      const val = parseInt(input.value) || 0;
      if (val > 0) input.value = val - 1;
    });

    plus.addEventListener('click', e => {
      e.stopPropagation();
      const val = parseInt(input.value) || 0;
      input.value = val + 1;
    });
  });

  applyFilters();
});
