<template>
    <div class="flex items-center flex-shrink-0 p-5 border-b border-gray-800">
        <Link href="/" class="flex items-center w-full p-2 space-x-2">
            <img
                src="/assets/images/header/figma-header-logo.svg"
                alt="HH MAKER"
                class="max-h-10 w-auto object-contain"
            />
        </Link>
    </div>
    <nav class="flex-1 px-2 pb-4 space-y-1 navs">
        <ul
            class="flex px-4 py-5 space-x-2 !uppercase overflow-y-auto btn-group"
            v-if="$page.props.locale.list.length > 1"
        >
            <Button
                class="grow !min-w-fit"
                v-for="(locale, index) in $page.props.locale.list"
                :key="index"
                :label="locale"
                size="sm"
                @click="switchLocale(locale)"
                :class="currentLocale === locale ? 'btn-primary' : 'btn-outline-primary'"
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
        @apply opacity-10;
    }
    .item {
        @apply flex items-center w-full gap-2 px-6 py-2 text-white transition duration-150 text-base;

        svg {
            @apply h-6 w-6;
        }
        &:not(.active) {
            @apply opacity-70;
            &:hover {
                @apply opacity-100;
            }
        }
        &.active {
            @apply bg-gray-800 text-primary;
        }
    }
}
</style>
