const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
        datasets: [{
          label: 'Penjualan',
          data: [200, 300, 400, 500, 650],
          borderColor: 'rgba(75, 192, 192, 1)',
          fill: false,
          tension: 0.1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: true }
        }
      }
    });

    const productCtx = document.getElementById('productChart').getContext('2d');
    const productChart = new Chart(productCtx, {
      type: 'doughnut',
      data: {
        labels: ['Arabica Toraja', 'Robusta', 'Liberica', 'Excelsa', 'Lainnya'],
        datasets: [{
          data: [30, 25, 20, 15, 10],
          backgroundColor: [
            '#f97316', '#10b981', '#6366f1', '#f43f5e', '#a3e635'
          ]
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'right' }
        }
      }
    });