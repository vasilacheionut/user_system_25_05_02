<div class="form-container">
    <form class="form-content" method="POST" action="login.php">
        <h2>Login</h2>

        <!-- CSRF token ascuns -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

        <input type="email" name="email" placeholder="Email" required /><br />
        <input type="password" name="password" placeholder="Password" required /><br />
        <button type="submit">Login</button>
    </form>
</div>
