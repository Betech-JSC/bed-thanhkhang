import MainLayout from '@/Layouts/MainLayout.vue'
import { createInertiaApp, Head, Link } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress'
import messages from '@intlify/unplugin-vue-i18n/messages'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createPinia } from 'pinia'
import { createApp, h } from 'vue'
import { createI18n } from 'vue-i18n'
import { ObserveVisibility } from 'vue3-observe-visibility'
import route from 'ziggy-js'
import { Ziggy } from '../../../public/build/ziggy.frontend.js'
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
            locale: props.initialPage.props.locale?.current,
            fallbackLocale: props.initialPage.props.locale?.default,
            legacy: false,
            globalInjection: true,
            messages: messages,
            missingWarn: false,
            fallbackWarn: false,
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
                        const locale = this.$inertia?.page?.props?.locale?.current || this.$page?.props?.locale?.current || props.initialPage?.props?.locale?.current || 'vi'
                        if (!locale) return name
                        const localeName = locale + '.' + name

                        return Ziggy.routes[localeName] ? localeName : name
                    },
                    route(
                        name,
                        params,
                        absolute,
                        config = {
                            ...Ziggy,
                            location: new URL(Ziggy.url),
                        }
                    ) {
                        try {
                            return route(this.routeLocaleName(name), params, absolute, config)
                        } catch (e) {
                            console.error('Ziggy route error for: ' + name, e)
                            return '#'
                        }
                    },
                    routes() {
                        return Ziggy.routes
                    },
                    tt(text) {
                        return this.$t(text)
                    },
                    toMoney: function (value) {
                        const { default: locale } = this.$inertia.page.props.locale
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
