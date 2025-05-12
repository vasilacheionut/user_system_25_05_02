<div class="form-container">
    <form class="form-content" method="POST" action="/register.php">
        <h2>Register</h2>
        <!-- $csrf_token Îl pui în formular ca input ascuns -->
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <!-- 
        Acesta se trimite împreună cu celelalte date din formular, dar este invizibil pentru utilizator.
        Il afisam aici ca un test. aceasta linie poate fi stearsa 
        -->
        <input type="text" value="<?php echo $csrf_token; ?>">        
        <input type="email" name="email" placeholder="Email" required /><br />
        <input type="password" name="password" placeholder="Password" required /><br />
        <button type="submit">Register</button>
    </form>
</div>