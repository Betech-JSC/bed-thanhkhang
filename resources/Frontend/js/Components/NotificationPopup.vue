<template>
    <transition-group name="notify" tag="div" class="fixed bottom-5 left-5 z-[100] space-y-3">
        <div
            v-for="(notification, index) in notifications"
            :key="notification.uniqueId || index"
            class="bg-white shadow-lg rounded-lg p-4 max-w-xs flex items-center space-x-3 cursor-pointer"
            @click="navigateNotification(notification)"
        >
            <svg class="min-w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-sm text-gray-800">{{ notification.message }}</p>
        </div>
    </transition-group>
</template>

<script>
export default {
    name: 'NotificationPopup',
    data() {
        return {
            notifications: [],
            messagePool: [],
            realPool: [],
            realIndex: 0,
            cycleCount: 0,
            timer: null,
            uniqueCounter: 0,
        }
    },
    computed: {
        mode() {
            // 'fake' | 'real' | 'hybrid' (mặc định: 'fake' nếu chưa thiết lập)
            return this.$page?.props?.global?.notification_popup_mode || 'fake'
        },
    },
    async mounted() {
        this.initMessagePool()

        if (this.mode === 'real' || this.mode === 'hybrid') {
            await this.fetchRealNotifications()
        }

        this.startNotificationCycle()
    },
    beforeUnmount() {
        if (this.timer) {
            clearTimeout(this.timer)
        }
    },
    methods: {
        initMessagePool() {
            const rawMessageString = this.$page?.props?.global?.messagePool || ''
            const rawMessages = rawMessageString
                .split(',')
                .map((str) => str.trim().replace(/^'|'$/g, ''))
            this.messagePool = [...new Set(rawMessages)].filter((m) => m)
        },
        async fetchRealNotifications() {
            try {
                const res = await fetch('/api/popup-notifications')
                if (res.ok) {
                    const data = await res.json()
                    if (Array.isArray(data.notifications) && data.notifications.length > 0) {
                        this.realPool = data.notifications
                    }
                }
            } catch (err) {
                console.error('Fetch popup notifications failed:', err)
            }
        },
        startNotificationCycle() {
            const showNext = () => {
                this.addNotification()

                // Random delay từ 12s → 25s
                const delay = Math.floor(Math.random() * (25000 - 12000 + 1)) + 12000
                this.timer = setTimeout(showNext, delay)
            }

            // Hiển thị thông báo đầu tiên sau 4 giây khi vào trang
            this.timer = setTimeout(showNext, 4000)
        },
        addNotification() {
            let item = null
            this.cycleCount++

            if (this.mode === 'real') {
                // Ưu tiên dữ liệu thật, nếu trống thì fallback dữ liệu ảo
                if (this.realPool.length > 0) {
                    item = this.realPool[this.realIndex % this.realPool.length]
                    this.realIndex++
                } else if (this.messagePool.length > 0) {
                    const msg = this.messagePool[Math.floor(Math.random() * this.messagePool.length)]
                    item = { message: msg, is_real: false }
                }
            } else if (this.mode === 'hybrid') {
                // Xen kẽ: lượt chẵn lấy dữ liệu thật, lượt lẻ lấy dữ liệu ảo (hoặc ngược lại nếu 1 trong 2 rỗng)
                const preferReal = this.cycleCount % 2 === 0
                if (preferReal && this.realPool.length > 0) {
                    item = this.realPool[this.realIndex % this.realPool.length]
                    this.realIndex++
                } else if (this.messagePool.length > 0) {
                    const msg = this.messagePool[Math.floor(Math.random() * this.messagePool.length)]
                    item = { message: msg, is_real: false }
                } else if (this.realPool.length > 0) {
                    item = this.realPool[this.realIndex % this.realPool.length]
                    this.realIndex++
                }
            } else {
                // Chế độ 'fake' (như cũ 100%)
                if (this.messagePool.length > 0) {
                    const msg = this.messagePool[Math.floor(Math.random() * this.messagePool.length)]
                    item = { message: msg, is_real: false }
                }
            }

            if (item) {
                const uniqueId = ++this.uniqueCounter
                const notifyObj = { ...item, uniqueId }

                // Giữ tối đa 1 thông báo cùng lúc để không che màn hình
                this.notifications = [notifyObj]

                // Tự động biến mất sau 5.5 giây
                setTimeout(() => {
                    this.notifications = this.notifications.filter((n) => n.uniqueId !== uniqueId)
                }, 5500)
            }
        },
        removeNotification(index) {
            this.notifications.splice(index, 1)
        },
        navigateNotification(notification) {
            if (notification?.url) {
                this.$inertia.visit(notification.url)
            }
        },
    },
}
</script>

<style scoped>
.notify-enter-active,
.notify-leave-active {
    transition: all 0.5s ease-in-out;
}
.notify-enter-from,
.notify-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
.notify-enter-to,
.notify-leave-from {
    opacity: 1;
    transform: translateY(0);
}
</style>
