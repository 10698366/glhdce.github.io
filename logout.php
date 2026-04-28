<?php

//LOGS THE USER OUT AND SENDS THEM BACK TO THE HOME PAGE LOGGED OUT

session_start();
session_unset();
session_destroy();
header("Location: index.php");
exit;

?>