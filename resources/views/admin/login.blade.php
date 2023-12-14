<!-- resources/views/admin/login.blade.php -->

<form method="POST" action="{{ route('admin.login.submit') }}">
    @csrf
    <label for="email">Email:</label>
    <input type="email" name="email" value="{{ old('email') }}" required autofocus>

    <label for="password">Password:</label>
    <input type="password" name="password" required>
    <input type="text" name="user_type" value="admin" required>

    <button type="submit">Login</button>
</form>
