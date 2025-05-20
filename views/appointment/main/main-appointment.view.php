<?php
$listAppointment = $_SERVER['DOCUMENT_ROOT'] . '/views/appointment/list/list-appointments.view.php';
if (file_exists($listAppointment)) {
    include $listAppointment;
} else {
    echo "<p style='color: red;'>Error: No se encontró el archivo en '$listAppointment'</p>";
}