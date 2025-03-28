<div class="login-container">
    <h2>Iniciar sesión</h2>
    <?php if (isset($_GET['error'])): ?>
        <div style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>
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