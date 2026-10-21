<?php require 'includes/config.php'; $_SESSION = []; session_destroy(); header('Location: ' . url('index.php')); exit;
