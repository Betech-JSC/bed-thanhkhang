import Field from '@Core/Components/Form/Field.vue'
import Form from '@Core/Components/Form/Form.vue'
import FormDynamic from '@Core/Components/Form/FormDynamic.vue'
import Input from '@Core/Components/Form/Input.vue'
import SeoFields from '@Core/Components/Form/SeoFields.vue'

import Table from '@Core/Components/Table.vue'
import AuthenticatedLayout from '@Core/Layouts/Authenticated.vue'
import SidebarMain from '@/Components/SidebarMain.vue'
import { InertiaProgress } from '@inertiajs/progress'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import { createApp, h } from 'vue'
import '../../../packages/bed-package-essentials/core/resources/Backend/scss/app.scss'
import '../scss/app.scss'

import router from '@Core/router'
import { createInertiaApp, Head, Link } from '@inertiajs/inertia-vue3'
import Axios from 'axios'
window.axios = Axios
Axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import 'primeicons/primeicons.css'
import PrimeVue from 'primevue/config'
import Toast from 'primevue/toast'
import ToastService from 'primevue/toastservice'
import { createI18n } from 'vue-i18n'
import localeMessages from '../../../public/build/locales/i18n.js'
import CKEditor from '@ckeditor/ckeditor5-vue';

import { TinyEmitter } from 'tiny-emitter'
const emitter = new TinyEmitter()

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel'

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const pages = import.meta.glob([
            '../../../packages/bed-package-essentials/core/resources/Backend/js/Pages/**/*.vue',
            './Pages/**/*.vue',
        ])

        let filePath = `./Pages/${name}.vue`
        if (name.includes('@Core')) {
            filePath =
                name.replace('@Core', '../../../packages/bed-package-essentials/core/resources/Backend/js/Pages') +
                '.vue'
        }
        const page = resolvePageComponent(filePath, pages)

        page.then((module) => {
            module.default.layout = module.default.layout || AuthenticatedLayout
        })

        return page
    },
    setup({ el, app, props, plugin }) {
        const i18n = createI18n({
            locale: props.initialPage.props.locale,
            fallbackLocale: 'vi',
            messages: localeMessages,
            missingWarn: false,
            fallbackWarn: false,
        })
        const vueApp = createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(i18n)
            .use(router)
            .use(PrimeVue)
            .use(CKEditor)
            .use(ToastService)
            .component('Toast', Toast)
            .component('Form', Form)
            .component('FormDynamic', FormDynamic)
            .component('Field', Field)
            .component('SeoFields', SeoFields)
            .component('Input', Input)
            .component('Table', Table)
            .component('Link', Link)
            .component('Head', Head)
            .component('SidebarMain', SidebarMain)

            .mixin({
                methods: {
                    routeLocaleName(name) {
                        const locale = this.$inertia?.page?.props?.locale?.current || this.$page?.props?.locale?.current || 'vi'
                        if (!locale) return name
                        const localeName = locale + '.' + name

                        return Ziggy.routes[localeName] ? localeName : name
                    },
                    tt(name, params = {}) {
                        let translate = this.$t(name)
                        if (translate !== name) return translate

                        let namePaths = name.split('.')

                        if (name.includes('models.')) {
                            const keyTranslate = `${namePaths.shift()}.common.${namePaths.pop()}`
                            const valueTranslate = this.$t(keyTranslate, params)

                            if (keyTranslate !== valueTranslate) return valueTranslate
                        }

                        translate = name.split('.').pop()
                        return translate.charAt(0).toUpperCase() + translate.slice(1).replace('_', ' ')
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
                    switchLocale(locale, params = {}) {
                        const currentLocale = this.$inertia.page.props.locale.current
                        const currentRoute = route().current().replace(currentLocale, locale)

                        window.location.href = route(currentRoute, {
                            ...route().params,
                            ...params,
                        })
                    },
                    formatPriceVND(price) {
                        return price.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
                    },

                    getResource: function () {
                        let segment = 2
                        const locale = this.$inertia.page.props.locale
                        if (locale.current !== locale.default) {
                            segment = 3
                        }

                        const paths = window.location.pathname.split('/')
                        return paths[segment] ?? paths[segment - 1]
                    },
                    getCurrentLocale: function () {
                        return this.$inertia.page.props.locale.current
                    },
                    pluck: function (array, key = 'id') {
                        return array.map((x) => x[key])
                    },
                    toMoney: function (value) {
                        if (typeof value === 'string') {
                            value = value.replace(/[^\d]/g, '')
                        }
                        const val = parseFloat(value)
                        const formatter = new Intl.NumberFormat('vi', {
                            style: 'currency',
                            currency: 'VND',
                            minimumFractionDigits: 0,
                        })

                        return formatter.format(isNaN(val) ? 0 : val)
                    },
                    toNumber: function (value) {
                        return new Intl.NumberFormat('vi-VN', {
                            minimumFractionDigits: 1,
                            maximumFractionDigits: 4,
                            minimumSignificantDigits: 1,
                            maximumSignificantDigits: 4,
                        }).format(value)
                    },
                    toDate: function (value, format = 'DD/MM/YYYY HH:mm') {
                        if (!value) return null
                        return dayjs.extend(relativeTime)(value).format(format)
                    },
                    isUrl(url, params = {}) {
                        let isCurrentParams = true
                        if (Object.keys(params).length) {
                            isCurrentParams = JSON.stringify(route().params) === JSON.stringify(params)
                        }
                        const isCurrentRoute = route().current(url) || route().current('*.' + url)

                        return isCurrentRoute && isCurrentParams
                    },
                    staticUrl(url) {
                        if (!url) return ''

                        const urlStr = url.toString().trim()
                        if (!urlStr) return ''

                        if (urlStr.startsWith('http://') || urlStr.startsWith('https://')) {
                            return urlStr
                        }

                        if (
                            urlStr.startsWith('/images/') ||
                            urlStr.startsWith('images/') ||
                            urlStr.startsWith('/assets/') ||
                            urlStr.startsWith('assets/') ||
                            urlStr.startsWith('/storage/') ||
                            urlStr.startsWith('storage/')
                        ) {
                            return window.location.origin + (urlStr.startsWith('/') ? urlStr : '/' + urlStr)
                        }

                        const cleanPath = urlStr.startsWith('/') ? urlStr.substring(1) : urlStr
                        return this.route('files.show') + '/' + cleanPath
                    },
                    isImage(url) {
                        if (!url || !(url.toString().includes('.'))) return false;
                        return /(jpg|jpeg|png|webp|avif|gif|svg)/.test(url?.toLowerCase().split('.').pop())
                    },
                    can(permission) {
                        return this.$inertia.page.props.admin.permissions.includes(permission)
                    },
                },
            })

        vueApp.config.globalProperties.$axios = Axios
        vueApp.config.globalProperties.$bus = emitter
        window.$axios = Axios
        return vueApp.mount(el)
    },
})

InertiaProgress.init({ color: 'var(--primary)' })
