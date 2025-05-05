<form method="POST" action="/akun_superadmin">
    @csrf

    <label>Nama Lengkap:</label><br>
    <input type="text" name="nama_lengkap" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Daftar Super Admin</button>
</form>