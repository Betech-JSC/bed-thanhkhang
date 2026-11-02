<template>
    <header
        class="fixed inset-x-0 z-[1000] xl:h-[var(--header-height-xl)] lg:h-[var(--header-height-lg)] md:h-[var(--header-height-md)]"
        @mouseleave="menuSelected = null">
        <div>
            <div class="h-full container pt-[10px]">
                <div
                    class="h-full w-full px-4 max-md:py-2 rounded-[24px] border border-gray-300 bg-[#F4EFE2] dark:bg-dark-surface dark:border-dark-surface-light max-lg:overflow-hidden">
                    <div class="flex items-center justify-between h-full">
                        <div class="flex items-center h-full">
                            <Link :href="route('home')" class="xl:w-[216px] w-[180px] block">
                            <JPicture src="/assets/images/header/logo.png" alt="logo" />
                            </Link>
                        </div>
                        <div class="max-lg:hidden">
                            <ul class="flex items-center">
                                <template v-for="(menu, index) in menus" :key="index">
                                    <li v-if="menu && menu.title !== ''" @mouseover="setMenuSelected(menu)"
                                        @mouseenter="setFirstSubMenu" class="relative py-5 flex items-center group"
                                        :class="fullPath.includes(menu.slug) ? 'text-red-fks' : 'text-gray-700 dark:text-[#F4EFE2]'">
                                        <Link :href="menu.slug || '#'"
                                            class="title-2 font-display font-bold px-[12px] hover:text-red-fks duration-300 flex items-center"
                                            :class="index < menus.length - 1 ? 'border-r border-r-[#CEBE93]' : ''"
                                            @click="menuSelected = null">
                                        <div class="">{{ menu.title }}</div>
                                        <DropdownArrow v-if="menu.subMenu && menu.subMenu.length > 0"
                                            class="ml-1 duration-300" :class="menuSelected === menu ? 'rotate-180' : ''"
                                            width="12" height="12" />
                                        </Link>

                                        <!-- Sub Menu PC Dropdown -->
                                        <transition name="fade-down-enter-from">
                                            <div v-show="menuSelected === menu && menu.subMenu && menu.subMenu.length > 0"
                                                class="absolute top-full pt-2 left-1/2 -translate-x-1/2 w-max min-w-[100px] bg-transparent z-[1000] overflow-hidden title-2 font-display font-bold uppercase">
                                                <ul class="space-y-1">
                                                    <li v-for="(sub, subIdx) in menu.subMenu" :key="subIdx"
                                                        class="px-6 py-3 bg-[#F4EFE2] rounded-2xl text-gray-700 lg:hover:text-red-fks duration-300 ease-in-out">
                                                        <Link :href="sub.slug" class="block text-center"
                                                            @click="menuSelected = null">
                                                        {{ sub.title }}
                                                        </Link>
                                                    </li>
                                                </ul>
                                            </div>
                                        </transition>
                                    </li>
                                </template>
                            </ul>
                        </div>
                        <div class="hidden lg:flex items-center justify-end shrink-0">
                            <!-- Đăng nhập / Tài khoản -->
                            <template v-if="$page.props.auth?.user">
                                <div class="flex items-center gap-2.5 justify-end shrink-0">
                                    <div class="flex flex-col items-end shrink-0">
                                        <Link :href="route('users.profile.index')"
                                            class="title-3 font-display font-black text-red-fks hover:opacity-80 transition-opacity whitespace-nowrap">
                                            {{ $page.props.auth.user.name }}
                                        </Link>
                                    </div>
                                    <!-- Nút mở Thông báo & Hoạt động / Ghi chú admin -->
                                    <button
                                        type="button"
                                        @click="openActivityPopup"
                                        class="relative w-[40px] h-[40px] rounded-xl bg-white/80 hover:bg-white text-gray-700 hover:text-red-fks flex items-center justify-center transition-colors shrink-0 cursor-pointer shadow-xs border border-gray-200/60"
                                        title="Thông báo, Lịch sử hoạt động &amp; Ghi chú Admin"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                        <span
                                            v-if="hasUnreadNotification"
                                            class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-fks rounded-full ring-2 ring-white"
                                        ></span>
                                    </button>

                                    <Link :href="route('users.profile.index')"
                                        class="max-w-[40px] w-full bg-red-fks h-[40px] rounded-xl flex items-center justify-center overflow-hidden border-2 border-red-fks/20 p-0.5 hover:scale-105 transition-transform shrink-0">
                                        <img v-if="$page.props.auth?.user?.avatar?.url" :src="$page.props.auth.user.avatar.url" class="w-full h-full object-cover rounded-lg" alt="Avatar" />
                                        <User v-else class="w-[22px] h-[22px] text-white" />
                                    </Link>
                                </div>
                            </template>
                            <template v-else>
                                <Link :href="route('user.login')"
                                    class="rounded-lg bg-red-fks px-4 py-2 text-sm font-medium text-white hover:opacity-90">
                                Đăng nhập
                                </Link>
                                <!-- <Link
                                    :href="route('register')"
                                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                                >
                                    Đăng ký
                                </Link> -->
                            </template>
                        </div>
                        <div class="lg:hidden" @click="onToggleMenu()">
                            <div
                                class="w-10 h-10 rounded-xl bg-[#D34000] border border-[#D34000] flex items-center justify-center">
                                <div :class="isToggleMenu
                                    ? 'opacity-0 hidden duration-700'
                                    : 'opacity-100 block duration-700'
                                    ">
                                    <JPicture src="/assets/svg/show.svg" class="w-full h-full" />
                                </div>
                                <div :class="!isToggleMenu
                                    ? 'opacity-0 hidden duration-700'
                                    : 'opacity-100 block duration-700'
                                    ">
                                    <JPicture src="/assets/svg/close.svg" class="w-full h-full" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Menu mobile -->
                    <div :style="{
                        maxHeight: isToggleMenu ? elementHeight + 'px' : '0',
                    }" :class="['transition-all duration-300 ease-out md:hidden']" ref="menuElement">
                        <!-- <div
                    class="absolute top-0 left-0 z-10 w-full h-full duration-300 ease-in-out bg-gray-800 opacity-100 bg-opacity-70"
                    :class="isToggleMenu ? 'opacity-100' : 'opacity-0'"
                    @click="closeMenu"
                ></div> -->
                        <div class="w-full h-full bg-[#F4EFE2] duration-300">
                            <div
                                class="flex flex-col overflow-y-scroll max-h-[calc(100vh-56px)] h-full relative z-10 pt-6">
                                <ul class="flex flex-col space-y-3">
                                    <template v-for="(menuMb, menuMbIndex) in menus" :key="menuMbIndex">
                                        <li class="flex items-center justify-between font-bold title-2 font-display pb-3 border-b border-b-[#CEBE93]"
                                            :class="[
                                                fullRoute.includes(menuMb.type) ? 'text-red-fks' : 'text-gray-700',
                                            ]" v-if="menuMb.type !== 'featured_posts'">
                                            <Link :href="menuMb.slug" @click="closeMenu()" class="block" :class="menuMb.subMenu && menuMb.subMenu.length > 0 ? 'w-4/12' : 'w-full'
                                                ">{{ menuMb.title }}</Link>
                                            <div @click="toggleSubMenu(menuMb.subMenu, menuMb.type)"
                                                v-if="menuMb.subMenu && menuMb.subMenu.length > 0" :class="menuMb.subMenu && menuMb.subMenu.length > 0
                                                    ? 'w-8/12 flex justify-end'
                                                    : ''
                                                    ">
                                                <DropdownArrow class="-rotate-90" />
                                            </div>
                                        </li>
                                        <li class="flex items-center justify-between font-bold title-2 font-display pb-3 border-b border-b-[#CEBE93]"
                                            :class="fullPath.includes(menuMb.slug) ? 'text-primary-900' : 'text-white'"
                                            v-else-if="menuMb.type === 'featured_posts' && menuMb.title !== ''">
                                            <Link :href="menuMb.slug" @click="closeMenu()" class="block w-full">{{
                                                menuMb.title
                                            }}</Link>
                                        </li>
                                    </template>
                                </ul>
                                <div class="mt-3">
                                    <div class="space-y-3">
                                        <p class="title-3 font-display font-bold text-gray-warm-700">
                                            Thông tin liên hệ
                                        </p>
                                        <div class="space-y-2">
                                            <p class="text-base text-gray-warm-700 font-normal font-beau">
                                                <span class="font-bold">Địa chỉ:</span>
                                                {{ $page.props.global?.general_address }}
                                            </p>
                                            <p class="text-base text-gray-warm-700 font-normal font-beau">
                                                <span class="font-bold">Email:</span>
                                                {{ $page.props.global?.general_email }}
                                            </p>
                                            <p class="text-base text-gray-warm-700 font-normal font-beau">
                                                <span class="font-bold">Điện thoại:</span>
                                                {{ $page.props.global?.general_phone }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-6 flex flex-col gap-3 border-t border-[#CEBE93] pt-4">
                                    <template v-if="$page.props.auth?.user">
                                        <div class="flex flex-col gap-2">
                                            <Link :href="route('users.profile.index')" @click="closeMenu()"
                                                class="title-2 font-display font-bold text-gray-700">
                                                {{ $page.props.auth.user.name }}
                                            </Link>
                                        </div>
                                        <button type="button"
                                            class="text-left title-2 font-display font-bold text-gray-700 flex items-center justify-between py-1"
                                            @click="openActivityPopup(); closeMenu();">
                                            <span class="flex items-center gap-2">
                                                <span>🔔</span>
                                                <span>Thông báo &amp; Ghi chú</span>
                                            </span>
                                            <span v-if="hasUnreadNotification" class="w-2 h-2 rounded-full bg-red-fks"></span>
                                        </button>
                                        <button type="button"
                                            class="text-left title-2 font-display font-bold text-red-fks"
                                            @click="logout(); closeMenu();">
                                            Đăng xuất
                                        </button>
                                    </template>
                                    <template v-else>
                                        <Link :href="route('user.login')" @click="closeMenu()"
                                            class="title-2 font-display font-bold text-red-fks">
                                        Đăng nhập
                                        </Link>
                                        <Link :href="route('register')" @click="closeMenu()"
                                            class="title-2 font-display font-bold text-gray-700">
                                        Đăng ký
                                        </Link>
                                    </template>
                                </div>
                                <div class="mt-6 flex items-center gap-x-3">
                                    <a @click="closeSubMenu()" v-for="(item, index) in socials" :key="index"
                                        :href="item.link" class="block p-2 w-10 h-10 rounded-lg bg-gray-warm-25">
                                        <img :src="item.image" :alt="item.image" class="w-6 h-6" />
                                    </a>
                                </div>
                            </div>
                            <!-- <div class="absolute bottom-0 left-0">
                        <JPicture src="/assets/images/header/header-shape.png" alt="header shape mobile category" />
                    </div> -->
                        </div>
                        <div class="w-[90vw] md:w-[50vw] h-full bg-[#F4EFE2] absolute z-30 duration-500 ease-in-out flex flex-col overflow-hidden"
                            :class="isToggleSubMenu ? 'right-0' : '-right-full'">
                            <div class="text-gray-700 flex items-center space-x-[4px] mb-8 mt-6 px-4"
                                @click="closeSubMenu()">
                                <DropdownArrow class="rotate-90" />
                                <span>{{ tt('Trở về') }}</span>
                            </div>
                            <ul class="flex flex-col space-y-3.5 overflow-y-auto pb-16 menu-listMb max-h-[500px] h-full"
                                v-if="currentSubMenu && currentSubMenu.length > 0">
                                <li v-for="(subMb, subMbIndex) in currentSubMenu" :key="subMbIndex"
                                    class="flex items-center justify-between menu-itemMb">
                                    <Link :href="route(
                                        currentType === 'services' ? 'services.show' : 'posts.category',
                                        subMb.slug
                                    )
                                        " @click="closeMenu()" class="block w-full px-4 py-2"
                                        :class="activeMenu(subMb.slug) ? 'text-primary-600' : 'text-gray-700'">{{
                                            subMb.title }}</Link>
                                </li>
                            </ul>
                            <div class="absolute bottom-0 left-0">
                                <JPicture src="/assets/images/header/header-shape.png"
                                    alt="header shape mobile sub menu" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
<script>
import ButtonPrimary from './ButtonPrimary.vue'
import DropdownArrow from './Icons/DropdownArrow.vue'
import Headphone from './Icons/Headphone.vue'
import Mail from './Icons/Mail.vue'

import User from './Icons/User.vue'

export default {
    props: {
        fullPath: {
            type: String,
            default: '',
        },
        fullRoute: {
            type: String,
            default: '',
        },
    },
    components: { DropdownArrow, Headphone, Mail, User },
    directives: {
        'click-outside': {
            beforeMount: (el, binding) => {
                el.clickOutsideEvent = (event) => {
                    // here I check that click was outside the el and his children
                    if (!(el == event.target || el.contains(event.target))) {
                        // and if it did, call method provided in attribute value
                        binding.value()
                    }
                }
                document.addEventListener('click', el.clickOutsideEvent)
            },
            unmounted: (el) => {
                document.removeEventListener('click', el.clickOutsideEvent)
            },
        },
    },
    data() {
        return {
            headerVisible: true,
            elementHeight: 0,
            socials: [
                {
                    link: 'https://funky.betech-digital.com/',
                    image: '/assets/svg/facebookHeader.svg',
                },
                {
                    link: 'https://funky.betech-digital.com/',
                    image: '/assets/svg/instagramHeader.svg',
                },
                {
                    link: 'https://funky.betech-digital.com/',
                    image: '/assets/svg/threadHeader.svg',
                },
                {
                    link: 'https://funky.betech-digital.com/',
                    image: '/assets/svg/tiktokHeader.svg',
                },
                {
                    link: 'https://funky.betech-digital.com/',
                    image: '/assets/svg/youtubeHeader.svg',
                },
            ],
            menus: [
                {
                    title: this.tt('Về funky'),
                    slug: this.route('histories.index'),
                    type: 'histories',
                    subMenu: [],
                },
                {
                    title: this.tt('Sản phẩm'),
                    slug: this.route('galleries.index'),
                    type: 'galleries',
                    subMenu: [],
                },
                {
                    title: this.tt('Giải thưởng'),
                    slug: this.route('prizes.index'),
                    type: 'prizes',
                    subMenu: [],
                },
                // {
                //     title: this.tt('Dịch vụ'),
                //     slug: this.route('services.index'),
                //     type: 'services',
                //     subMenu: [],
                // },
                {
                    title: this.tt('Đồ án'),
                    slug: this.route('projects.index'),
                    type: 'projects',
                    subMenu: [],
                },
                {
                    title: this.tt('Feedback'),
                    slug: this.route('feedbacks.index'),
                    type: 'feedbacks',
                    subMenu: [],
                },
                {
                    title: this.tt('F.BLog'),
                    slug: this.route('posts'),
                    type: 'posts',
                    subMenu: [],
                },
            ],
            menuSelected: null,
            subMenuSelected: null,
            isActive: '',
            currentLan: null,
            isToggleLanBox: false,
            isToggleMenu: false,
            isToggleSubMenu: false,
            currentType: '',
            currentSubMenu: [],
            languageList: [
                {
                    title: 'VI',
                    image: {
                        url: '/assets/images/header/vi.png',
                        alt: 'vietnam flag',
                    },
                    type: 'vi',
                    link: '/vi',
                },
            ],
        }
    },
    computed: {
        hasUnreadNotification() {
            const auth = this.$page.props.auth
            return !!auth?.admin_note || !!auth?.user?.funky_note || (auth?.daily_reward && auth.daily_reward.granted && auth.daily_reward.points > 0)
        },
    },
    methods: {
        openActivityPopup() {
            window.dispatchEvent(new CustomEvent('open-activity-popup'))
        },
        calculateHeight() {
            const element = this.$refs.menuElement
            if (element) {
                this.elementHeight = element.scrollHeight
            }
        },
        setMenuSelected(item) {
            this.menuSelected = item
        },
        setFirstSubMenu() {
            if (this.menuSelected.subMenu && this.menuSelected.subMenu.length > 0) {
                this.subMenuSelected = this.menuSelected.subMenu[0]
            }
        },
        setSubMenuSelected(item) {
            this.subMenuSelected = item
        },
        activeMenu(slug) {
            const splitPath = this.fullPath.split('/')
            return slug === splitPath[splitPath.length - 1]
        },
        toggleLanBox() {
            this.isToggleLanBox = !this.isToggleLanBox
        },
        hideBox() {
            this.isToggleLanBox = false
        },
        onToggleMenu() {
            this.isToggleMenu = !this.isToggleMenu
            if (this.isToggleMenu === true) {
                document.body.classList.add('overflow-hidden')
            } else {
                document.body.classList.remove('overflow-hidden')
            }
        },
        closeMenu() {
            const el = document.body
            el.classList.remove('overflow-hidden', 'menu-is-opened')
            this.isToggleMenu = false
            this.isToggleSubMenu = false
        },
        toggleSubMenu(arr, type) {
            this.isToggleSubMenu = !this.isToggleSubMenu
            this.currentSubMenu = arr
            this.currentType = type
        },
        closeSubMenu() {
            this.isToggleSubMenu = false
        },
        scrollToBottom() {
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: 'smooth', // Tạo hiệu ứng cuộn mượt
            })
        },
        logout() {
            this.$inertia.post(
                this.route('users.logout'),
                {},
                {
                    onSuccess: () => {
                        this.$toast?.success('Đăng xuất thành công')
                    },
                }
            )
        },
    },
    mounted() {
        this.calculateHeight()
    },
}
</script>
<style lang="scss">
body {
    --header-height-sm: 48px;
    --header-height-md: 48px;
    --header-height-lg: 68px;
    --header-height-xl: 68px;
}
</style>
<style lang="scss" scoped>
.header-shadow {
    box-shadow: 0 0 3px #1018280f, 0 1px 2px #1018280f;
}

.language-box {
    box-shadow: 0px 1px 2px 0px #1018280d;
}

.bg-header {
    clip-path: polygon(0 0, 100% 0%, 88% 100%, 0% 100%);
}

@media screen and (min-width: 375px) {
    .header-shape {
        width: calc(100vw - ((100vw - 425px) / 2));
    }
}

@media screen and (min-width: 768px) {
    .header-shape {
        width: calc(100vw - ((100vw - 768px) / 2));
    }
}

@media screen and (min-width: 1024px) {
    .header-shape {
        width: calc(100vw - ((100vw - 1024px) / 2));
    }
}

@media screen and (min-width: 1280px) {
    .header-shape {
        width: calc(100vw - ((100vw - 1250px) / 2));
    }
}

@media screen and (min-width: 1366px) {
    .header-shape {
        width: calc(100vw - ((100vw - 1280px) / 2));
    }
}

@media screen and (min-width: 1440px) {
    .header-shape {
        width: calc(100vw - ((100vw - 1265px) / 2));
    }
}

.bg-gradient-header {
    background-image: linear-gradient(350.4deg, #2b40b6 3.98%, #2067e3 30%);
}

ul li:last-child {
    border-bottom: none;
}

.fade-down-enter-active,
.fade-down-leave-active {
    transition: all 0.3s ease-out;
}

.fade-down-enter-from,
.fade-down-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
