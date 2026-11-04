import MainLayout from '@/Layouts/MainLayout.vue'
import { createInertiaApp, Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { InertiaProgress } from '@inertiajs/progress'
import messages from '@intlify/unplugin-vue-i18n/messages'
import i18nMessages from '../../../public/build/locales/i18n.js'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createPinia } from 'pinia'
import { createApp, h } from 'vue'
import { createI18n } from 'vue-i18n'
import { ObserveVisibility } from 'vue3-observe-visibility'
import route from 'ziggy-js'
import { Ziggy } from '../../../public/build/ziggy.frontend.js'

if (typeof window !== 'undefined') {
    Ziggy.url = window.location.origin
    Ziggy.port = window.location.port ? Number(window.location.port) : null
    Ziggy.location = window.location
    if (Ziggy.routes) {
        for (const name in Ziggy.routes) {
            delete Ziggy.routes[name].domain
        }
    }
}
import VuePlyr from 'vue-plyr'
import 'vue-plyr/dist/vue-plyr.css'

import '../scss/app.scss'

import AOS from 'aos';
import 'aos/dist/aos.css';
import { useThemeStore } from './stores/themeStore.js'
import axios from 'axios'

// Axios: gửi cookie session và CSRF để POST/PUT/DELETE hoạt động với Laravel
axios.defaults.withCredentials = true
axios.defaults.xsrfCookieName = 'XSRF-TOKEN'
axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN'
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || import.meta.env.VITE_APP_NAME
const mergedMessages = { ...messages, ...i18nMessages }

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const page = resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'))

        page.then((module) => {
            module.default.layout = module.default.layout || MainLayout
        })
        return page
    },
    setup({ el, app, props, plugin }) {
        const pinia = createPinia()
        const app_instance = createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(pinia)
        const i18n = createI18n({
            locale: props.initialPage.props.locale?.current || 'vi',
            fallbackLocale: props.initialPage.props.locale?.default || 'vi',
            legacy: false,
            globalInjection: true,
            messages: mergedMessages,
            missingWarn: false,
            fallbackWarn: false,
        })

        // Synchronize i18n locale when Inertia navigates
        Inertia.on('navigate', (event) => {
            const nextLocale = event.detail.page.props.locale?.current
            if (nextLocale && i18n.global) {
                if (typeof i18n.global.locale === 'object' && 'value' in i18n.global.locale) {
                    i18n.global.locale.value = nextLocale
                } else {
                    i18n.global.locale = nextLocale
                }
            }
        })

        app_instance
            .use(i18n)
            .use(AOS.init())
            .use(VuePlyr, { plyr: {} })
            .component('Link', Link)
            .component('Head', Head)
            .directive('observe-visibility', ObserveVisibility)
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

                        // 1. Check direct translation map first
                        try {
                            const currentMsgs = this.$i18n?.messages?.[locale] || mergedMessages?.[locale]
                            if (currentMsgs?.ui?.[text]) {
                                return currentMsgs.ui[text]
                            }
                            if (currentMsgs?.[text]) {
                                return currentMsgs[text]
                            }
                        } catch (e) {}

                        // 2. Vue-i18n $te / $t checks
                        if (typeof this.$te === 'function' && this.$te(`ui.${text}`)) {
                            return this.$t(`ui.${text}`)
                        }
                        if (typeof this.$te === 'function' && this.$te(text)) {
                            return this.$t(text)
                        }

                        return text
                    },
                    toMoney: function (value) {
                        const locale = this.$inertia?.page?.props?.locale?.current || this.$page?.props?.locale?.current || 'vi'
                        let options = {}
                        if (locale === 'vi') {
                            options = {
                                minimumFractionDigits: 0,
                                style: 'currency',
                                currency: 'VND',
                            }
                        } else if (locale === 'en') {
                            options = {
                                minimumFractionDigits: 0,
                                style: 'currency',
                                currency: 'USD',
                            }
                        }

                        const formatter = new Intl.NumberFormat(locale, options)

                        return formatter.format(value ?? 0)
                    },
                    toNumber: function (value) {
                        return new Intl.NumberFormat('vi-VN', {
                            minimumFractionDigits: 1,
                            maximumFractionDigits: 4,
                            minimumSignificantDigits: 1,
                            maximumSignificantDigits: 4,
                        }).format(value)
                    },
                },
                mounted() {
                    AOS.refresh();
                }
            })
            .mount(el)

        // Initialize theme after app is mounted
        const themeStore = useThemeStore()
        themeStore.initializeTheme()
    },
})
InertiaProgress.init({
    color: '#D34000',
    showSpinner: true, // Hide the spinner
    delay: 200, // Show the progress bar after 200ms
})
