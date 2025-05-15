<?php
$listPatients = $_SERVER['DOCUMENT_ROOT'] . '/views/patient/list/list-patients.view.php';
if (file_exists($listPatients)) {
    include $listPatients;
} else {
    echo "<p style='color: red;'>Error: No se encontró el archivo en '$listPatients'</p>";
}