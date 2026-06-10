<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!current_user()) {
        redirect('login.php');
    }
}

function login(string $email, string $password): bool
{
    global $pdo;

    $statement = $pdo->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
    $statement->execute([$email]);
    $user = $statement->fetch();

    if (!$user) {
        return false;
    }

    $storedHash = (string) $user['password_hash'];
    $usesLegacyHash = hash_equals($storedHash, hash('sha256', $password));
    $validPassword = password_verify($password, $storedHash) || $usesLegacyHash;

    if (!$validPassword) {
        return false;
    }

    if ($usesLegacyHash) {
        $rehash = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $rehash->execute([password_hash($password, PASSWORD_DEFAULT), (int) $user['id']]);
    }

    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];

    return true;
}
