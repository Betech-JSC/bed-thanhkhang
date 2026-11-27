<template>
    <section class="bg-[#FAFAFA] pb-8 md:pb-16 lg:pb-[80px]">
        <div class="app-container-narrow flex flex-col gap-6 md:gap-[40px]">
            <!-- THANH TAB ĐIỀU HƯỚNG (Figma Node 1264:49359 / 49360) -->
            <div
                role="tablist"
                :aria-label="tt('Chi tiết sản phẩm')"
                class="border-b border-[#E3E8EF] flex flex-wrap items-center gap-x-5 gap-y-1 md:gap-[32px]"
            >
                <button
                    v-for="(tab, idx) in tabs"
                    :id="`pdp-tab-${tab.id}`"
                    :key="tab.id"
                    :ref="(el) => setTabButtonRef(tab.id, el)"
                    type="button"
                    role="tab"
                    :aria-selected="activeTab === tab.id"
                    :aria-controls="`pdp-panel-${tab.id}`"
                    :class="[
                        'shrink-0 whitespace-nowrap pt-3 pb-[14px] min-h-[44px] text-[15px] md:text-[16px] leading-[1.6] border-b-2 transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]',
                        activeTab === tab.id
                            ? 'border-[#005EA4] font-bold text-[#005EA4]'
                            : 'border-transparent font-medium text-[#4B5565] hover:text-[#005EA4]',
                    ]"
                    @click="selectTab(tab.id)"
                    @keydown.left.prevent="focusAdjacentTab(idx, -1)"
                    @keydown.right.prevent="focusAdjacentTab(idx, 1)"
                >
                    {{ tt(tab.label) }}
                </button>
            </div>

            <!-- NỘI DUNG TABS VỚI HIỆU ỨNG TRƯỢT NGANG TRANSLATE-X (ZERO WHITE-OUT) -->
            <div class="overflow-hidden">
                <div
                    :key="activeTab"
                    :class="[
                        'pdp-tab-panel',
                        hasInteractedTab
                            ? slideDirection === 'next'
                                ? 'pdp-tab-enter-next'
                                : 'pdp-tab-enter-prev'
                            : '',
                    ]"
                >
                    <div
                        v-if="activeTab === 'overview'"
                        id="pdp-panel-overview"
                        role="tabpanel"
                        aria-labelledby="pdp-tab-overview"
                        class="w-full max-w-[950px] flex flex-col gap-3"
                    >
                        <h2 class="text-[18px] md:text-[20px] font-bold leading-[1.6] text-[#001932] break-words">
                            {{ overviewTitle }}
                        </h2>

                        <div
                            v-if="sanitizedContent"
                            ref="pdpRichtextRef"
                            class="pdp-richtext min-w-0"
                            v-html="sanitizedContent"
                        />

                        <div
                            v-if="extraMetadataIntro || extraMetadataFeatures.length > 0"
                            class="flex flex-col gap-4"
                            :class="sanitizedContent ? 'mt-2' : ''"
                        >
                            <p
                                v-if="extraMetadataIntro"
                                class="text-[15px] md:text-[16px] leading-[1.6] text-[#364152] break-words"
                            >
                                {{ extraMetadataIntro }}
                            </p>

                            <div
                                v-for="(feat, fIdx) in extraMetadataFeatures"
                                :key="`pdp-feature-${fIdx}`"
                                class="flex flex-col gap-2"
                            >
                                <h3
                                    v-if="feat.title"
                                    class="text-[16px] md:text-[18px] font-bold leading-[1.5] text-[#001932] break-words"
                                >
                                    {{ feat.title }}
                                </h3>
                                <p
                                    v-for="(para, pIdx) in feat.paragraphs"
                                    :key="`pdp-feature-${fIdx}-p-${pIdx}`"
                                    class="text-[15px] md:text-[16px] leading-[1.6] text-[#364152] break-words"
                                >
                                    {{ para }}
                                </p>
                                <ul
                                    v-if="feat.bullets.length > 0"
                                    class="list-disc pl-5 space-y-1.5 text-[15px] md:text-[16px] leading-[1.6] text-[#364152]"
                                >
                                    <li
                                        v-for="(bullet, bIdx) in feat.bullets"
                                        :key="`pdp-feature-${fIdx}-b-${bIdx}`"
                                        class="break-words"
                                    >
                                        {{ bullet }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <p
                            v-if="!sanitizedContent && !extraMetadataIntro && extraMetadataFeatures.length === 0"
                            class="text-[15px] md:text-[16px] leading-[1.6] text-[#697586] break-words"
                        >
                            {{ overviewEmptyFallback }}
                        </p>
                    </div>
                    <div
                        v-else-if="activeTab === 'specs'"
                        id="pdp-panel-specs"
                        role="tabpanel"
                        aria-labelledby="pdp-tab-specs"
                        class="w-full"
                    >
                        <SpecTable :specifications="resolvedSpecifications" />
                    </div>
                    <div
                        v-else
                        id="pdp-panel-drawings"
                        role="tabpanel"
                        aria-labelledby="pdp-tab-drawings"
                        class="w-full flex flex-col gap-6 md:gap-8"
                    >
                        <!-- Lưới ảnh bản vẽ kỹ thuật -->
                        <div
                            v-if="drawingImages.length > 0"
                            :class="
                                drawingImages.length === 1
                                    ? 'grid grid-cols-1 gap-4 md:gap-8'
                                    : 'grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8'
                            "
                        >
                            <template v-for="(img, idx) in drawingImages" :key="`drawing-${img.url}-${idx}`">
                                <button
                                    v-if="!failedDrawingUrls[img.url]"
                                    type="button"
                                    :class="[
                                        'bg-white border border-[#E3E8EF] hover:border-[#005EA4] rounded-[4px] overflow-hidden p-2 md:p-3 flex flex-col items-center justify-center group cursor-zoom-in transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]',
                                        drawingImages.length === 1 ? 'w-full max-w-[560px]' : 'w-full',
                                    ]"
                                    :aria-label="`${tt('Phóng to bản vẽ')}: ${img.alt}`"
                                    @click="openDrawingModal(idx)"
                                >
                                    <img
                                        :src="img.url"
                                        :alt="img.alt"
                                        class="w-full h-auto max-h-[672px] object-contain select-none"
                                        loading="lazy"
                                        decoding="async"
                                        @error="markDrawingFailed(img.url)"
                                    />
                                    <span
                                        class="mt-2 inline-flex items-center gap-1.5 text-[12px] leading-[1.5] font-medium text-[#697586] group-hover:text-[#005EA4] transition-colors"
                                    >
                                        <svg
                                            width="14"
                                            height="14"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="w-3.5 h-3.5 shrink-0"
                                            aria-hidden="true"
                                        >
                                            <circle cx="11" cy="11" r="8" />
                                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                            <line x1="11" y1="8" x2="11" y2="14" />
                                            <line x1="8" y1="11" x2="14" y2="11" />
                                        </svg>
                                        <span>{{ tt('Phóng to bản vẽ') }}</span>
                                    </span>
                                </button>

                                <div
                                    v-else
                                    :class="[
                                        'bg-white border border-[#E3E8EF] rounded-[4px] p-6 min-h-[240px] flex flex-col items-center justify-center gap-2 text-center',
                                        drawingImages.length === 1 ? 'w-full max-w-[560px]' : 'w-full',
                                    ]"
                                >
                                    <span class="w-10 h-[3px] rounded-[2px] bg-[#CDD5DF]" aria-hidden="true" />
                                    <span class="text-[14px] text-[#697586]">
                                        {{ tt('Bản vẽ kỹ thuật & Tài liệu đang được cập nhật') }}
                                    </span>
                                </div>
                            </template>
                        </div>

                        <!-- Danh sách tài liệu kỹ thuật đính kèm (từ Product::posts()) -->
                        <div v-if="attachedDocuments.length > 0" class="flex flex-col gap-3 md:gap-4">
                            <h3 class="text-[16px] md:text-[18px] font-bold leading-[1.6] text-[#001932]">
                                {{ tt('Tài liệu kỹ thuật đính kèm') }}
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-6">
                                <template v-for="doc in attachedDocuments" :key="doc.key">
                                    <a
                                        v-if="doc.hasFile && doc.downloadUrl"
                                        :href="doc.downloadUrl"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        download
                                        :aria-label="`${tt('Tải xuống tài liệu')}: ${doc.title}`"
                                        :class="DOC_CARD_CLASS"
                                    >
                                        <div class="min-w-0 flex-1">
                                            <p
                                                class="text-[14px] md:text-[15px] leading-[1.5] font-bold text-[#00284B] group-hover:text-[#005EA4] line-clamp-2 break-words transition-colors"
                                            >
                                                {{ doc.title }}
                                            </p>
                                            <p
                                                v-if="doc.description"
                                                class="text-[13px] leading-[1.5] text-[#4B5565] line-clamp-1 break-words mt-0.5"
                                            >
                                                {{ doc.description }}
                                            </p>
                                        </div>
                                        <span :class="DOC_ICON_WRAP_CLASS" aria-hidden="true">
                                            <svg
                                                width="20"
                                                height="20"
                                                viewBox="0 0 40 40"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="w-5 h-5"
                                            >
                                                <path
                                                    d="M35 25V27C35 29.8003 35 31.2004 34.455 32.27C33.9757 33.2108 33.2108 33.9757 32.27 34.455C31.2004 35 29.8003 35 27 35H13C10.1997 35 8.79961 35 7.73005 34.455C6.78924 33.9757 6.02433 33.2108 5.54497 32.27C5 31.2004 5 29.8003 5 27V25M11.6667 16.6667L20 25L28.3333 16.6667M20 25V5"
                                                    stroke="currentColor"
                                                    stroke-width="4"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>
                                        </span>
                                    </a>

                                    <Link
                                        v-else-if="doc.isInternalLink && doc.url"
                                        :href="doc.url"
                                        :class="DOC_CARD_CLASS"
                                    >
                                        <div class="min-w-0 flex-1">
                                            <p
                                                class="text-[14px] md:text-[15px] leading-[1.5] font-bold text-[#00284B] group-hover:text-[#005EA4] line-clamp-2 break-words transition-colors"
                                            >
                                                {{ doc.title }}
                                            </p>
                                            <p
                                                v-if="doc.description"
                                                class="text-[13px] leading-[1.5] text-[#4B5565] line-clamp-1 break-words mt-0.5"
                                            >
                                                {{ doc.description }}
                                            </p>
                                        </div>
                                        <span :class="DOC_ICON_WRAP_CLASS" aria-hidden="true">
                                            <svg
                                                width="18"
                                                height="18"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="w-[18px] h-[18px]"
                                            >
                                                <path d="M5 12h14" />
                                                <path d="m12 5 7 7-7 7" />
                                            </svg>
                                        </span>
                                    </Link>

                                    <a
                                        v-else-if="doc.url"
                                        :href="doc.url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        :class="DOC_CARD_CLASS"
                                    >
                                        <div class="min-w-0 flex-1">
                                            <p
                                                class="text-[14px] md:text-[15px] leading-[1.5] font-bold text-[#00284B] group-hover:text-[#005EA4] line-clamp-2 break-words transition-colors"
                                            >
                                                {{ doc.title }}
                                            </p>
                                            <p
                                                v-if="doc.description"
                                                class="text-[13px] leading-[1.5] text-[#4B5565] line-clamp-1 break-words mt-0.5"
                                            >
                                                {{ doc.description }}
                                            </p>
                                        </div>
                                        <span :class="DOC_ICON_WRAP_CLASS" aria-hidden="true">
                                            <svg
                                                width="18"
                                                height="18"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="w-[18px] h-[18px]"
                                            >
                                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                                <polyline points="15 3 21 3 21 9" />
                                                <line x1="10" y1="14" x2="21" y2="3" />
                                            </svg>
                                        </span>
                                    </a>

                                    <button
                                        v-else
                                        type="button"
                                        :aria-label="`${tt('Yêu cầu tài liệu kỹ thuật')}: ${doc.title}`"
                                        :class="`${DOC_CARD_CLASS} text-left cursor-pointer`"
                                        @click="handleRequestDrawing(doc.title)"
                                    >
                                        <div class="min-w-0 flex-1">
                                            <p
                                                class="text-[14px] md:text-[15px] leading-[1.5] font-bold text-[#00284B] group-hover:text-[#005EA4] line-clamp-2 break-words transition-colors"
                                            >
                                                {{ doc.title }}
                                            </p>
                                            <p
                                                v-if="doc.description"
                                                class="text-[13px] leading-[1.5] text-[#4B5565] line-clamp-1 break-words mt-0.5"
                                            >
                                                {{ doc.description }}
                                            </p>
                                        </div>
                                        <span :class="DOC_ICON_WRAP_CLASS" aria-hidden="true">
                                            <svg
                                                width="20"
                                                height="20"
                                                viewBox="0 0 40 40"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="w-5 h-5"
                                            >
                                                <path
                                                    d="M35 25V27C35 29.8003 35 31.2004 34.455 32.27C33.9757 33.2108 33.2108 33.9757 32.27 34.455C31.2004 35 29.8003 35 27 35H13C10.1997 35 8.79961 35 7.73005 34.455C6.78924 33.9757 6.02433 33.2108 5.54497 32.27C5 31.2004 5 29.8003 5 27V25M11.6667 16.6667L20 25L28.3333 16.6667M20 25V5"
                                                    stroke="currentColor"
                                                    stroke-width="4"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>
                                        </span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Trạng thái rỗng sạch (4/103 SP chưa có drawing & không có posts) -->
                        <div
                            v-if="drawingImages.length === 0 && attachedDocuments.length === 0"
                            class="bg-white border border-[#E3E8EF] rounded-[4px] p-6 md:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
                        >
                            <div class="min-w-0 max-w-[680px]">
                                <p class="text-[16px] font-bold leading-[1.6] text-[#001932] break-words">
                                    {{ tt('Bản vẽ kỹ thuật & Tài liệu đang được cập nhật') }}
                                </p>
                                <p class="text-[14px] leading-[1.6] text-[#4B5565] mt-1 break-words">
                                    {{
                                        tt(
                                            'Liên hệ đội ngũ kỹ sư Thành Khang để nhận bản vẽ CAD và catalogue thông số chi tiết cho sản phẩm này.'
                                        )
                                    }}
                                </p>
                            </div>

                            <button
                                type="button"
                                class="shrink-0 min-h-[44px] px-5 py-2.5 rounded-[4px] bg-[#005EA4] hover:bg-[#004D86] active:bg-[#003967] text-[#FAFAFA] text-[14px] font-medium leading-[1.6] transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4] focus-visible:ring-offset-2"
                                @click="handleRequestDrawing()"
                            >
                                {{ tt('Yêu cầu bản vẽ & Tài liệu') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LIGHTBOX PHÓNG TO BẢN VẼ KỸ THUẬT -->
        <Teleport to="body">
            <div
                v-if="activeZoomDrawing"
                class="fixed inset-0 z-[9999] bg-black/85 backdrop-blur-sm flex items-center justify-center p-4 md:p-8"
                role="dialog"
                aria-modal="true"
                :aria-label="activeZoomDrawing.alt || tt('Bản vẽ kỹ thuật')"
                @click="closeDrawingModal"
            >
                <button
                    type="button"
                    class="absolute top-4 right-4 md:top-6 md:right-6 z-20 w-10 h-10 rounded-full bg-white/15 hover:bg-white/25 text-white flex items-center justify-center transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                    :aria-label="tt('Đóng phóng to')"
                    @click.stop="closeDrawingModal"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <button
                    v-if="drawingImages.length > 1"
                    type="button"
                    class="absolute left-3 md:left-6 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/15 hover:bg-white/25 text-white flex items-center justify-center transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                    :aria-label="tt('Bản vẽ trước')"
                    @click.stop="prevDrawing"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button
                    v-if="drawingImages.length > 1"
                    type="button"
                    class="absolute right-3 md:right-6 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white/15 hover:bg-white/25 text-white flex items-center justify-center transition-colors cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                    :aria-label="tt('Bản vẽ tiếp theo')"
                    @click.stop="nextDrawing"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <div
                    class="w-full max-w-5xl max-h-[88vh] overflow-hidden"
                    @click.stop
                >
                    <div
                        class="w-full flex items-center transition-transform duration-300 ease-out will-change-transform"
                        :style="{ transform: `translateX(-${zoomedDrawingIndex * 100}%)` }"
                    >
                        <div
                            v-for="(img, idx) in drawingImages"
                            :key="`drawing-zoom-${img.url}-${idx}`"
                            class="w-full shrink-0 flex items-center justify-center px-2"
                        >
                            <div class="bg-white rounded-[4px] p-2 sm:p-4 max-h-[86vh] overflow-auto shadow-2xl">
                                <img
                                    :src="img.url"
                                    :alt="img.alt"
                                    class="max-w-full max-h-[80vh] w-auto h-auto object-contain select-none"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </section>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'
import SpecTable from '@/Components/Product/SpecTable.vue'
import { openQuoteModal } from '@/composables/useQuoteModal'
import {
    hasVietnameseDiacritics,
    localizeRichHtmlForLocale,
    useTranslate,
} from '@/composables/useTranslate.js'

const DOC_CARD_CLASS =
    'group bg-white border border-[#E3E8EF] hover:border-[#005EA4] rounded-[4px] min-h-[72px] px-4 md:px-5 py-4 flex items-center justify-between gap-3 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#005EA4]'

const DOC_ICON_WRAP_CLASS =
    'w-[42px] h-[42px] rounded-[6px] bg-[#EEF2F6] group-hover:bg-[#E0ECF8] shrink-0 flex items-center justify-center text-[#697586] group-hover:text-[#005EA4] transition-colors'

const tabs = [
    { id: 'overview', label: 'Mô tả tổng quan' },
    { id: 'specs', label: 'Thông số & Cấu tạo' },
    { id: 'drawings', label: 'Bản vẽ & Tài liệu' },
]

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
})

const { translate, isEn } = useTranslate()

const activeTab = ref('overview')
const slideDirection = ref('next')
const hasInteractedTab = ref(false)
const tabButtonRefs = {}
const pdpRichtextRef = ref(null)
const zoomedDrawingIndex = ref(null)
const failedDrawingUrls = ref({})

function setTabButtonRef(id, el) {
    if (el) {
        tabButtonRefs[id] = el
    }
}

function selectTab(tabId) {
    if (tabId === activeTab.value) return
    const prevIdx = tabs.findIndex((t) => t.id === activeTab.value)
    const nextIdx = tabs.findIndex((t) => t.id === tabId)
    if (nextIdx === -1) return
    slideDirection.value = nextIdx > prevIdx ? 'next' : 'prev'
    hasInteractedTab.value = true
    activeTab.value = tabId
}

function focusAdjacentTab(currentIdx, delta) {
    const nextIdx = (currentIdx + delta + tabs.length) % tabs.length
    const nextTab = tabs[nextIdx]
    if (!nextTab) return
    selectTab(nextTab.id)
    tabButtonRefs[nextTab.id]?.focus?.()
}

function pickNonEmpty(...values) {
    for (const val of values) {
        if (typeof val === 'string' && val.trim() !== '') {
            return val.trim()
        }
    }
    return ''
}

function decodeBasicEntities(str) {
    return str
        .replace(/&amp;/gi, '&')
        .replace(/&lt;/gi, '<')
        .replace(/&gt;/gi, '>')
        .replace(/&quot;/gi, '"')
        .replace(/&#0*39;|&apos;/gi, "'")
        .replace(/&nbsp;/gi, ' ')
}

function normalizeForCompare(str) {
    if (typeof str !== 'string' || !str) return ''
    return decodeBasicEntities(str)
        .replace(/<[^>]+>/g, ' ')
        .replace(/[•·]/g, ' ')
        .replace(/\s+/g, ' ')
        .trim()
        .toLowerCase()
}

function sanitizeRichHtml(rawHtml) {
    if (typeof rawHtml !== 'string' || !rawHtml.trim()) return ''
    return rawHtml
        .replace(/<\s*(script|style|iframe|object|embed)\b[^>]*>[\s\S]*?<\s*\/\s*\1\s*>/gi, '')
        .replace(/<\s*(script|style|iframe|object|embed)\b[^>]*\/?>/gi, '')
        .replace(/\s+on[a-z]+\s*=\s*(?:"[^"]*"|'[^']*'|[^\s>]+)/gi, '')
        .replace(/\s+(href|src)\s*=\s*(?:"\s*(?:javascript|vbscript):[^"]*"|'\s*(?:javascript|vbscript):[^']*')/gi, '')
        .trim()
}

function isAlreadyInContent(candidate, normalizedContent) {
    const normCandidate = normalizeForCompare(candidate)
    if (!normCandidate) return true
    if (!normalizedContent) return false
    if (normalizedContent.includes(normCandidate)) return true

    const segments = candidate
        .split(/\r?\n|(?<=\.)\s+/)
        .map((seg) => normalizeForCompare(seg))
        .filter((seg) => seg.length >= 18)

    if (segments.length === 0) {
        return false
    }

    const matchedCount = segments.filter((seg) => normalizedContent.includes(seg)).length
    return matchedCount / segments.length >= 0.75
}

function splitParagraphsAndBullets(rawText) {
    const paragraphs = []
    const bullets = []
    if (typeof rawText !== 'string' || !rawText.trim()) {
        return { paragraphs, bullets }
    }

    for (const rawLine of rawText.split(/\r?\n/)) {
        const line = rawLine.trim()
        if (!line) continue
        if (line.startsWith('•') || line.startsWith('- ')) {
            const cleanBullet = line.replace(/^[•\-]\s*/, '').trim()
            if (cleanBullet) bullets.push(cleanBullet)
        } else {
            paragraphs.push(line)
        }
    }

    return { paragraphs, bullets }
}

function normalizeImageUrl(url) {
    if (!url || typeof url !== 'string') return ''
    let trimmed = url.trim()
    if (!trimmed) return ''
    trimmed = trimmed.replace(/\/static\/\/?(assets\/|images\/)/g, '/$1').replace(/\/static\/\//g, '/')
    if (/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?\/(assets|images)\//i.test(trimmed)) {
        return trimmed.replace(/^https?:\/\/[^/]+/i, '')
    }
    if (/^(assets|images|storage)\//i.test(trimmed)) {
        return `/${trimmed}`
    }
    return trimmed
}

function normalizeActionUrl(url) {
    if (typeof url !== 'string') return ''
    const trimmed = url.trim()
    if (!trimmed || trimmed === '#') return ''
    return normalizeImageUrl(trimmed)
}

const pdpMeta = computed(() =>
    props.product?.pdp_metadata && typeof props.product.pdp_metadata === 'object'
        ? props.product.pdp_metadata
        : {}
)

const currentTitle = computed(() => pickNonEmpty(props.product?.title, props.product?.name))

const currentCategory = computed(() =>
    pickNonEmpty(
        props.product?.category?.title,
        props.product?.category?.name,
        props.product?.categories?.[0]?.title,
        props.product?.categories?.[0]?.name
    )
)

function isSameAsProductTitle(candidate) {
    const normCandidate = normalizeForCompare(candidate)
    if (!normCandidate) return false
    const titleCandidates = [
        currentTitle.value,
        props.product?.title,
        props.product?.name,
    ]
    return titleCandidates.some((title) => {
        const normTitle = normalizeForCompare(title)
        return Boolean(normTitle && normCandidate === normTitle)
    })
}

const overviewTitle = computed(() => {
    const enOverride = pickNonEmpty(pdpMeta.value?.story_title_en)
    const rawStoryTitle = pickNonEmpty(props.product?.story_title, pdpMeta.value?.story_title)

    if (isEn.value) {
        if (enOverride && !isSameAsProductTitle(enOverride)) return enOverride
        if (
            rawStoryTitle &&
            !hasVietnameseDiacritics(rawStoryTitle) &&
            !isSameAsProductTitle(rawStoryTitle)
        ) {
            return rawStoryTitle
        }
        return translate('Tổng quan & Đặc điểm thiết kế')
    }

    if (rawStoryTitle && !isSameAsProductTitle(rawStoryTitle)) {
        return rawStoryTitle
    }
    return translate('Tổng quan & Đặc điểm thiết kế')
})

const sanitizedContent = computed(() => {
    const clean = sanitizeRichHtml(props.product?.content || '')
    return localizeRichHtmlForLocale(clean, isEn.value, translate)
})

const normalizedContentPlain = computed(() => normalizeForCompare(sanitizedContent.value))

const extraMetadataIntro = computed(() => {
    const rawIntro = isEn.value
        ? pickNonEmpty(pdpMeta.value?.story_intro_en, pdpMeta.value?.story_intro)
        : pickNonEmpty(props.product?.story_intro, pdpMeta.value?.story_intro)

    if (!rawIntro) return ''
    if (isEn.value && !pdpMeta.value?.story_intro_en && hasVietnameseDiacritics(rawIntro)) {
        return ''
    }
    if (isAlreadyInContent(rawIntro, normalizedContentPlain.value)) {
        return ''
    }
    return rawIntro
})

const extraMetadataFeatures = computed(() => {
    const pairs = [
        {
            title: isEn.value
                ? pickNonEmpty(pdpMeta.value?.feature1_title_en, pdpMeta.value?.feature1_title)
                : pickNonEmpty(props.product?.feature1_title, pdpMeta.value?.feature1_title),
            desc: isEn.value
                ? pickNonEmpty(pdpMeta.value?.feature1_desc_en, pdpMeta.value?.feature1_desc)
                : pickNonEmpty(props.product?.feature1_desc, pdpMeta.value?.feature1_desc),
            hasEnOverride: Boolean(
                pickNonEmpty(pdpMeta.value?.feature1_title_en, pdpMeta.value?.feature1_desc_en)
            ),
        },
        {
            title: isEn.value
                ? pickNonEmpty(pdpMeta.value?.feature2_title_en, pdpMeta.value?.feature2_title)
                : pickNonEmpty(props.product?.feature2_title, pdpMeta.value?.feature2_title),
            desc: isEn.value
                ? pickNonEmpty(pdpMeta.value?.feature2_desc_en, pdpMeta.value?.feature2_desc)
                : pickNonEmpty(props.product?.feature2_desc, pdpMeta.value?.feature2_desc),
            hasEnOverride: Boolean(
                pickNonEmpty(pdpMeta.value?.feature2_title_en, pdpMeta.value?.feature2_desc_en)
            ),
        },
    ]

    return pairs
        .map((pair) => {
            if (!pair.title && !pair.desc) return null
            if (
                isEn.value &&
                !pair.hasEnOverride &&
                (hasVietnameseDiacritics(pair.title) || hasVietnameseDiacritics(pair.desc))
            ) {
                return null
            }
            if (pair.desc && isAlreadyInContent(pair.desc, normalizedContentPlain.value)) {
                return null
            }
            if (!pair.desc && pair.title && isAlreadyInContent(pair.title, normalizedContentPlain.value)) {
                return null
            }
            const { paragraphs, bullets } = splitParagraphsAndBullets(pair.desc)
            return {
                title: pair.title,
                paragraphs,
                bullets,
            }
        })
        .filter(Boolean)
})

const overviewEmptyFallback = computed(() => {
    const shortDesc = pickNonEmpty(props.product?.description, props.product?.short_description)
    return shortDesc || translate('Thông tin mô tả chi tiết đang được cập nhật.')
})

const resolvedSpecifications = computed(() => {
    if (Array.isArray(props.product?.specifications) && props.product.specifications.length > 0) {
        return props.product.specifications
    }
    if (Array.isArray(pdpMeta.value?.specifications) && pdpMeta.value.specifications.length > 0) {
        return pdpMeta.value.specifications
    }
    return []
})

const drawingImages = computed(() => {
    const rawImages = Array.isArray(props.product?.images) ? props.product.images : []
    const seenUrls = new Set()
    const list = []

    rawImages.forEach((item) => {
        if (!item) return
        const isObj = typeof item === 'object'
        const rawUrl = typeof item === 'string' ? item : item.url || item.path || ''
        const candidates = isObj
            ? [item.url, item.path, item.name, item.tag]
            : [item]
        const matchesDrawing = candidates.some(
            (field) => typeof field === 'string' && /drawing/i.test(field)
        )
        if (!matchesDrawing) return

        const url = normalizeImageUrl(rawUrl)
        if (!url || seenUrls.has(url)) return
        seenUrls.add(url)

        const rawAlt = isObj ? pickNonEmpty(item.alt, item.title, item.name) : ''
        const fallbackAlt = currentTitle.value
            ? `${currentTitle.value} - ${translate('Bản vẽ kỹ thuật')} ${list.length + 1}`
            : `${translate('Bản vẽ kỹ thuật')} ${list.length + 1}`

        list.push({
            url,
            alt:
                rawAlt &&
                rawAlt !== currentTitle.value &&
                !(isEn.value && hasVietnameseDiacritics(rawAlt))
                    ? rawAlt
                    : fallbackAlt,
        })
    })

    return list
})

const attachedDocuments = computed(() => {
    const rawDocs =
        Array.isArray(props.product?.documents) && props.product.documents.length > 0
            ? props.product.documents
            : Array.isArray(props.product?.posts)
            ? props.product.posts
            : []

    return rawDocs
        .map((doc, idx) => {
            if (!doc || typeof doc !== 'object') return null
            const title = pickNonEmpty(doc.title, doc.name)
            if (!title) return null
            const downloadUrl = normalizeActionUrl(doc.download_url || doc.file_url || '')
            const url = normalizeActionUrl(doc.url || doc.link || '') || downloadUrl
            const hasFile = Boolean(doc.has_file && downloadUrl)
            const isInternalLink = Boolean(url && url.startsWith('/') && !url.startsWith('//'))

            return {
                key: `pdp-doc-${doc.id ?? idx}-${doc.slug || title}`,
                title,
                description: pickNonEmpty(doc.description),
                downloadUrl,
                url,
                hasFile,
                isInternalLink,
            }
        })
        .filter(Boolean)
})

const activeZoomDrawing = computed(() => {
    if (zoomedDrawingIndex.value === null) return null
    return drawingImages.value[zoomedDrawingIndex.value] || null
})

function markDrawingFailed(url) {
    if (!url) return
    failedDrawingUrls.value = { ...failedDrawingUrls.value, [url]: true }
}

function openDrawingModal(index) {
    if (index < 0 || index >= drawingImages.value.length) return
    zoomedDrawingIndex.value = index
}

function closeDrawingModal() {
    zoomedDrawingIndex.value = null
}

function prevDrawing() {
    if (drawingImages.value.length <= 1 || zoomedDrawingIndex.value === null) return
    zoomedDrawingIndex.value =
        zoomedDrawingIndex.value === 0
            ? drawingImages.value.length - 1
            : zoomedDrawingIndex.value - 1
}

function nextDrawing() {
    if (drawingImages.value.length <= 1 || zoomedDrawingIndex.value === null) return
    zoomedDrawingIndex.value =
        zoomedDrawingIndex.value === drawingImages.value.length - 1
            ? 0
            : zoomedDrawingIndex.value + 1
}

function handleLightboxKeydown(event) {
    if (zoomedDrawingIndex.value === null) return
    if (event.key === 'Escape') {
        closeDrawingModal()
    } else if (event.key === 'ArrowLeft') {
        prevDrawing()
    } else if (event.key === 'ArrowRight') {
        nextDrawing()
    }
}

watch(activeZoomDrawing, (isOpen) => {
    if (typeof window === 'undefined') return
    if (isOpen) {
        window.addEventListener('keydown', handleLightboxKeydown)
        document.body.classList.add('overflow-hidden')
    } else {
        window.removeEventListener('keydown', handleLightboxKeydown)
        document.body.classList.remove('overflow-hidden')
    }
})

watch(
    () => props.product?.id || props.product?.slug,
    () => {
        activeTab.value = 'overview'
        slideDirection.value = 'next'
        hasInteractedTab.value = false
        failedDrawingUrls.value = {}
        closeDrawingModal()
    }
)

async function wrapPdpTables() {
    await nextTick()
    if (typeof document === 'undefined') return
    const container = pdpRichtextRef.value
    if (!container) return

    const tables = container.querySelectorAll('table')
    tables.forEach((table) => {
        if (table.parentNode && table.parentElement && !table.parentElement.classList.contains('pdp-table-wrap')) {
            const wrapper = document.createElement('div')
            wrapper.className = 'pdp-table-wrap'
            table.parentNode.insertBefore(wrapper, table)
            wrapper.appendChild(table)
        }
    })
}

watch(
    [sanitizedContent, activeTab],
    ([, currentTab]) => {
        if (currentTab === 'overview') {
            wrapPdpTables()
        }
    }
)

onMounted(() => {
    if (activeTab.value === 'overview') {
        wrapPdpTables()
    }
})

onBeforeUnmount(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('keydown', handleLightboxKeydown)
        document.body.classList.remove('overflow-hidden')
    }
})

function handleRequestDrawing(documentTitle = '') {
    openQuoteModal({
        source: 'product_detail_drawing',
        product: documentTitle ? `${currentTitle.value} (${documentTitle})` : currentTitle.value,
        sku: pickNonEmpty(props.product?.sku),
        category: currentCategory.value,
    })
}
</script>

<style scoped>
.pdp-tab-panel {
    opacity: 1;
}

.pdp-tab-enter-next {
    animation: pdpTabEnterNext 240ms cubic-bezier(0.22, 1, 0.36, 1);
}

.pdp-tab-enter-prev {
    animation: pdpTabEnterPrev 240ms cubic-bezier(0.22, 1, 0.36, 1);
}

@keyframes pdpTabEnterNext {
    from {
        transform: translateX(16px);
    }
    to {
        transform: translateX(0);
    }
}

@keyframes pdpTabEnterPrev {
    from {
        transform: translateX(-16px);
    }
    to {
        transform: translateX(0);
    }
}

.pdp-richtext {
    font-size: 15px;
    line-height: 1.6;
    color: #364152;
    word-break: break-word;
}

@media (min-width: 768px) {
    .pdp-richtext {
        font-size: 16px;
    }
}

.pdp-richtext :deep(h2),
.pdp-richtext :deep(h3),
.pdp-richtext :deep(h4) {
    color: #001932;
    font-weight: 700;
    line-height: 1.5;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
}

.pdp-richtext :deep(> h2:first-child),
.pdp-richtext :deep(> h3:first-child),
.pdp-richtext :deep(> h4:first-child) {
    margin-top: 0;
}

.pdp-richtext :deep(h2) {
    font-size: 18px;
}

.pdp-richtext :deep(h3) {
    font-size: 16px;
}

.pdp-richtext :deep(h4) {
    font-size: 15px;
}

@media (min-width: 768px) {
    .pdp-richtext :deep(h2) {
        font-size: 20px;
    }

    .pdp-richtext :deep(h3) {
        font-size: 18px;
    }

    .pdp-richtext :deep(h4) {
        font-size: 16px;
    }
}

.pdp-richtext :deep(p) {
    font-size: 15px;
    line-height: 1.6;
    color: #364152;
    margin-bottom: 0.75rem;
}

@media (min-width: 768px) {
    .pdp-richtext :deep(p) {
        font-size: 16px;
    }
}

.pdp-richtext :deep(p:last-child) {
    margin-bottom: 0;
}

.pdp-richtext :deep(strong),
.pdp-richtext :deep(b) {
    color: #001932;
    font-weight: 700;
}

.pdp-richtext :deep(ul) {
    list-style-type: disc;
    padding-left: 1.25rem;
    margin-bottom: 1rem;
}

.pdp-richtext :deep(ol) {
    list-style-type: decimal;
    padding-left: 1.25rem;
    margin-bottom: 1rem;
}

.pdp-richtext :deep(ul:last-child),
.pdp-richtext :deep(ol:last-child) {
    margin-bottom: 0;
}

.pdp-richtext :deep(li) {
    line-height: 1.6;
    color: #364152;
    margin-bottom: 0.375rem;
}

.pdp-richtext :deep(li:last-child) {
    margin-bottom: 0;
}

.pdp-richtext :deep(a) {
    color: #005ea4;
    text-decoration: underline;
    text-underline-offset: 2px;
}

.pdp-richtext :deep(a:hover) {
    color: #004d86;
}

.pdp-richtext :deep(blockquote) {
    background-color: #eef2f6;
    border-left: 4px solid #005ea4;
    border-radius: 0 4px 4px 0;
    padding: 0.75rem 1rem;
    margin: 1rem 0;
    color: #364152;
}

.pdp-richtext :deep(.pdp-table-wrap) {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
    margin: 1rem 0;
    border: 1px solid #e3e8ef;
    border-radius: 4px;
    background-color: #ffffff;
}

.pdp-richtext :deep(.pdp-table-wrap table) {
    margin: 0;
    border: 0;
}

.pdp-richtext :deep(table) {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
    border: 1px solid #e3e8ef;
    background-color: #ffffff;
    font-size: 14px;
    line-height: 1.6;
}

.pdp-richtext :deep(thead th) {
    background-color: #00284b;
    color: #fafafa;
    font-weight: 700;
    text-align: left;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e3e8ef;
}

.pdp-richtext :deep(tbody td) {
    padding: 0.75rem 1rem;
    color: #364152;
    border-bottom: 1px solid #e3e8ef;
}

.pdp-richtext :deep(tbody tr:nth-child(even)) {
    background-color: #f8fafc;
}

.pdp-richtext :deep(img) {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
    margin: 1rem 0;
}
</style>
