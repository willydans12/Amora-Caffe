
    
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              primary: "#593b2b",
              secondary: "#10B981",
            },
            borderRadius: {
              button: "8px",
            },
          },
        },
      };
    

      document.addEventListener("DOMContentLoaded", function () {
        const paginationButtons = document.querySelectorAll("nav button");
        paginationButtons.forEach((button) => {
          button.addEventListener("click", function () {
            paginationButtons.forEach((btn) =>
              btn.classList.remove("bg-primary", "text-white")
            );
            if (!this.querySelector("i")) {
              this.classList.add("bg-primary", "text-white");
            }
          });
        });
      });

      const dataPesanan = {
        pesanan1: {
          tanggal: "1 Juni 2025",
          waktu: "13:45 WIB",
          status: "Diproses",
          jenis: "Dine-in",
          total: "Rp 78.500",
          item: ["Cappuccino", "Croissant"],
        },
        pesanan2: {
          tanggal: "28 Mei 2025",
          waktu: "10:15 WIB",
          status: "Dikirim",
          jenis: "Take-away",
          total: "Rp 124.000",
          item: ["Latte", "Avocado Toast", "Chocolate Cake"],
        },
        pesanan3: {
          tanggal: "22 Mei 2025",
          waktu: "16:30 WIB",
          status: "Selesai",
          jenis: "Dine-in",
          total: "Rp 95.000",
          item: ["Matcha Latte", "Tiramisu"],
        },
      };

      function tampilkanDetail(id) {
        const data = dataPesanan[id];
        const isi = `
          <p><strong>Tanggal:</strong> ${data.tanggal}</p>
          <p><strong>Waktu:</strong> ${data.waktu}</p>
          <p><strong>Status:</strong> ${data.status}</p>
          <p><strong>Jenis Pesanan:</strong> ${data.jenis}</p>
          <p><strong>Total:</strong> ${data.total}</p>
          <p><strong>Item:</strong></p>
          <ul class="list-disc list-inside">
            ${data.item.map((item) => `<li>${item}</li>`).join("")}
          </ul>
        `;
        document.getElementById("isiDetail").innerHTML = isi;
        document.getElementById("detailPesanan").classList.remove("hidden");
      }

      function tutupDetail() {
        document.getElementById("detailPesanan").classList.add("hidden");
      }
    