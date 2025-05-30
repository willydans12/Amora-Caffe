// Quantity control script
if (document.querySelectorAll) {
  document.querySelectorAll('.qty-control').forEach(group => {
    const input = group.querySelector('input');
    group.querySelector('.btn-increment').addEventListener('click', () => {
      input.value = parseInt(input.value) + 1;
    });
    group.querySelector('.btn-decrement').addEventListener('click', () => {
      if (parseInt(input.value) > 0) input.value = parseInt(input.value) - 1;
    });
  });
}
