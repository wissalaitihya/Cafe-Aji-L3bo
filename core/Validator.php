<?php

namespace Core;

/**
 * Backend validation — mirrors frontend HTML5/JS rules so bypassing
 * the browser never bypasses the checks (OWASP: validate both sides).
 */
class Validator
{
    public const CATEGORIES = ['social_deduction', 'party', 'cooperative', 'team', 'trivia', 'other'];
    public const DIFFICULTIES = ['easy', 'medium', 'hard'];
    public const GAME_STATUS = ['available', 'in_use'];
    public const RESERVATION_STATUS = ['pending', 'confirmed', 'cancelled'];
    public const TABLE_STATUS = ['free', 'occupied'];

    public static function email(string $email): ?string
    {
        $email = trim(mb_substr($email, 0, 100));
        if ($email === '' || strlen($email) > 100) {
            return 'Email is required.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Invalid email address.';
        }
        return null;
    }

    public static function name(string $name, int $max = 40): ?string
    {
        $name = trim($name);
        if ($name === '') {
            return 'Name is required.';
        }
        if (mb_strlen($name) > $max || mb_strlen($name) < 2) {
            return "Name must be 2–{$max} characters.";
        }
        return null;
    }

    public static function phone(string $phone): ?string
    {
        $phone = trim($phone);
        if ($phone === '') {
            return null; // optional
        }
        if (!preg_match('/^[+0-9][0-9\s.\-]{5,14}$/', $phone)) {
            return 'Invalid phone number.';
        }
        return null;
    }

    public static function password(string $password): ?string
    {
        if (strlen($password) < 6) {
            return 'Password must be at least 6 characters.';
        }
        if (strlen($password) > 255) {
            return 'Password is too long.';
        }
        return null;
    }

    public static function date(string $date, bool $noPast = true): ?string
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return 'Invalid date format (YYYY-MM-DD).';
        }
        [$y, $m, $d] = array_map('intval', explode('-', $date));
        if (!checkdate($m, $d, $y)) {
            return 'Invalid calendar date.';
        }
        if ($noPast && $date < date('Y-m-d')) {
            return 'Date cannot be in the past.';
        }
        return null;
    }

    public static function time(string $time): ?string
    {
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $time)) {
            return 'Invalid time format (HH:MM).';
        }
        return null;
    }

    public static function slot(string $date, string $start, string $end): ?string
    {
        if (($e = self::date($date)) !== null) {
            return $e;
        }
        if (($e = self::time($start)) !== null) {
            return $e;
        }
        if (($e = self::time($end)) !== null) {
            return $e;
        }
        $s = strtotime("{$date} {$start}");
        $eTs = strtotime("{$date} {$end}");
        if ($s === false || $eTs === false) {
            return 'Invalid date/time.';
        }
        if ($eTs - $s < 1800) {
            return 'Booking must be at least 30 minutes.';
        }
        if ($eTs - $s > 12 * 3600) {
            return 'Booking cannot exceed 12 hours.';
        }
        return null;
    }

    public static function enum(string $value, array $allowed, string $field = 'value'): ?string
    {
        if (!in_array($value, $allowed, true)) {
            return "Invalid {$field}.";
        }
        return null;
    }

    public static function intRange(mixed $value, int $min, int $max, string $field = 'value'): ?string
    {
        $v = filter_var($value, FILTER_VALIDATE_INT);
        if ($v === false || $v < $min || $v > $max) {
            return "Invalid {$field} (must be {$min}–{$max}).";
        }
        return null;
    }

    public static function searchQuery(string $q): string
    {
        $q = Sanitizer::str($q, 100);
        // Drop SQL wildcard abuse but keep readable search
        return trim($q);
    }
}
