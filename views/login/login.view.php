<div class="login-container"> 
    <h2>Iniciar sesión</h2>
    <?php
    if (isset($_GET['error'])) {
        echo "<p style='color:red;'>Credenciales incorrectas</p>";
    }
    ?>
    <form id="loginForm" action="controllers/auth/login.controller.php" method="POST">
        <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" placeholder="Correo electrónico" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="Contraseña" required autocomplete="off">
        </div>
        <button type="submit" name="login">Ingresar</button>
    </form>   
</div>
