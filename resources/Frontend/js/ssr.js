import MainLayout from '@/Layouts/MainLayout.vue'
import { createInertiaApp, Head, Link } from '@inertiajs/inertia-vue3'
import createServer from '@inertiajs/server'
import { renderToString } from '@vue/server-renderer'
import { createPinia } from 'pinia'
import { createSSRApp, h } from 'vue'
import { vScrollReveal } from '@/composables/useScrollReveal.js'
import {
    createFrontendI18n,
    provideFrontendI18n,
    resolveLocaleFromProps,
    translateMessage,
} from '@/composables/useTranslate.js'
import route from 'ziggy-js'
import { Ziggy } from '../../../public/build/ziggy.frontend.js'

if (Ziggy.routes) {
    for (const name in Ziggy.routes) {
        delete Ziggy.routes[name].domain
    }
}

const appName = import.meta.env.VITE_APP_NAME
const appPort = import.meta.env.VITE_APP_PORT || 13714

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
                const i18n = createFrontendI18n(
                    props.initialPage.props.locale?.current || 'vi',
                    props.initialPage.props.locale?.default || 'vi'
                )
                const ssrApp = createSSRApp({
                    render: () => h(app, props),
                })
                provideFrontendI18n(ssrApp, i18n)

                return ssrApp
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
                        },
                    })
            },
        }),
    appPort
)
