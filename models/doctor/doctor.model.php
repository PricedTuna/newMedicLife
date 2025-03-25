<?php
function createDoctor($email, $password) {
    // Esto es solo un ejemplo hardcodeado:
    $usuarios = [
        'admin@lifemedic.com' => '1234',
        'usuario@lifemedic.com' => 'abcd'
    ];

    return isset($usuarios[$email]) && $usuarios[$email] === $password;
}
