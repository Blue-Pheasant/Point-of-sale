<?php

namespace app\Core;

/**
 * Generates RFC 4122 identifiers.
 *
 * Replaces uniqid() for primary keys: uniqid() is derived from the system
 * clock and is NOT unique under concurrent requests, whereas {@see self::v4()}
 * draws from a cryptographically secure random source.
 *
 * @package app\Core
 */
class Uuid
{
    /**
     * Returns a random (version 4) UUID, e.g. "9b2e5c1a-...-4f3a-...".
     *
     * @return string The 36-character UUID.
     */
    public static function v4(): string
    {
        $bytes = random_bytes(16);

        // Set the version (4) and variant (RFC 4122) bits.
        $bytes[6] = chr((ord($bytes[6]) & 0x0F) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3F) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
