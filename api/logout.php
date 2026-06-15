<?php
require_once '../config.php';
session_destroy();
responder(['success' => true]);
?>