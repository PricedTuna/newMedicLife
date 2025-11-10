<?php
// This file redirects to the update-medication.view.php with the type parameter set to 'type'
// to handle medication type updates

// Get the ID from the URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Redirect to the correct URL
header("Location: /views/medications/update/update-medication.view.php?id=$id&type=type");
exit();