<form method="POST" action="/register">
    @csrf
    <label>Nama:</label><br>
    <input type="text" name="name"><br><br>

    <label>Email:</label><br>
    <input type="email" name="email"><br><br>

    <button type="submit">Daftar</button>
</form>