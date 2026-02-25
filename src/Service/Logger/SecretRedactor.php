<?php

namespace App\Service\Logger;

class SecretRedactor
{
    const FORBIDDEN = ['x-auth-token', 'x-subject-token', 'password', 'cloud_init', 'secret', 'token', 'signature', 'authorization'];

    public static function redact($in)
    {
        if (!$in) {
            return null;
        }

        if (is_object($in)) {
            throw new \InvalidArgumentException('Unable to analyze object');
        }

        if (!is_array($in)) {
            return $in;
        }

        $out = [];

        foreach ($in as $key => $val) {
            $needle = $key;
            if (is_string($needle)) {
                $needle = strtolower($needle);
            }

            if (!is_numeric($needle) && in_array($needle, self::FORBIDDEN)) {
                $out[$key] = '[REDACTED]';
            } else {
                $out[$key] = self::redact($val);
            }
        }

        return $out;
    }
}
