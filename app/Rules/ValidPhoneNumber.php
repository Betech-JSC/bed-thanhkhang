<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

class ValidPhoneNumber implements InvokableRule
{
    private const FORBIDDEN_VN_SEQUENTIAL_TAILS = [
        '12345678',
        '23456789',
        '98765432',
        '87654321',
        '76543210',
    ];

    protected ?string $locale;

    public function __construct(?string $locale = null)
    {
        $this->locale = $locale;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail): void
    {
        $result = self::validateValue($value, $this->locale);

        if (!$result['valid'] && $result['message'] !== null) {
            $fail($result['message']);
        }
    }

    /**
     * Validate a raw phone number consistently with Frontend lib/validator.js.
     *
     * @return array{valid: bool, message: ?string, normalized: ?string}
     */
    public static function validateValue(mixed $raw, ?string $locale = null): array
    {
        $resolvedLocale = $locale ?? (function_exists('current_locale') ? current_locale() : app()->getLocale());
        $isEn = $resolvedLocale === 'en';

        if ($raw !== null && !is_string($raw) && !is_int($raw) && !is_float($raw)) {
            return self::fail(
                $isEn
                    ? 'Please enter a valid Vietnam or international phone number.'
                    : 'Số điện thoại không hợp lệ.'
            );
        }

        $trimmed = trim((string) ($raw ?? ''));

        if ($trimmed === '') {
            return self::fail(
                $isEn
                    ? 'Please enter your phone number.'
                    : 'Vui lòng nhập số điện thoại.'
            );
        }

        // Reject letters, disallowed characters, or misplaced/multiple '+' BEFORE extracting digits.
        if (
            preg_match('/\p{L}/u', $trimmed) === 1
            || preg_match('/^[0-9+\s\-\.\(\)]+$/u', $trimmed) !== 1
            || strpos($trimmed, '+', 1) !== false
        ) {
            return self::fail(
                $isEn
                    ? 'Phone number must contain only digits and valid separators (+, spaces, -, ., parentheses).'
                    : 'Số điện thoại chỉ được chứa chữ số và ký tự hợp lệ (+, khoảng trắng, -, ., dấu ngoặc).'
            );
        }

        $digits = preg_replace('/\D/', '', $trimmed) ?? '';
        $hasPlus = str_starts_with($trimmed, '+');

        if ($digits === '') {
            return self::fail(
                $isEn
                    ? 'Please enter a valid Vietnam or international phone number.'
                    : 'Số điện thoại không hợp lệ.'
            );
        }

        $isVn = (!$hasPlus && (str_starts_with($digits, '0') || str_starts_with($digits, '84')))
            || ($hasPlus && str_starts_with($digits, '84'));
        $vnLocal = null;

        if ($isVn) {
            $vnLocal = str_starts_with($digits, '84')
                ? '0' . substr($digits, 2)
                : $digits;

            $isValidVnMobile = preg_match('/^0[35789]\d{8}$/', $vnLocal) === 1;
            $isValidVnLandline = preg_match('/^02\d{8,9}$/', $vnLocal) === 1;

            if (!$isValidVnMobile && !$isValidVnLandline) {
                return self::fail(
                    $isEn
                        ? 'Please enter a valid Vietnam (e.g. 0901234567) or international (+...) phone number.'
                        : 'Số điện thoại không đúng định dạng Việt Nam hoặc quốc tế hợp lệ.'
                );
            }
        } elseif ($hasPlus && !str_starts_with($digits, '84')) {
            if (preg_match('/^[1-9]\d{7,14}$/', $digits) !== 1) {
                return self::fail(
                    $isEn
                        ? 'Please enter a valid international phone number (8-15 digits after +).'
                        : 'Số điện thoại quốc tế không hợp lệ (cần 8-15 chữ số sau dấu +).'
                );
            }
        } else {
            return self::fail(
                $isEn
                    ? 'Please enter a valid Vietnam (e.g. 0901234567) or international (+...) phone number.'
                    : 'Số điện thoại không đúng định dạng Việt Nam hoặc quốc tế hợp lệ.'
            );
        }

        if (self::isFakeOrSpamNumber($digits, $vnLocal)) {
            return self::fail(
                $isEn
                    ? 'Please enter a real, valid phone number.'
                    : 'Vui lòng nhập số điện thoại thật hợp lệ (không sử dụng số lặp hoặc dãy số tuần tự).'
            );
        }

        return [
            'valid' => true,
            'message' => null,
            'normalized' => $hasPlus ? '+' . $digits : $vnLocal,
        ];
    }

    private static function isFakeOrSpamNumber(string $digits, ?string $vnLocal): bool
    {
        if (preg_match('/^(\d)\1+$/', $digits) === 1) {
            return true;
        }

        if ($vnLocal !== null && preg_match('/^(\d)\1+$/', $vnLocal) === 1) {
            return true;
        }

        if ($vnLocal !== null) {
            $tail2 = substr($vnLocal, 2);
            $tail3 = substr($vnLocal, 3);

            if (
                ($tail2 !== '' && preg_match('/^(\d)\1+$/', $tail2) === 1)
                || ($tail3 !== '' && preg_match('/^(\d)\1+$/', $tail3) === 1)
            ) {
                return true;
            }
        }

        if (preg_match('/(\d)\1{5,}/', $digits) === 1) {
            return true;
        }

        if ($vnLocal !== null && preg_match('/(\d)\1{5,}/', $vnLocal) === 1) {
            return true;
        }

        if (self::hasSequentialRun($digits, 9) || self::hasSequentialRunAtStart($digits, 8)) {
            return true;
        }

        if ($vnLocal !== null) {
            if (self::hasSequentialRun($vnLocal, 9) || self::hasSequentialRunAtStart($vnLocal, 8)) {
                return true;
            }

            if (
                preg_match('/^0[35789]\d{8}$/', $vnLocal) === 1
                && in_array(substr($vnLocal, 2), self::FORBIDDEN_VN_SEQUENTIAL_TAILS, true)
            ) {
                return true;
            }
        }

        return false;
    }

    private static function hasSequentialRun(string $value, int $minLength): bool
    {
        $length = strlen($value);
        if ($length < $minLength) {
            return false;
        }

        $asc = 1;
        $desc = 1;

        for ($i = 1; $i < $length; $i++) {
            $diff = (int) $value[$i] - (int) $value[$i - 1];

            if ($diff === 1) {
                $asc++;
                $desc = 1;
            } elseif ($diff === -1) {
                $desc++;
                $asc = 1;
            } else {
                $asc = 1;
                $desc = 1;
            }

            if ($asc >= $minLength || $desc >= $minLength) {
                return true;
            }
        }

        return false;
    }

    private static function hasSequentialRunAtStart(string $value, int $minLength): bool
    {
        $length = strlen($value);
        if ($length < $minLength) {
            return false;
        }

        $step = (int) $value[1] - (int) $value[0];
        if ($step !== 1 && $step !== -1) {
            return false;
        }

        for ($i = 2; $i < $minLength; $i++) {
            if (((int) $value[$i] - (int) $value[$i - 1]) !== $step) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array{valid: false, message: string, normalized: null}
     */
    private static function fail(string $message): array
    {
        return [
            'valid' => false,
            'message' => $message,
            'normalized' => null,
        ];
    }
}
