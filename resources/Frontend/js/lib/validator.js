"use strict";

const PHONE_ALLOWED_CHARS_RE = /^[0-9+\s\-.()]+$/;
const HAS_LETTER_RE = /[a-zA-Z]|\p{L}/u;
const ALL_SAME_DIGITS_RE = /^(\d)\1+$/;
const SIX_REPEATED_DIGITS_RE = /(\d)\1{5,}/;
const SEVEN_TRAILING_REPEATED_RE = /(\d)\1{6,}$/;
const VN_MOBILE_RE = /^0[35789]\d{8}$/;
const VN_LANDLINE_RE = /^02\d{8,9}$/;
const INTL_DIGITS_RE = /^[1-9]\d{7,14}$/;
const BLOCKED_VN_SUB8 = new Set([
    "12345678",
    "23456789",
    "98765432",
    "87654321",
    "76543210",
]);

const PHONE_ERROR_MESSAGES = {
    vi: {
        required: "Vui lòng nhập số điện thoại.",
        invalid_chars:
            "Số điện thoại không được chứa chữ cái hoặc ký tự không hợp lệ.",
        format:
            "Số điện thoại không hợp lệ (hỗ trợ số VN 10–11 số hoặc quốc tế + 8–15 chữ số).",
        spam:
            "Số điện thoại không hợp lệ (trùng lặp hoặc dãy số tuần tự). Vui lòng nhập số thật.",
    },
    en: {
        required: "Please enter your phone number.",
        invalid_chars:
            "Phone number must not contain letters or invalid characters.",
        format:
            "Please enter a valid Vietnam (10–11 digits) or international (+ with 8–15 digits) phone number.",
        spam:
            "Invalid phone number (repeated or sequential digits). Please enter a real number.",
    },
};

function resolvePhoneLocale(options = {}) {
    if (options && options.isEn) return "en";
    return options && options.locale === "en" ? "en" : "vi";
}

function maxSequentialDigitRun(digitStr) {
    if (!digitStr || digitStr.length < 2) return digitStr ? digitStr.length : 0;
    let maxRun = 1;
    let ascRun = 1;
    let descRun = 1;

    for (let i = 1; i < digitStr.length; i++) {
        const prev = digitStr.charCodeAt(i - 1);
        const curr = digitStr.charCodeAt(i);
        ascRun = curr - prev === 1 ? ascRun + 1 : 1;
        descRun = prev - curr === 1 ? descRun + 1 : 1;
        if (ascRun > maxRun) maxRun = ascRun;
        if (descRun > maxRun) maxRun = descRun;
    }

    return maxRun;
}

function leadingSequentialDigitRun(digitStr) {
    if (!digitStr || digitStr.length < 2) return digitStr ? digitStr.length : 0;
    const step = digitStr.charCodeAt(1) - digitStr.charCodeAt(0);
    if (step !== 1 && step !== -1) return 1;

    let run = 2;
    for (let i = 2; i < digitStr.length; i++) {
        if (digitStr.charCodeAt(i) - digitStr.charCodeAt(i - 1) === step) {
            run++;
        } else {
            break;
        }
    }
    return run;
}

function hasSequentialSpam(digitStr) {
    if (!digitStr || digitStr.length < 8) return false;
    return (
        maxSequentialDigitRun(digitStr) >= 9 ||
        leadingSequentialDigitRun(digitStr) >= 8
    );
}

function isSpamDigitPattern(digits, vnLocal = null) {
    if (digits.length >= 8 && ALL_SAME_DIGITS_RE.test(digits)) {
        return true;
    }

    if (
        SIX_REPEATED_DIGITS_RE.test(digits) ||
        SEVEN_TRAILING_REPEATED_RE.test(digits) ||
        hasSequentialSpam(digits)
    ) {
        return true;
    }

    if (vnLocal) {
        if (
            ALL_SAME_DIGITS_RE.test(vnLocal) ||
            SIX_REPEATED_DIGITS_RE.test(vnLocal) ||
            SEVEN_TRAILING_REPEATED_RE.test(vnLocal) ||
            hasSequentialSpam(vnLocal)
        ) {
            return true;
        }
        const sub2 = vnLocal.slice(2);
        const sub3 = vnLocal.slice(3);
        if (
            (sub2 && ALL_SAME_DIGITS_RE.test(sub2)) ||
            (sub3 && ALL_SAME_DIGITS_RE.test(sub3))
        ) {
            return true;
        }
        if (VN_MOBILE_RE.test(vnLocal) && BLOCKED_VN_SUB8.has(sub2)) {
            return true;
        }
    }

    return false;
}

function buildPhoneResult(valid, reason = null, locale = "vi", normalized = "") {
    const lang = locale === "en" ? "en" : "vi";
    return {
        valid,
        reason,
        message: valid ? "" : PHONE_ERROR_MESSAGES[lang][reason] || PHONE_ERROR_MESSAGES[lang].format,
        normalized,
    };
}

/**
 * Validate a Vietnamese or international phone number consistently with backend ValidPhoneNumber.
 *
 * @param {string|number|null|undefined} rawValue
 * @param {{ locale?: 'vi'|'en', isEn?: boolean, required?: boolean }} [options]
 * @returns {{ valid: boolean, reason: string|null, message: string, normalized: string }}
 */
function validatePhoneNumber(rawValue, options = {}) {
    const locale = resolvePhoneLocale(options);
    const required = options.required !== false;
    const raw = rawValue === null || rawValue === undefined ? "" : String(rawValue);
    const trimmed = raw.trim();

    if (!trimmed) {
        return required
            ? buildPhoneResult(false, "required", locale)
            : buildPhoneResult(true, null, locale, "");
    }

    if (HAS_LETTER_RE.test(trimmed) || !PHONE_ALLOWED_CHARS_RE.test(trimmed)) {
        return buildPhoneResult(false, "invalid_chars", locale);
    }

    const plusMatches = trimmed.match(/\+/g);
    if (plusMatches && (plusMatches.length > 1 || !trimmed.startsWith("+"))) {
        return buildPhoneResult(false, "invalid_chars", locale);
    }

    const hasPlus = trimmed.startsWith("+");
    const digits = trimmed.replace(/\D/g, "");
    if (!digits) {
        return buildPhoneResult(false, "format", locale);
    }

    if (
        digits.length >= 8 &&
        (ALL_SAME_DIGITS_RE.test(digits) ||
            SIX_REPEATED_DIGITS_RE.test(digits) ||
            hasSequentialSpam(digits))
    ) {
        return buildPhoneResult(false, "spam", locale);
    }

    const isVietnamNumber =
        (!hasPlus && (digits.startsWith("0") || digits.startsWith("84"))) ||
        (hasPlus && digits.startsWith("84"));

    if (isVietnamNumber) {
        const vnLocal = digits.startsWith("84")
            ? "0" + digits.slice(2)
            : digits;

        if (!VN_MOBILE_RE.test(vnLocal) && !VN_LANDLINE_RE.test(vnLocal)) {
            return buildPhoneResult(false, "format", locale);
        }

        if (isSpamDigitPattern(digits, vnLocal)) {
            return buildPhoneResult(false, "spam", locale);
        }

        return buildPhoneResult(
            true,
            null,
            locale,
            hasPlus ? `+${digits}` : vnLocal
        );
    }

    if (hasPlus) {
        if (!INTL_DIGITS_RE.test(digits)) {
            return buildPhoneResult(false, "format", locale);
        }

        if (isSpamDigitPattern(digits, null)) {
            return buildPhoneResult(false, "spam", locale);
        }

        return buildPhoneResult(true, null, locale, `+${digits}`);
    }

    return buildPhoneResult(false, "format", locale);
}

/**
 * Boolean convenience wrapper for phone validation.
 *
 * @param {string|number|null|undefined} rawValue
 * @param {{ locale?: 'vi'|'en', isEn?: boolean, required?: boolean }} [options]
 * @returns {boolean}
 */
function isValidPhoneNumber(rawValue, options = {}) {
    return validatePhoneNumber(rawValue, options).valid;
}

function validate(value, rule) {
    const type = rule.split(":")[0];
    const condition = rule.split(":")[1] ?? null;

    switch (type) {
        case "array":
            return Array.isArray(value);

        case "required":
            return !!value;
        case "email":
            const regEx =
                /^[\w.!?#$%&'=~|{}`+*^][\w\-.!?#$%&'=~|{}`+*^]*@((xn--)?[\w]+([-][\w]+)*\.)+[a-z]{2,}$/i;
            const validDomain =
                !value.includes("@gnail.") &&
                !value.includes("@gmail.con") &&
                !value.includes("@gmai.");

            return value ? regEx.test(value) && validDomain : true;

        case "date":
            return (
                new Date(value) !== "Invalid Date" && !isNaN(new Date(value))
            );

        case "string":
            return typeof value === "string";

        case "phone":
            return isValidPhoneNumber(value);

        case "in":
            const conditions = condition.split(",");
            if (Array.isArray(value)) {
                return value.every((element) =>
                    conditions.includes(element.toString())
                );
            } else {
                return value && conditions.includes(value.toString());
            }

        case "integer":
            return !isNaN(value);

        case "min":
            return value.length >= parseInt(condition);

        case "max":
            return value.length <= parseInt(condition);

        default:
            return true;
    }
}

function validateForm(formFields, rules) {
    let errors = {};
    for (const field of Object.entries(formFields)) {
        if (!validateField(field[1], rules[field[0]])) {
            errors[field[0]] = "";
        }
    }
    return errors;
}

function validateField(value, rules) {
    if (rules === undefined) return true;

    let fieldIsValid = true;

    for (const rule of rules.split("|")) {
        const valid = validate(value, rule);
        if (!valid) {
            fieldIsValid = false;
            break;
        }
    }

    return fieldIsValid;
}

export { validateForm, validateField, validatePhoneNumber, isValidPhoneNumber };
