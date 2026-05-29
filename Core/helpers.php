<?php

/**
 * Global helper functions for views and application code.
 *
 * Loaded automatically via composer autoload "files".
 */

if (!function_exists('e')) {
    /**
     * HTML-escape a value for safe output in views.
     * Returns an empty string for null.
     */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Render a hidden CSRF input field.
     */
    function csrf_field(): string
    {
        $token = \app\Middlewares\CsrfMiddleware::token();
        return sprintf(
            '<input type="hidden" name="_csrf" value="%s">',
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
        );
    }
}

if (!function_exists('old')) {
    /**
     * Return the previously submitted value for a field (from session flash).
     */
    function old(string $field, mixed $default = ''): string
    {
        $old = \app\Core\Session::get('_old_input');
        if (is_array($old) && array_key_exists($field, $old)) {
            return e($old[$field]);
        }
        return e($default);
    }
}

if (!function_exists('url')) {
    /**
     * Normalise a path to an absolute URL starting with /.
     */
    function url(string $path): string
    {
        return '/' . ltrim($path, '/');
    }
}
