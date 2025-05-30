<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <script src="/views/login/login.app.js" defer></script>
    <link rel="stylesheet" href="login.styles.css"> <!-- Ajusta la ruta si tienes un CSS -->
</head>
<body >

    <div class="login-container">
        <h2>Iniciar sesión</h2>

        {if $error}
            <div style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                {$error|escape}
            </div>
        {/if}

        <div id="form-error" style="display: none; color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;"></div>
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

</body>
</html>
