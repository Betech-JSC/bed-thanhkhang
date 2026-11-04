import MainLayout from '@/Layouts/MainLayout.vue'
import { createInertiaApp, Head, Link } from '@inertiajs/inertia-vue3'
import createServer from '@inertiajs/server'
import messages from '@intlify/unplugin-vue-i18n/messages'
import i18nMessages from '../../../public/build/locales/i18n.js'
import viJson from '../lang/vi.json'
import enJson from '../lang/en.json'
import { renderToString } from '@vue/server-renderer'
import { createPinia } from 'pinia'
import { createSSRApp, h } from 'vue'
import { createI18n } from 'vue-i18n'
import { vScrollReveal } from '@/composables/useScrollReveal.js'
import route from 'ziggy-js'
import { Ziggy } from '../../../public/build/ziggy.frontend.js'

if (Ziggy.routes) {
    for (const name in Ziggy.routes) {
        delete Ziggy.routes[name].domain
    }
}

const appName = import.meta.env.VITE_APP_NAME
const appPort = import.meta.env.VITE_APP_PORT || 13714
const rawMerged = { ...messages, ...i18nMessages }
const mergedMessages = {
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

createServer(
    (page) =>
        createInertiaApp({
            page,
            render: renderToString,
            title: (title) => `${title} - ${appName}`,
            resolve: (name) => {
                const pages = import.meta.glob('./Pages/**/*.vue')

                let page = pages[`./Pages/${name}.vue`]
                if (typeof page === 'undefined') {
                    return false
                }
                page = typeof page === 'function' ? page() : page

                page.then((module) => {
                    module.default.layout = module.default.layout || MainLayout
                })

                return page
            },
            setup({ app, props, plugin }) {
                const i18n = createI18n({
                    locale: props.initialPage.props.locale?.current || 'vi',
                    fallbackLocale: props.initialPage.props.locale?.default || 'vi',
                    legacy: false,
                    globalInjection: true,
                    messages: mergedMessages,
                })
                return createSSRApp({
                    render: () => h(app, props),
                })
                    .use(plugin)
                    .use(i18n)
                    .use(createPinia())
                    .component('Link', Link)
                    .component('Head', Head)
                    .directive('scroll-reveal', vScrollReveal)
                    .mixin({
                        methods: {
                            routeLocaleName(name) {
                                if (!name) return ''
                                if (name.startsWith('vi.') || name.startsWith('en.')) {
                                    return Ziggy.routes && Ziggy.routes[name] ? name : name
                                }
                                const locale = this.$inertia?.page?.props?.locale?.current || this.$page?.props?.locale?.current || props.initialPage?.props?.locale?.current || 'vi'
                                if (!locale) return name
                                const localeName = locale + '.' + name

                                return Ziggy.routes && Ziggy.routes[localeName] ? localeName : name
                            },
                            route(
                                name,
                                params,
                                absolute = false,
                                config = { ...Ziggy }
                            ) {
                                try {
                                    const res = route(this.routeLocaleName(name), params, absolute, config)
                                    return res === '' ? '/' : res
                                } catch (e) {
                                    console.error('Ziggy route error for: ' + name, e)
                                    return '#'
                                }
                            },
                            routes() {
                                return Ziggy.routes
                            },
                            tt(text) {
                                if (!text) return ''
                                const locale = this.$inertia?.page?.props?.locale?.current || this.$page?.props?.locale?.current || 'vi'

                                if (this.$i18n) {
                                    if (typeof this.$i18n.locale === 'object' && this.$i18n.locale.value !== locale) {
                                        this.$i18n.locale.value = locale
                                    } else if (typeof this.$i18n.locale === 'string' && this.$i18n.locale !== locale) {
                                        this.$i18n.locale = locale
                                    }
                                }

                                try {
                                    const currentMsgs = this.$i18n?.messages?.[locale] || mergedMessages?.[locale]
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

                                if (typeof this.$te === 'function' && this.$te(`ui.${text}`)) {
                                    return this.$t(`ui.${text}`)
                                }
                                if (typeof this.$te === 'function' && this.$te(text)) {
                                    return this.$t(text)
                                }

                                return text
                            },
                        },
                    })
            },
        }),
    appPort
)
