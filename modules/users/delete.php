<?php
require_once __DIR__ . '/../../config/config.php';

Auth::requireRole('admin');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Verificar que no sea el usuario actual
if ($id === $_SESSION['user_id']) {
    header('Location: index.php?error=cannot_delete_self');
    exit();
}

$user = User::find($id);

if (!$user) {
    header('Location: index.php?error=user_not_found');
    exit();
}

if (User::delete($id)) {
    header('Location: index.php?success=deleted');
    exit();
} else {
    header('Location: index.php?error=delete_failed');
    exit();
}
?>