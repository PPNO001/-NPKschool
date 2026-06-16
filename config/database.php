<?php

declare(strict_types=1);

function load_env_file(string $path): void
{
    if (!is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if ($key === '') {
            continue;
        }

        $value = trim($value, "\"'");
        if (getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

function db_env(array $keys, ?string $default = null): ?string
{
    foreach ($keys as $key) {
        $value = getenv($key);
        if ($value === false && isset($_ENV[$key])) {
            $value = $_ENV[$key];
        }
        if ($value === false && isset($_SERVER[$key])) {
            $value = $_SERVER[$key];
        }
        if ($value !== false && $value !== '') {
            return (string) $value;
        }
    }

    return $default;
}

function database_url_settings(?string $url): array
{
    if ($url === null || $url === '') {
        return [];
    }

    $parts = parse_url($url);
    if (!is_array($parts)) {
        return [];
    }

    $scheme = strtolower((string) ($parts['scheme'] ?? 'mysql'));
    if (!in_array($scheme, ['mysql', 'mariadb'], true)) {
        return [];
    }

    parse_str((string) ($parts['query'] ?? ''), $query);
    $database = isset($parts['path']) ? rawurldecode(ltrim((string) $parts['path'], '/')) : null;

    return [
        'host' => isset($parts['host']) ? rawurldecode((string) $parts['host']) : null,
        'port' => isset($parts['port']) ? (string) $parts['port'] : null,
        'name' => $database !== '' ? $database : null,
        'user' => isset($parts['user']) ? rawurldecode((string) $parts['user']) : null,
        'pass' => isset($parts['pass']) ? rawurldecode((string) $parts['pass']) : '',
        'charset' => isset($query['charset']) ? (string) $query['charset'] : null,
    ];
}

function database_settings(): array
{
    $urlSettings = database_url_settings(db_env(['DATABASE_URL', 'MYSQL_URL']));

    return [
        'host' => db_env(['DB_HOST', 'MYSQLHOST'], $urlSettings['host'] ?? '127.0.0.1') ?? '127.0.0.1',
        'port' => db_env(['DB_PORT', 'MYSQLPORT'], $urlSettings['port'] ?? '3306') ?? '3306',
        'name' => db_env(['DB_DATABASE', 'DB_NAME', 'MYSQLDATABASE'], $urlSettings['name'] ?? 'school_management_system') ?? 'school_management_system',
        'user' => db_env(['DB_USERNAME', 'DB_USER', 'MYSQLUSER'], $urlSettings['user'] ?? 'root') ?? 'root',
        'pass' => db_env(['DB_PASSWORD', 'DB_PASS', 'MYSQLPASSWORD'], $urlSettings['pass'] ?? '') ?? '',
        'charset' => db_env(['DB_CHARSET'], $urlSettings['charset'] ?? 'utf8mb4') ?? 'utf8mb4',
    ];
}

function connect_database(array $settings): PDO
{
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $settings['host'],
        $settings['port'],
        $settings['name'],
        $settings['charset']
    );

    return new PDO($dsn, (string) $settings['user'], (string) $settings['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

load_env_file(__DIR__ . '/../.env');

if (!defined('DB_CONFIG_SKIP_CONNECT')) {
    try {
        $pdo = connect_database(database_settings());
    } catch (PDOException $error) {
        error_log('Database connection failed: ' . $error->getMessage());
        http_response_code(500);
        exit('Database connection failed. Please check database environment variables.');
    }
}
