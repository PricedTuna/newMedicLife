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
        <form id="loginForm" action="/controllers/auth/login.controller.php" method="POST">
            {if isset($smarty.get.redirect)}
                <input type="hidden" name="redirect" value="{$smarty.get.redirect|escape}">
            {/if}
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" placeholder="Correo electrónico" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" placeholder="Contraseña" required autocomplete="off">
                    <button type="button" id="togglePassword" class="toggle-password">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                          <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                          <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                        </svg>
                    </button>
                </div>
            </div>
            <button type="submit" name="login">Ingresar</button>
        </form>
        <div class="public-links">
            <a href="/views/public/medical_history/public-medical-history.view.php" class="public-link">Consultar Historial Médico</a>
        </div>
    </div>

</body>
</html>
