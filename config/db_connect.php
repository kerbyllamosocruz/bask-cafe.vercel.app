<?php
// Load local environment variables if testing locally (this file is ignored by Git)
if (file_exists(__DIR__ . '/local_env.php')) {
    require_once(__DIR__ . '/local_env.php');
}

// Fetch credentials from environment variables (Vercel sets these automatically)
$host = getenv('DB_HOST') ?: 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$user = getenv('DB_USER') ?: '2tXUvbRqBa3jH7S.root';
$dbname = getenv('DB_NAME') ?: 'bask_db';
$port = getenv('DB_PORT') ?: 4000;

// The password should NEVER have a fallback hardcoded here. It must come from the environment.
$password = getenv('DB_PASS');

// Create mysqli connection (with SSL support for TiDB)
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
mysqli_real_connect($conn, $host, $user, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);

if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}
?>
