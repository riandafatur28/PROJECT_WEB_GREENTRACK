<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
    <h1>Register</h1>
    <form method="POST" action="/register">
        @csrf
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
