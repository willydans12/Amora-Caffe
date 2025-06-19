 id="formValidation">
document.addEventListener('DOMContentLoaded', function() {
const form = document.querySelector('form');
const continueButton = form.querySelector('button[type="button"]');
continueButton.addEventListener('click', function() {
const firstName = document.getElementById('firstName').value;
const lastName = document.getElementById('lastName').value;
const address = document.getElementById('address').value;
const phone = document.getElementById('phone').value;
const email = document.getElementById('email').value;
if (!firstName || !lastName || !address || !phone || !email) {
alert('Mohon lengkapi semua informasi pengiriman');
return;
}
// Validate email format
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
if (!emailRegex.test(email)) {
alert('Format email tidak valid');
return;
}
// Validate phone number (Indonesian format)
const phoneRegex = /^(\+62|62|0)8[1-9][0-9]{6,9}$/;
if (!phoneRegex.test(phone)) {
alert('Format nomor telepon tidak valid');
return;
}
alert('Mengarahkan ke halaman pembayaran...');
// Here you would redirect to payment page
});
});

 id="promoCode">
document.addEventListener('DOMContentLoaded', function() {
const promoForm = document.querySelector('footer + script').previousElementSibling.querySelector('.flex');
const promoInput = promoForm.querySelector('input');
const promoButton = promoForm.querySelector('button');
promoButton.addEventListener('click', function() {
const promoCode = promoInput.value.trim();
if (!promoCode) {
alert('Masukkan kode promo');
return;
}
// Example promo codes
const validPromoCodes = {
'WELCOME10': 10,
'KOPI25': 25,
'FREESHIP': 15000
};
if (validPromoCodes[promoCode] !== undefined) {
alert(`Kode promo berhasil diterapkan! Anda mendapatkan diskon.`);
promoInput.value = '';
} else {
alert('Kode promo tidak valid');
}
});
});
