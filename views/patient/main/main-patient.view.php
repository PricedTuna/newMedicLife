<?php
// Include session controller to protect this route
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
$listPatients = $_SERVER['DOCUMENT_ROOT'] . '/views/patient/list/list-patients.view.php';
if (file_exists($listPatients)) {
    include $listPatients;
} else {
    echo "<p style='color: red;'>Error: No se encontró el archivo en '$listPatients'</p>";
}
