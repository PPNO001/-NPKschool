<?php

declare(strict_types=1);

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header("Location: {$path}");
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Invalid form token.');
    }
}

function status_label(string $status): string
{
    return [
        'present' => 'มาเรียน',
        'late' => 'มาสาย',
        'absent' => 'ขาดเรียน',
        'leave' => 'ลา',
    ][$status] ?? $status;
}

function status_class(string $status): string
{
    return [
        'present' => 'success',
        'late' => 'warning',
        'absent' => 'danger',
        'leave' => 'info',
    ][$status] ?? 'muted';
}
