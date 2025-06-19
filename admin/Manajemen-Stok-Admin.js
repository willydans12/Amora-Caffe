// tailwind.config
tailwind.config = {
  theme: {
    extend: {
      colors: { primary: "#6B4226", secondary: "#8B5D33" },
      borderRadius: {
        none: "0px", sm: "4px", DEFAULT: "8px", md: "12px",
        lg: "16px", xl: "20px", "2xl": "24px", "3xl": "32px",
        full: "9999px", button: "8px",
      },
    },
  },
};

document.addEventListener("DOMContentLoaded", function () {
  // --- Ambil elemen-elemen ---
  const kategoriBtn   = document.getElementById("kategoriBtn");
  const kategoriList  = document.getElementById("kategoriList");
  const searchInput   = document.querySelector('header input[placeholder="Cari..."]');
  const table         = document.querySelector("table");
  const tbodyRows     = Array.from(table.querySelectorAll("tbody tr"));
  const chkAll        = document.getElementById("chkAll");
  const checkboxes    = Array.from(table.querySelectorAll('tbody input[type="checkbox"]'));

  // --- Dropdown Kategori ---
  let currentCat = "Semua Kategori";
  kategoriBtn.addEventListener("click", e => {
    e.stopPropagation();
    kategoriList.classList.toggle("hidden");
  });
  kategoriList.querySelectorAll("a").forEach(a => {
    a.addEventListener("click", e => {
      e.preventDefault();
      const txt = a.textContent.trim();
      kategoriBtn.firstChild.textContent = txt + " ";
      currentCat = txt;
      kategoriList.classList.add("hidden");
      filterTable();
    });
  });
  document.addEventListener("click", ev => {
    if (!kategoriBtn.contains(ev.target) && !kategoriList.contains(ev.target)) {
      kategoriList.classList.add("hidden");
    }
  });

  // --- Search Bar ---
  searchInput.addEventListener("input", filterTable);

  // --- Select All Checkbox ---
  chkAll.addEventListener("change", function () {
    checkboxes.forEach(c => c.checked = this.checked);
  });
  checkboxes.forEach(c => {
    c.addEventListener("change", () => {
      const all   = checkboxes.every(c => c.checked);
      const some  = checkboxes.some(c => c.checked);
      chkAll.checked      = all;
      chkAll.indeterminate = some && !all;
    });
  });

  // --- Filter Function ---
  function filterTable() {
    const q = searchInput.value.trim().toLowerCase();
    tbodyRows.forEach(tr => {
      const tds = tr.querySelectorAll("td");
      const nama = tds[1].textContent.trim().toLowerCase();
      const kat  = tds[2].textContent.trim();
      const matchName = nama.includes(q);
      const matchCat  = (currentCat === "Semua Kategori") || (kat === currentCat);
      tr.style.display = (matchName && matchCat) ? "" : "none";
    });
  }

  // Inisialisasi: sembunyikan semua dropdown
  kategoriList.classList.add("hidden");
  filterTable();
});
