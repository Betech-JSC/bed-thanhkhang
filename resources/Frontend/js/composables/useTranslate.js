import { computed, inject } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { createI18n } from 'vue-i18n'
import messages from '@intlify/unplugin-vue-i18n/messages'
import i18nMessages from '../../../../public/build/locales/i18n.js'
import viJson from '../../lang/vi.json'
import enJson from '../../lang/en.json'

const TRANSLATE_I18N_KEY = Symbol('frontend-translate-i18n')

const VIETNAMESE_DIACRITIC_RE =
    /[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/i

export function hasVietnameseDiacritics(text) {
    if (typeof text !== 'string' || !text) return false
    return VIETNAMESE_DIACRITIC_RE.test(text)
}

export function normalizeTranslateKey(raw) {
    if (raw === null || raw === undefined) return ''
    return String(raw)
        .replace(/\u00a0/g, ' ')
        .replace(/\s+/g, ' ')
        .trim()
}

function decodeBasicHtmlEntities(str) {
    if (typeof str !== 'string' || !str) return ''
    return str
        .replace(/&amp;/gi, '&')
        .replace(/&lt;/gi, '<')
        .replace(/&gt;/gi, '>')
        .replace(/&quot;/gi, '"')
        .replace(/&#0*39;|&apos;/gi, "'")
        .replace(/&nbsp;/gi, ' ')
        .replace(/&ndash;/gi, '–')
        .replace(/&mdash;/gi, '—')
}

function escapeBasicHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
}

/**
 * Translate technical specification labels consistently across PDP sections,
 * normalizing whitespace/colons before looking up via vue-i18n `tt()`.
 */
export function translateSpecLabel(rawLabel, translator = null) {
    const normalized = normalizeTranslateKey(rawLabel)
    if (!normalized) return ''
    const fn = typeof translator === 'function' ? translator : (text) => text

    const direct = fn(normalized)
    if (direct && direct !== normalized) {
        return direct
    }

    const hasColon = normalized.endsWith(':')
    const base = hasColon ? normalized.slice(0, -1).trim() : normalized
    if (!base) return normalized

    const candidates = [base]
    const hyphenVariant = base.replace(/[–—]/g, '-')
    const enDashVariant = base.replace(/\s-\s/g, ' – ')
    if (hyphenVariant !== base) candidates.push(hyphenVariant)
    if (enDashVariant !== base) candidates.push(enDashVariant)

    for (const candidate of candidates) {
        const translatedBase = fn(candidate)
        if (translatedBase && translatedBase !== candidate) {
            return hasColon ? `${translatedBase}:` : translatedBase
        }
        if (!hasColon) {
            const withColon = `${candidate}:`
            const translatedColon = fn(withColon)
            if (translatedColon && translatedColon !== withColon) {
                return translatedColon.replace(/:\s*$/, '')
            }
        }
    }

    return normalized
}

/**
 * Translate technical specification values (numbers/units pass through untouched;
 * known Vietnamese phrases like strand count or heat-treatment notes localize on EN).
 */
export function translateSpecValue(rawValue, translator = null, isEnLocale = false) {
    const normalized = normalizeTranslateKey(rawValue)
    if (!normalized) return ''
    const fn = typeof translator === 'function' ? translator : (text) => text

    const direct = fn(normalized)
    if (direct && direct !== normalized) {
        return direct
    }

    if (!isEnLocale || !hasVietnameseDiacritics(normalized)) {
        return normalized
    }

    let fallback = normalized
        .replace(/\b(\d+)\s+hoặc\s+(\d+)\s+dãy\b/gi, '$1 or $2 strands')
        .replace(/\bchưa nhiệt luyện\b/gi, 'non-heat-treated')
        .replace(/\bnhiệt luyện\b/gi, 'heat-treated')
        .replace(/\bKiểu\s*\(type\)/gi, 'Type')
        .replace(/\bbề dày má\b/gi, 'plate thickness')

    return fallback
}

/**
 * Localize DB-seeded Vietnamese headings and `<li><strong>Label:</strong> Value</li>`
 * entries inside product rich HTML when rendering EN locale.
 */
export function localizeRichHtmlForLocale(rawHtml, isEnLocale = false, translator = null) {
    if (typeof rawHtml !== 'string' || !rawHtml.trim() || !isEnLocale) {
        return typeof rawHtml === 'string' ? rawHtml : ''
    }
    const fn = typeof translator === 'function' ? translator : (text) => text

    let localized = rawHtml.replace(
        /(<(h[1-6])\b[^>]*>)([^<]+)(<\/\2>)/gi,
        (full, openTag, tagName, inner, closeTag) => {
            const decoded = decodeBasicHtmlEntities(inner)
            const translated = translateSpecLabel(decoded, fn)
            if (translated && translated !== normalizeTranslateKey(decoded)) {
                return `${openTag}${escapeBasicHtml(translated)}${closeTag}`
            }
            return full
        }
    )

    localized = localized.replace(
        /(<li\b[^>]*>\s*<(strong|b)\b[^>]*>)([^<]+)(<\/\2>)/gi,
        (full, prefix, tagName, labelRaw, closeTag) => {
            const decodedLabel = decodeBasicHtmlEntities(labelRaw)
            const translatedLabel = translateSpecLabel(decodedLabel, fn)
            if (translatedLabel && translatedLabel !== normalizeTranslateKey(decodedLabel)) {
                return `${prefix}${escapeBasicHtml(translatedLabel)}${closeTag}`
            }
            return full
        }
    )

    localized = localized.replace(
        /(<li\b[^>]*>\s*<(?:strong|b)\b[^>]*>[^<]+<\/(?:strong|b)>\s*:?\s*)([^<]+)(<\/li>)/gi,
        (full, prefix, valueRaw, closeLi) => {
            const decodedValue = decodeBasicHtmlEntities(valueRaw)
            const translatedValue = translateSpecValue(decodedValue, fn, true)
            if (translatedValue && translatedValue !== normalizeTranslateKey(decodedValue)) {
                const leadingSpace = /^\s/.test(valueRaw) ? ' ' : ''
                return `${prefix}${leadingSpace}${escapeBasicHtml(translatedValue)}${closeLi}`
            }
            return full
        }
    )

    return localized
}

const rawMerged = { ...messages, ...i18nMessages }

export const mergedMessages = {
    ...rawMerged,
    vi: {
        ...(rawMerged.vi || {}),
        ...viJson,
        ui: {
            ...(rawMerged.vi?.ui || {}),
            ...viJson,
        },
    },
    en: {
        ...(rawMerged.en || {}),
        ...enJson,
        ui: {
            ...(rawMerged.en?.ui || {}),
            ...enJson,
        },
    },
}

/**
 * Resolve current locale code ('vi' | 'en' | ...) from Inertia page props.
 */
export function resolveLocaleFromProps(pageProps, fallback = 'vi') {
    const loc = pageProps?.locale
    if (typeof loc === 'string' && loc) return loc
    return loc?.current || fallback
}

/**
 * Synchronize a vue-i18n instance's active locale with the given locale string.
 */
export function syncI18nLocale(i18n, locale) {
    const target = i18n?.global || i18n
    if (!target || !locale) return

    if (typeof target.locale === 'object' && target.locale !== null && 'value' in target.locale) {
        if (target.locale.value !== locale) {
            target.locale.value = locale
        }
    } else if (typeof target.locale === 'string' && target.locale !== locale) {
        target.locale = locale
    }
}

/**
 * Translate text against merged UI dictionaries and optional vue-i18n composer,
 * without accessing missing $te properties on component proxy instances.
 */
export function translateMessage(text, locale = 'vi', i18n = null) {
    if (!text) return ''

    const composer = i18n?.global || i18n
    syncI18nLocale(composer, locale)

    // 1. Check direct translation map first
    try {
        const composerMsgs = composer?.messages?.value?.[locale] || composer?.messages?.[locale]
        const currentMsgs = composerMsgs || mergedMessages?.[locale]

        if (currentMsgs?.ui?.[text]) {
            return currentMsgs.ui[text]
        }
        if (currentMsgs?.[text]) {
            return currentMsgs[text]
        }

        // Robust newline handling: literal \n, normal space, or trimmed
        if (typeof text === 'string') {
            if (text.includes('\n')) {
                const literalSlashN = text.replace(/\r?\n/g, '\\n')
                if (currentMsgs?.ui?.[literalSlashN]) {
                    return currentMsgs.ui[literalSlashN]
                }
                const singleSpaced = text.replace(/\r?\n/g, ' ')
                if (currentMsgs?.ui?.[singleSpaced]) {
                    return currentMsgs.ui[singleSpaced]
                }
            }
            const trimmed = text.trim()
            if (trimmed !== text) {
                if (currentMsgs?.ui?.[trimmed]) {
                    return currentMsgs.ui[trimmed]
                }
                if (currentMsgs?.[trimmed]) {
                    return currentMsgs[trimmed]
                }
            }
        }
    } catch (e) {}

    // 2. Vue-i18n composer te / t checks
    if (composer && typeof composer.te === 'function' && typeof composer.t === 'function') {
        if (composer.te(`ui.${text}`, locale)) {
            return composer.t(`ui.${text}`)
        }
        if (composer.te(text, locale)) {
            return composer.t(text)
        }
    }

    return text
}

/**
 * Create a configured vue-i18n instance sharing the merged frontend dictionaries.
 */
export function createFrontendI18n(locale = 'vi', fallbackLocale = 'vi', extraOptions = {}) {
    return createI18n({
        locale,
        fallbackLocale,
        legacy: false,
        globalInjection: true,
        messages: mergedMessages,
        missingWarn: false,
        fallbackWarn: false,
        ...extraOptions,
    })
}

/**
 * Provide the active vue-i18n instance to Composition API consumers via inject.
 */
export function provideFrontendI18n(appInstance, i18n) {
    appInstance.provide(TRANSLATE_I18N_KEY, i18n)
}

/**
 * Reactive translation composable for <script setup> components.
 * Uses `translate` (non-shadowing) so templates continue using global mixin `tt()`.
 */
export function useTranslate() {
    const page = usePage()
    const i18n = inject(TRANSLATE_I18N_KEY, null)

    const pageProps = computed(() => page?.props?.value ?? page?.props ?? {})
    const currentLocale = computed(() => resolveLocaleFromProps(pageProps.value, 'vi'))
    const isEn = computed(() => currentLocale.value === 'en')

    function translate(text) {
        return translateMessage(text, currentLocale.value, i18n)
    }

    return {
        translate,
        currentLocale,
        isEn,
        page,
        pageProps,
    }
}
