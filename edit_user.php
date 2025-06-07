<?php
include 'koneksi.php';
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id = $id"));
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card p-4">
    <h3 class="mb-4">✏️ Edit User</h3>
    <form action="proses_edit.php" method="post">
      <input type="hidden" name="id" value="<?= $data['id']; ?>">
      <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="nama" class="form-control" value="<?= $data['nama']; ?>" required>
      </div>
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?= $data['email']; ?>" required>
      </div>
      <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-select">
          <option <?= $data['role'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
          <option <?= $data['role'] == 'Kasir' ? 'selected' : '' ?>>Kasir</option>
          <option <?= $data['role'] == 'Staff' ? 'selected' : '' ?>>Staff</option>
        </select>
      </div>
      <div class="mb-3">
        <label>Alamat</label>
        <textarea name="alamat" class="form-control"><?= $data['alamat']; ?></textarea>
      </div>
      <div class="mb-3">
        <label>Status</label><br>
        <select name="status" class="form-select">
          <option value="1" <?= $data['status'] == 1 ? 'selected' : '' ?>>Aktif</option>
          <option value="0" <?= $data['status'] == 0 ? 'selected' : '' ?>>Nonaktif</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Update</button>
      <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
  </div>
</div>
</body>
</html>

