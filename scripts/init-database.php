<?php

declare(strict_types=1);

define('DB_CONFIG_SKIP_CONNECT', true);

require_once __DIR__ . '/../config/database.php';

$waitSeconds = max(0, (int) (db_env(['DB_WAIT_SECONDS'], '30') ?? '30'));
$deadline = time() + $waitSeconds;

while (true) {
    try {
        $pdo = connect_database(database_settings());
        break;
    } catch (PDOException $error) {
        if (time() >= $deadline) {
            fwrite(STDERR, 'Database is not reachable: ' . $error->getMessage() . PHP_EOL);
            exit(1);
        }

        sleep(2);
    }
}

$schemaPath = __DIR__ . '/../database/schema.sql';
$schema = file_get_contents($schemaPath);
if ($schema === false) {
    fwrite(STDERR, 'Cannot read database/schema.sql' . PHP_EOL);
    exit(1);
}

$statements = preg_split('/;\s*(?:\r?\n|$)/', $schema);
if ($statements === false) {
    fwrite(STDERR, 'Cannot parse database/schema.sql' . PHP_EOL);
    exit(1);
}

foreach ($statements as $statement) {
    $statement = trim($statement);
    if ($statement !== '') {
        $pdo->exec($statement);
    }
}

$adminPassword = db_env(['ADMIN_PASSWORD']);
if ($adminPassword !== null && $adminPassword !== '') {
    $adminEmail = db_env(['ADMIN_EMAIL'], 'admin@example.com') ?? 'admin@example.com';
    $adminName = db_env(['ADMIN_NAME'], 'ผู้ดูแลระบบ') ?? 'ผู้ดูแลระบบ';

    $statement = $pdo->prepare('
        INSERT INTO users (name, email, password_hash, role)
        VALUES (?, ?, ?, "admin")
        ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            password_hash = VALUES(password_hash),
            role = VALUES(role)
    ');
    $statement->execute([$adminName, $adminEmail, password_hash($adminPassword, PASSWORD_DEFAULT)]);
}

fwrite(STDOUT, 'Database schema is ready.' . PHP_EOL);
