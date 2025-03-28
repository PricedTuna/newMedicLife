<?php
$listDoctors = $_SERVER['DOCUMENT_ROOT'] . '/views/doctor/list/list-doctors.view.php';
if (file_exists($listDoctors)) {
    include $listDoctors;
} else {
    echo "<p style='color: red;'>Error: No se encontró el archivo en '$listDoctors'</p>";
}
