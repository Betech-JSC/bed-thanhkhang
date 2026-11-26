import MainLayout from '@/Layouts/MainLayout.vue'
import { createInertiaApp, Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { InertiaProgress } from '@inertiajs/progress'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createPinia } from 'pinia'
import { createApp, h } from 'vue'
import { ObserveVisibility } from 'vue3-observe-visibility'
import { vScrollReveal } from '@/composables/useScrollReveal.js'
import {
    createFrontendI18n,
    provideFrontendI18n,
    resolveLocaleFromProps,
    syncI18nLocale,
    translateMessage,
} from '@/composables/useTranslate.js'
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

import { useThemeStore } from './stores/themeStore.js'
import axios from 'axios'

// Axios: gửi cookie session và CSRF để POST/PUT/DELETE hoạt động với Laravel
axios.defaults.withCredentials = true
axios.defaults.xsrfCookieName = 'XSRF-TOKEN'
axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN'
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
window.axios = axios

const appName = window.document.getElementsByTagName('title')[0]?.innerText || import.meta.env.VITE_APP_NAME

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
        const i18n = createFrontendI18n(
            props.initialPage.props.locale?.current || 'vi',
            props.initialPage.props.locale?.default || 'vi'
        )

        // Synchronize i18n locale when Inertia navigates
        Inertia.on('navigate', (event) => {
            const nextLocale = resolveLocaleFromProps(event.detail.page.props, '')
            if (nextLocale) {
                syncI18nLocale(i18n, nextLocale)
            }
        })

        provideFrontendI18n(app_instance, i18n)

        app_instance
            .use(i18n)
            .use(VuePlyr, { plyr: {} })
            .component('Link', Link)
            .component('Head', Head)
            .directive('observe-visibility', ObserveVisibility)
            .directive('scroll-reveal', vScrollReveal)
            .mixin({
                methods: {
                    routeLocaleName(name) {
                        if (!name) return ''
                        if (name.startsWith('vi.') || name.startsWith('en.')) {
                            return Ziggy.routes && Ziggy.routes[name] ? name : name
                        }
                        const locale = resolveLocaleFromProps(
                            this.$page?.props || this.$inertia?.page?.props || props.initialPage?.props,
                            'vi'
                        )
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
                        const locale = resolveLocaleFromProps(
                            this.$page?.props || this.$inertia?.page?.props || props.initialPage?.props,
                            'vi'
                        )
                        return translateMessage(text, locale, i18n)
                    },
                    toMoney: function (value) {
                        const locale = resolveLocaleFromProps(
                            this.$page?.props || this.$inertia?.page?.props || props.initialPage?.props,
                            'vi'
                        )
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
            })
            .mount(el)

        // Initialize theme after app is mounted
        const themeStore = useThemeStore()
        themeStore.initializeTheme()
    },
})
InertiaProgress.init({
    color: '#2970FF',
    showSpinner: true, // Hide the spinner
    delay: 50, // Show the progress bar after 200ms
})
