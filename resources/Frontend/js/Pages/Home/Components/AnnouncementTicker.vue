<template>
    <div
        v-if="validAnnouncements.length > 0"
        class="w-full max-w-[1279px] mx-auto px-4 sm:px-6 lg:px-0 my-3 md:my-5 select-none"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
        @touchstart.passive="handleTouchStart"
        @touchend="handleTouchEnd"
    >
        <!-- Capsule Frosted Gray Glassmorphism Container (Option A: Minimalist Center-Aligned) -->
        <div
            :class="[
                'bg-[#202533]/85 backdrop-blur-md border border-white/10 hover:border-white/20 rounded-[12px] shadow-[0_4px_20px_rgba(0,0,0,0.25)] px-4 py-3 sm:px-6 sm:py-3.5 transition-all duration-300 flex items-center justify-center relative overflow-hidden group',
                hasLink ? 'cursor-pointer' : ''
            ]"
            :role="hasLink ? 'button' : undefined"
            :tabindex="hasLink ? 0 : undefined"
            @click="handleCapsuleClick"
            @keydown.enter="handleCapsuleClick"
            @keydown.space.prevent="handleCapsuleClick"
        >
            <!-- Center: Mechanical 3D Split-Flap Ticker Area -->
            <div class="w-full min-w-0 flex items-center justify-center relative overflow-hidden h-[34px] sm:h-[38px] perspective-container">
                <transition name="split-flap" mode="out-in">
                    <div
                        :key="currentIndex"
                        class="split-flap-card w-full flex items-center justify-center text-center"
                    >
                        <span
                            class="text-sm sm:text-base md:text-[17px] font-medium text-slate-100 group-hover:text-white transition-colors truncate block max-w-full text-center mx-auto"
                            :title="currentItem.title"
                        >
                            {{ currentItem.title }}
                        </span>
                    </div>
                </transition>
            </div>
        </div>
    </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';

export default {
    name: 'AnnouncementTicker',
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
        currentUrl() {
            return this.getItemUrl(this.currentItem);
        },
        hasLink() {
            return !!this.currentUrl;
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
            if (!item) return '';
            return item.primary_cta_link || item.link || '';
        },
        isInternalLink(url) {
            return typeof url === 'string' && url.startsWith('/') && !url.startsWith('//');
        },
        handleCapsuleClick() {
            const url = this.currentUrl;
            if (!url) return;
            if (this.isInternalLink(url)) {
                if (this.$inertia) {
                    this.$inertia.visit(url);
                } else {
                    Inertia.visit(url);
                }
            } else {
                window.open(url, this.currentItem.target || '_blank', 'noopener,noreferrer');
            }
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
