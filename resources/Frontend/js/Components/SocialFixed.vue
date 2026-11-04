<template>
    <div class="fixed top-1/2 xl:right-[32px] right-4 z-[100] space-y-3 md:space-y-4">
        <!-- <transition name="chat-bubble">
            <div
                v-if="showChatBubble"
                class="bg-white shadow-lg rounded-lg p-4 mb-4 max-w-xs transition-all duration-300"
                :class="isChatBubbleOpen ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
            >
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-800">Chào bạn!</h3>
                    <button @click="closeChatBubble" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    Cảm ơn bạn đã ghé thăm! Bạn muốn liên hệ qua Messenger, Zalo hay gọi điện nhé?
                </p>
                <div class="mt-3 flex space-x-2">
                    <a
                        href="https://m.me/2108952142469502"
                        target="_blank"
                        rel="noopener noreferrer nofollow"
                        class="text-sm text-blue-600 hover:underline"
                    >
                        Messenger
                    </a>
                    <a
                        :href="$page.props.global?.general_zalo || $page.props.global?.zalo || 'https://zalo.me/0938039170'"
                        target="_blank"
                        rel="noopener noreferrer nofollow"
                        class="text-sm text-blue-600 hover:underline"
                    >
                        Zalo
                    </a>
                    <a
                        :href="'tel:' + ($page.props.global?.general_phone || $page.props.global?.hotline || '0938039170')"
                        rel="noopener noreferrer nofollow"
                        class="text-sm text-blue-600 hover:underline"
                    >
                        Gọi ngay
                    </a>
                </div>
            </div>
        </transition> -->
    </div>
    <div class="fixed xl:bottom-10 bottom-5 xl:right-[32px] right-4 z-[100] space-y-3 md:space-y-4">
        <a
            href="https://m.me/2108952142469502"
            target="_blank"
            rel="noopener noreferrer nofollow"
            class="w-12 h-12 rounded-full flex items-center justify-center"
        >
            <JPicture src="/assets/images/icon-messenger.webp" alt="icon messenger" class="picture-cover" />
        </a>
        <a
            :href="$page.props.global?.general_zalo || $page.props.global?.zalo || 'https://zalo.me/0938039170'"
            target="_blank"
            rel="noopener noreferrer nofollow"
            class="w-12 h-12 rounded-full flex items-center justify-center"
        >
            <JPicture src="/assets/images/icon-zalo.webp" alt="icon zalo" class="picture-cover" />
        </a>
        <a
            :href="'tel:' + ($page.props.global?.general_phone || $page.props.global?.hotline || '0938039170')"
            rel="noopener noreferrer nofollow"
            class="w-12 h-12 rounded-full flex items-center justify-center bg-red-fks lg:hover:bg-green-fks duration-300 ease-in-out text-green-fks lg:hover:text-black-fks"
        >
            <Phone class="w-8" />
        </a>
        <button
            class="w-[48px] h-[48px] bg-red-fks rounded-full flex items-center justify-center lg:hover:bg-green-fks duration-300 ease-in-out text-white lg:hover:text-black-fks"
            :class="isShowBtn ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
            @click="scrollToTop"
        >
            <IconArrowUp class="-rotate-90" />
        </button>
    </div>
</template>

<script>
import Phone from './Icon/Phone.vue'

export default {
    components: { Phone },
    data() {
        return {
            isShowBtn: false,
            showChatBubble: false,
            isChatBubbleOpen: false,
        }
    },
    mounted() {
        const self = this
        // Scroll event for scroll-to-top button
        window.addEventListener('scroll', function () {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 500) {
                self.isShowBtn = true
            } else {
                self.isShowBtn = false
            }
        })

        // Show chat bubble after 3 seconds
        setTimeout(() => {
            self.showChatBubble = true
            self.isChatBubbleOpen = true
        }, 3000)
    },
    methods: {
        scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth',
            })
        },
        closeChatBubble() {
            this.isChatBubbleOpen = false
            // Optionally hide the bubble completely after a delay
            setTimeout(() => {
                this.showChatBubble = false
            }, 300)
        },
    },
}
</script>

<style scoped>
/* Animation for chat bubble */
.chat-bubble-enter-active {
    transition: all 0.3s ease-in-out;
}
.chat-bubble-leave-active {
    transition: all 0.3s ease-in-out;
}
.chat-bubble-enter-from,
.chat-bubble-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
.chat-bubble-enter-to,
.chat-bubble-leave-from {
    opacity: 1;
    transform: translateY(0);
}
</style>