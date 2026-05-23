<?php
// Load local environment variables if testing locally (this file is ignored by Git)
if (file_exists(__DIR__ . '/local_env.php')) {
    require_once(__DIR__ . '/local_env.php');
}

// Fetch credentials from environment variables (Vercel sets these automatically)
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$dbname = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: 4000;

// The password must come from the environment.
$password = getenv('DB_PASS');

// Create mysqli connection (with SSL support for TiDB)
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
mysqli_real_connect($conn, $host, $user, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);

if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}
?>