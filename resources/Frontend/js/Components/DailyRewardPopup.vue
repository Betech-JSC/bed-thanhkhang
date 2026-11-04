<template>
    <div>
        <!-- Floating Trigger Button (Nút nổi mở lại bất cứ lúc nào khi đã đăng nhập) -->
        <div
            v-if="isAuthenticated && !visible"
            class="fixed bottom-20 right-4 sm:right-6 z-[990] flex items-center gap-2 select-none"
        >
            <button
                type="button"
                @click="openPopup"
                class="group flex items-center gap-2 bg-white/95 hover:bg-white text-gray-800 hover:text-red-fks py-2 px-3 sm:px-3.5 rounded-full shadow-[0_8px_25px_rgba(0,0,0,0.12)] border border-red-fks/20 hover:border-red-fks transition-all duration-300 transform hover:scale-105 active:scale-95"
                title="Xem lịch sử hoạt động và ghi chú của Admin"
            >
                <div class="relative flex items-center justify-center w-7 h-7 bg-red-50 text-red-fks rounded-full group-hover:bg-red-fks group-hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <!-- Badge chấm đỏ nếu có ghi chú hoặc thưởng hôm nay -->
                    <span
                        v-if="hasUnreadBadge"
                        class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-fks rounded-full ring-2 ring-white animate-ping"
                    ></span>
                    <span
                        v-if="hasUnreadBadge"
                        class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-fks rounded-full ring-2 ring-white"
                    ></span>
                </div>
                <span class="text-xs font-bold font-display hidden sm:inline-block">Hoạt động &amp; Ghi chú</span>
            </button>
        </div>

        <!-- Main Notification & Activity Popup -->
        <transition name="slide-fade-right">
            <div
                v-if="visible"
                class="fixed top-16 sm:top-20 right-2 sm:right-6 z-[9999] max-w-[380px] w-[calc(100vw-1rem)] sm:w-full bg-white rounded-2xl border-2 border-red-fks shadow-[0_15px_40px_rgba(227,28,28,0.2)] overflow-hidden font-sans select-none flex flex-col max-h-[85vh]"
            >
                <!-- Popup Header -->
                <div class="bg-red-fks px-4 py-3 text-white flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center">
                            <span class="text-xs">🔔</span>
                        </div>
                        <span class="text-xs font-black uppercase tracking-wider font-display">Thông báo &amp; Hoạt động</span>
                    </div>
                    <button
                        type="button"
                        @click="dismiss"
                        class="w-6 h-6 rounded-lg bg-white/10 hover:bg-white/30 flex items-center justify-center text-white/90 hover:text-white transition-colors cursor-pointer"
                        title="Tạm ẩn popup (vẫn lưu lại để xem lại bất cứ lúc nào)"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Popup Scrollable Body -->
                <div class="p-3.5 overflow-y-auto space-y-3 custom-scrollbar flex-1">
                    
                    <!-- 1. Ghi chú từ Quản trị viên (Admin Note) -->
                    <div
                        v-if="adminNote"
                        class="bg-amber-50/90 border border-amber-200/80 rounded-xl p-3 shadow-xs space-y-2"
                    >
                        <div class="flex items-center gap-1.5 text-amber-800 font-bold text-xs">
                            <span>📌</span>
                            <span>Ghi chú từ Ban Quản trị:</span>
                        </div>
                        <p class="text-xs text-amber-900 leading-relaxed whitespace-pre-line pl-5 font-medium">
                            {{ adminNote }}
                        </p>
                        <div v-if="!showRewardBanner" class="pt-1">
                            <button
                                type="button"
                                @click="dismiss"
                                class="w-full py-1.5 px-3 bg-amber-600 hover:bg-amber-700 active:scale-[0.99] text-white font-bold text-[11px] rounded-lg transition shadow-2xs cursor-pointer flex items-center justify-center gap-1"
                            >
                                <span>✓ Đã hiểu và đóng thông báo</span>
                            </button>
                        </div>
                    </div>

                    <!-- 2. Banner Thưởng đăng nhập ngày hôm nay (Daily Reward Card) -->
                    <div
                        v-if="showRewardBanner"
                        class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200/80 rounded-xl p-3 space-y-2.5"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <div class="space-y-0.5 flex-1">
                                <p class="text-xs font-bold text-gray-800 leading-tight">
                                    Bạn được <span class="text-amber-600 font-black">+{{ rewardPoints }}</span> quả thưởng
                                </p>
                                <p class="text-[11px] text-gray-500 font-medium">
                                    đăng nhập ngày hôm nay
                                </p>
                            </div>
                            <!-- Badge +10 Quả thưởng -->
                            <div class="flex items-center gap-1.5 bg-white text-amber-700 px-2 py-1 rounded-lg border border-amber-200 font-black text-xs shrink-0 shadow-2xs">
                                <span>+{{ rewardPoints }}</span>
                                <img src="/assets/images/qua_thuong.png" class="w-4 h-4 object-contain shrink-0" alt="Quả thưởng" />
                            </div>
                        </div>

                        <!-- Button cảm ơn -->
                        <button
                            type="button"
                            @click="dismiss"
                            class="w-full py-2 px-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 active:scale-[0.99] text-white font-black text-[11px] uppercase tracking-wider flex items-center justify-center gap-1.5 rounded-lg transition-all duration-200 cursor-pointer group shadow-xs"
                        >
                            <span>MÌNH CẢM ƠN FUNKY!</span>
                            <span class="text-[9px] transform group-hover:translate-x-1 transition-transform">▶</span>
                        </button>
                    </div>

                    <!-- 3. Lịch sử hoạt động & Biến động Quả -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between px-0.5">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 font-display flex items-center gap-1">
                                <span>🕒</span>
                                <span>Hoạt động gần đây</span>
                            </span>
                            <Link
                                v-if="hasPointHistoryRoute"
                                :href="route('home')"
                                @click="visible = false"
                                class="text-[11px] text-red-fks font-bold hover:underline"
                            >
                                Xem tất cả →
                            </Link>
                        </div>

                        <!-- Danh sách giao dịch -->
                        <div v-if="activities.length > 0" class="space-y-1.5">
                            <div
                                v-for="act in activities"
                                :key="act.id"
                                class="p-2.5 bg-gray-50/80 hover:bg-gray-100/80 rounded-xl border border-gray-100 flex items-start justify-between gap-2 transition-colors"
                            >
                                <div class="flex items-start gap-2 flex-1 min-w-0">
                                    <span class="text-sm shrink-0 mt-0.5">
                                        {{ getActivityIcon(act.type) }}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-semibold text-gray-800 leading-snug line-clamp-2">
                                            {{ act.description || act.formatted_type }}
                                        </p>
                                        <p class="text-[10px] text-gray-400 mt-0.5 font-medium">
                                            {{ act.formatted_time || act.created_at }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span
                                        class="text-xs font-black font-display block"
                                        :class="act.is_positive ? 'text-green-600' : 'text-red-fks'"
                                    >
                                        {{ act.is_positive ? '+' : '' }}{{ act.amount }}
                                    </span>
                                    <span class="text-[9px] text-gray-400 font-medium">
                                        dư: {{ act.balance_after }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="py-6 text-center text-gray-400 space-y-1">
                            <p class="text-xs font-medium">Chưa có hoạt động nào được ghi nhận</p>
                        </div>
                    </div>
                </div>

                <!-- Popup Footer Actions -->
                <div class="p-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-2 shrink-0">
                    <Link
                        :href="route('home')"
                        @click="visible = false"
                        class="flex-1 py-2 px-3 bg-white border border-gray-200 hover:border-green-600 text-gray-700 hover:text-green-700 font-bold text-xs rounded-xl text-center transition-colors flex items-center justify-center gap-1.5"
                    >
                        <img src="/assets/images/qua_nap.png" class="w-4 h-4 object-contain shrink-0" alt="Quả nạp" />
                        <span>Nạp thêm Quả</span>
                    </Link>
                    <button
                        type="button"
                        @click="dismiss"
                        class="py-2 px-3.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-xs rounded-xl transition-colors cursor-pointer"
                    >
                        Tạm ẩn
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
export default {
    name: 'DailyRewardPopup',
    data() {
        return {
            visible: false,
            rewardPoints: 10,
        }
    },
    computed: {
        isAuthenticated() {
            return !!this.$page.props.auth?.user
        },
        dailyReward() {
            return this.$page.props.auth?.daily_reward
        },
        adminNote() {
            return this.$page.props.auth?.admin_note || this.$page.props.auth?.user?.funky_note
        },
        activities() {
            return this.$page.props.auth?.recent_activities || []
        },
        hasUnreadBadge() {
            return !!this.adminNote || (this.dailyReward && this.dailyReward.granted && this.dailyReward.points > 0)
        },
        showRewardBanner() {
            return this.dailyReward && this.dailyReward.granted && this.dailyReward.points > 0
        },
        hasPointHistoryRoute() {
            try {
                return !!this.route('home')
            } catch (e) {
                return false
            }
        },
    },
    watch: {
        dailyReward: {
            immediate: true,
            handler(reward) {
                if (reward && reward.granted && reward.points > 0) {
                    const today = new Date().toISOString().slice(0, 10)
                    const dismissedKey = `daily_reward_dismissed_${today}`
                    
                    if (!sessionStorage.getItem(dismissedKey)) {
                        this.rewardPoints = reward.points
                        this.visible = true
                    }
                }
            },
        },
        adminNote: {
            immediate: true,
            handler(note) {
                if (note && note.trim()) {
                    const dismissedNoteKey = `admin_note_dismissed_${encodeURIComponent(note.slice(0, 30))}`
                    if (!sessionStorage.getItem(dismissedNoteKey)) {
                        this.visible = true
                    }
                }
            },
        },
    },
    mounted() {
        // Lắng nghe sự kiện toàn cục để mở popup từ Header hoặc các component khác
        window.addEventListener('open-activity-popup', this.openPopup)
    },
    beforeUnmount() {
        window.removeEventListener('open-activity-popup', this.openPopup)
    },
    methods: {
        openPopup() {
            this.visible = true
        },
        dismiss() {
            if (this.adminNote) {
                const dismissedNoteKey = `admin_note_dismissed_${encodeURIComponent(this.adminNote.slice(0, 30))}`
                sessionStorage.setItem(dismissedNoteKey, 'true')
            }
            const today = new Date().toISOString().slice(0, 10)
            const dismissedKey = `daily_reward_dismissed_${today}`
            sessionStorage.setItem(dismissedKey, 'true')
            this.visible = false
        },
        getActivityIcon(type) {
            switch (type) {
                case 'BONUS':
                    return '🎁'
                case 'SHARE_FACEBOOK':
                    return '🌐'
                case 'TOPUP':
                    return '⚡'
                case 'SPEND':
                    return '📥'
                case 'MANUAL_ADD':
                    return '➕'
                case 'MANUAL_SUBTRACT':
                    return '➖'
                default:
                    return '✨'
            }
        },
    },
}
</script>

<style scoped>
.slide-fade-right-enter-active {
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-fade-right-leave-active {
    transition: all 0.35s cubic-bezier(0.7, 0, 0.84, 0);
}

.slide-fade-right-enter-from {
    transform: translateX(120%);
    opacity: 0;
}

.slide-fade-right-enter-to {
    transform: translateX(0);
    opacity: 1;
}

.slide-fade-right-leave-from {
    transform: translateX(0);
    opacity: 1;
}

.slide-fade-right-leave-to {
    transform: translateX(120%);
    opacity: 0;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 4px;
}
</style>
