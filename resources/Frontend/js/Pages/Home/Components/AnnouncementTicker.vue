<template>
    <div
        v-if="validAnnouncements.length > 0"
        class="w-full max-w-[1279px] mx-auto px-4 sm:px-6 lg:px-0 my-3 md:my-5 select-none"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
        @touchstart.passive="handleTouchStart"
        @touchend="handleTouchEnd"
    >
        <!-- Capsule Dark Tech Glassmorphism Container -->
        <div
            class="bg-[#111322]/85 backdrop-blur-md border border-[#2970FF]/30 hover:border-[#2970FF]/60 rounded-[12px] shadow-[0_8px_30px_rgba(0,0,0,0.35)] px-3.5 py-2.5 sm:px-6 sm:py-3 transition-all duration-300 flex items-center justify-between gap-3 sm:gap-4 relative overflow-hidden group"
        >
            <!-- Left: Category Badge with Glowing Neon LED Pulse -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <div
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[6px] bg-[#2970FF]/15 border border-[#2970FF]/30 text-[#84ADFF] text-[11px] sm:text-xs font-semibold tracking-wider uppercase shadow-[inset_0_0_12px_rgba(41,112,255,0.15)]"
                >
                    <!-- LED Neon Pulse -->
                    <span class="relative flex h-2 w-2 flex-shrink-0">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#2970FF] opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex rounded-full h-2 w-2 bg-[#2970FF] shadow-[0_0_8px_#2970FF]"
                        ></span>
                    </span>
                    <span class="whitespace-nowrap">{{ currentItem.subtitle || 'HOT UPDATE' }}</span>
                </div>
            </div>

            <!-- Center: Mechanical 3D Split-Flap Ticker Area -->
            <div class="flex-1 min-w-0 flex items-center relative overflow-hidden h-[34px] sm:h-[38px] perspective-container">
                <transition name="split-flap" mode="out-in">
                    <div
                        :key="currentIndex"
                        class="split-flap-card w-full flex items-center"
                    >
                        <component
                            :is="getLinkComponent(currentItem)"
                            v-bind="getLinkProps(currentItem)"
                            class="text-xs sm:text-sm font-medium text-gray-200 hover:text-white transition-colors truncate block w-full cursor-pointer outline-none"
                            :title="currentItem.title"
                        >
                            {{ currentItem.title }}
                        </component>
                    </div>
                </transition>
            </div>

            <!-- Right: Navigation Controls & CTA Button -->
            <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                <!-- Numeric Counter (Monospace) -->
                <span
                    v-if="hasMultiple"
                    class="text-[11px] sm:text-xs text-gray-400 font-mono tracking-wider tabular-nums select-none hidden sm:inline-block"
                >
                    {{ currentIndex + 1 }}/{{ totalCount }}
                </span>

                <!-- Prev / Next Navigation Arrows -->
                <div v-if="hasMultiple" class="flex items-center gap-1">
                    <button
                        type="button"
                        @click="prevItem"
                        class="w-6 h-6 sm:w-7 sm:h-7 rounded-[6px] bg-white/5 hover:bg-white/10 active:bg-white/20 text-gray-300 hover:text-white flex items-center justify-center transition-all border border-white/10 focus:outline-none"
                        aria-label="Previous announcement"
                    >
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        @click="nextItem"
                        class="w-6 h-6 sm:w-7 sm:h-7 rounded-[6px] bg-white/5 hover:bg-white/10 active:bg-white/20 text-gray-300 hover:text-white flex items-center justify-center transition-all border border-white/10 focus:outline-none"
                        aria-label="Next announcement"
                    >
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- CTA Action Link / Button -->
                <component
                    :is="getLinkComponent(currentItem)"
                    v-bind="getLinkProps(currentItem)"
                    v-if="hasCta(currentItem)"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[6px] bg-[#2970FF]/20 hover:bg-[#2970FF]/35 text-[#84ADFF] hover:text-white border border-[#2970FF]/40 text-xs font-medium transition-all shadow-sm"
                >
                    <span>{{ currentItem.primary_cta_text || tt('Xem ngay') }}</span>
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </component>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';

export default {
    name: 'AnnouncementTicker',
    components: {
        Link,
    },
    props: {
        announcements: {
            type: Array,
            required: false,
            default: () => [],
        },
    },
    data() {
        return {
            currentIndex: 0,
            timerId: null,
            isHovered: false,
            touchStartX: 0,
            touchEndX: 0,
        };
    },
    computed: {
        validAnnouncements() {
            if (!Array.isArray(this.announcements)) return [];
            return this.announcements.filter((item) => item && item.title && item.title.trim() !== '');
        },
        totalCount() {
            return this.validAnnouncements.length;
        },
        hasMultiple() {
            return this.totalCount > 1;
        },
        currentItem() {
            return this.validAnnouncements[this.currentIndex] || {};
        },
    },
    watch: {
        announcements: {
            handler() {
                this.currentIndex = 0;
                this.startTimer();
            },
            deep: true,
        },
    },
    mounted() {
        this.startTimer();
    },
    beforeUnmount() {
        this.clearTimer();
    },
    methods: {
        startTimer() {
            this.clearTimer();
            if (this.hasMultiple && !this.isHovered) {
                this.timerId = setInterval(() => {
                    this.nextItem();
                }, 5000);
            }
        },
        pauseTimer() {
            this.clearTimer();
        },
        clearTimer() {
            if (this.timerId) {
                clearInterval(this.timerId);
                this.timerId = null;
            }
        },
        nextItem() {
            if (this.totalCount <= 1) return;
            this.currentIndex = (this.currentIndex + 1) % this.totalCount;
        },
        prevItem() {
            if (this.totalCount <= 1) return;
            this.currentIndex = (this.currentIndex - 1 + this.totalCount) % this.totalCount;
        },
        handleMouseEnter() {
            this.isHovered = true;
            this.pauseTimer();
        },
        handleMouseLeave() {
            this.isHovered = false;
            this.startTimer();
        },
        handleTouchStart(e) {
            if (e.touches && e.touches[0]) {
                this.touchStartX = e.touches[0].clientX;
                this.pauseTimer();
            }
        },
        handleTouchEnd(e) {
            if (e.changedTouches && e.changedTouches[0]) {
                this.touchEndX = e.changedTouches[0].clientX;
                const diff = this.touchStartX - this.touchEndX;
                if (diff > 40) {
                    this.nextItem();
                } else if (diff < -40) {
                    this.prevItem();
                }
                setTimeout(() => {
                    if (!this.isHovered) {
                        this.startTimer();
                    }
                }, 3000);
            }
        },
        getItemUrl(item) {
            return item.primary_cta_link || item.link || '';
        },
        hasCta(item) {
            return !!this.getItemUrl(item);
        },
        isInternalLink(url) {
            return typeof url === 'string' && url.startsWith('/') && !url.startsWith('//');
        },
        getLinkComponent(item) {
            const url = this.getItemUrl(item);
            if (!url) return 'span';
            return this.isInternalLink(url) ? 'Link' : 'a';
        },
        getLinkProps(item) {
            const url = this.getItemUrl(item);
            if (!url) return {};
            if (this.isInternalLink(url)) {
                return { href: url };
            }
            return {
                href: url,
                target: item.target || '_blank',
                rel: 'noopener noreferrer',
            };
        },
    },
};
</script>

<style scoped>
.perspective-container {
    perspective: 1000px;
}

.split-flap-card {
    transform-style: preserve-3d;
    backface-visibility: hidden;
    will-change: transform, opacity;
}

/* 3D Mechanical Split-Flap Transitions */
.split-flap-enter-active,
.split-flap-leave-active {
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.5s ease;
    transform-style: preserve-3d;
    backface-visibility: hidden;
}

.split-flap-enter-from {
    transform: perspective(1000px) rotateX(90deg);
    opacity: 0;
}

.split-flap-leave-to {
    transform: perspective(1000px) rotateX(-90deg);
    opacity: 0;
}

/* Accessibility: prefers-reduced-motion fallback */
@media (prefers-reduced-motion: reduce) {
    .split-flap-enter-active,
    .split-flap-leave-active {
        transition: opacity 0.3s ease;
        transform: none !important;
    }
    .split-flap-enter-from,
    .split-flap-leave-to {
        transform: none !important;
        opacity: 0;
    }
}
</style>
