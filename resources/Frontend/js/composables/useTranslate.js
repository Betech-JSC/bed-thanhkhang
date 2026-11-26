import { computed, inject } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { createI18n } from 'vue-i18n'
import messages from '@intlify/unplugin-vue-i18n/messages'
import i18nMessages from '../../../../public/build/locales/i18n.js'
import viJson from '../../lang/vi.json'
import enJson from '../../lang/en.json'

const TRANSLATE_I18N_KEY = Symbol('frontend-translate-i18n')

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
