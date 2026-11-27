<template>
    <div class="flex items-center flex-shrink-0 p-4 border-b border-white/10">
        <Link
            href="/"
            class="flex items-center justify-center w-full bg-white rounded-lg px-3 py-2 shadow-sm hover:opacity-95 transition-opacity"
        >
            <img
                src="/assets/images/logo/logo-thanhkhang.png"
                alt="Thành Khang"
                class="h-8 w-auto object-contain"
            />
        </Link>
    </div>
    <nav class="flex-1 px-3 pb-4 space-y-1 navs">
        <ul
            class="flex px-1 py-4 space-x-2 !uppercase overflow-y-auto btn-group"
            v-if="$page.props.locale.list.length > 1"
        >
            <Button
                class="grow !min-w-fit btn-sm sidebar-locale-btn"
                v-for="(locale, index) in $page.props.locale.list"
                :key="index"
                :label="locale"
                size="sm"
                @click="switchLocale(locale)"
                :class="
                    currentLocale === locale
                        ? 'bg-[#005EA4] border-[#005EA4] text-white font-semibold sidebar-locale-btn--active'
                        : 'bg-transparent border-white/25 text-[#C9E0FF] hover:bg-white/10 hover:text-white sidebar-locale-btn--inactive'
                "
            />
        </ul>
        <SidebarMain />
    </nav>
</template>
<script>
export default {
    computed: {
        currentLocale() {
            return this.$page.props.locale.current
        },
    },

    methods: {
        async switchLocale(locale, params = {}) {
            await this.$axios.post(route(`admin.helper.set-locale`, { locale }))

            const currentLocale = this.$inertia.page.props.locale.current
            const currentRoute = route().current().replace(currentLocale, locale)

            window.location.href = route(currentRoute, {
                ...route().params,
                ...params,
            })
        },
    },
}
</script>
<style lang="scss">
.navs {
    hr {
        opacity: 0.1;
        border-color: rgba(255, 255, 255, 0.1);
    }
    .btn-group {
        .p-button.sidebar-locale-btn {
            @apply h-8 text-xs rounded-lg shadow-none transition-colors duration-150;

            &.sidebar-locale-btn--active {
                background-color: #005EA4;
                border: 1px solid #005EA4;
                color: #ffffff;
                @apply font-semibold;

                &:hover {
                    background-color: #004D86;
                    border-color: #004D86;
                    color: #ffffff;
                }
            }

            &.sidebar-locale-btn--inactive {
                background-color: transparent;
                border: 1px solid rgba(255, 255, 255, 0.25);
                color: #C9E0FF;
                @apply font-medium;

                &:hover {
                    background-color: rgba(255, 255, 255, 0.1);
                    border-color: rgba(255, 255, 255, 0.4);
                    color: #ffffff;
                }
            }
        }
    }
    .item {
        @apply flex items-center w-full gap-2.5 px-4 py-2.5 text-sm font-medium rounded-lg transition duration-150;
        color: rgba(201, 224, 255, 0.85);

        svg {
            @apply h-5 w-5 shrink-0;
        }
        &:not(.active):hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        &.active {
            background-color: #005EA4;
            color: #ffffff;
            @apply font-semibold shadow-sm;
        }
    }
}
</style>
