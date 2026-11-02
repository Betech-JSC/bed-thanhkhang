<template>
    <div
        class="bg-white rounded-[2rem] sm:rounded-[2.5rem] p-5 sm:p-6 lg:p-7 shadow-[0_10px_40px_rgb(0,0,0,0.03)] border border-orange-100/80 relative overflow-hidden transition-all duration-300 hover:shadow-[0_15px_50px_rgb(0,0,0,0.05)]"
    >
        <!-- Background Ambient Glow & Subtle Pattern -->
        <div class="absolute top-0 right-0 w-80 h-80 bg-orange-400/10 rounded-full blur-[100px] pointer-events-none -z-0"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-red-fks/5 rounded-full blur-[100px] pointer-events-none -z-0"></div>
        <div
            class="absolute inset-0 z-0 opacity-[0.03] pointer-events-none"
            style="background-image: radial-gradient(circle, #D34000 1px, transparent 1px); background-size: 16px 16px;"
        ></div>

        <div class="relative z-10">
            <!-- 1. HEADER AREA -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <!-- Badge tag: Trung tâm người dùng -->
                    <span
                        class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-red-fks/10 border border-red-fks/15 text-red-fks font-bold text-xs mb-2 shadow-2xs"
                    >
                        <i class="pi pi-shield text-xs"></i>
                        <span class="tracking-wide uppercase font-beau">Trung tâm người dùng</span>
                    </span>

                    <!-- Main Title: HÀNH TRÌNH TÍCH LŨY QUẢ* -->
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-display font-black text-gray-warm-900 tracking-tight leading-tight">
                        HÀNH TRÌNH <span class="text-red-fks">TÍCH LŨY QUẢ</span><span class="text-red-fks">*</span>
                    </h2>

                    <!-- Subtitle -->
                    <p class="text-xs sm:text-sm text-gray-warm-500 font-beau font-medium mt-1">
                        Tích lũy Quả để mở khóa danh hiệu mới
                    </p>
                </div>

                <!-- Right Corner: Sticker "QUẢ NHỎ TẠO NÊN ĐIỀU LỚN" and Fruit Basket -->
                <div
                    class="flex items-center gap-3 bg-gradient-to-r from-amber-50/90 via-orange-50/80 to-amber-50/90 border border-amber-200/80 rounded-2xl p-2.5 pr-4 shadow-xs self-start sm:self-center transition-transform hover:scale-[1.02]"
                >
                    <div
                        class="w-12 h-12 rounded-xl bg-white flex items-center justify-center p-1.5 shadow-2xs border border-amber-100 shrink-0"
                    >
                        <img
                            src="/assets/images/khoqua.png"
                            alt="Kho Quả"
                            class="w-full h-full object-contain drop-shadow-sm"
                            @error="(e) => e.target.src = '/assets/images/qua_nap.png'"
                        />
                    </div>
                    <div class="text-left">
                        <span
                            class="inline-block px-2 py-0.5 rounded-md bg-green-fks text-blue-fks text-[9px] font-black font-display uppercase tracking-wider mb-0.5"
                        >
                            Funky Spirit
                        </span>
                        <p class="font-display font-black text-[11px] sm:text-xs text-orange-950 uppercase leading-snug">
                            QUẢ NHỎ TẠO NÊN<br />
                            <span class="text-red-fks">ĐIỀU LỚN</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- 2. TOP SUMMARY PROGRESS BAR -->
            <div
                class="bg-gradient-to-br from-orange-50/60 via-white/90 to-amber-50/50 backdrop-blur-md rounded-2xl sm:rounded-3xl p-5 border border-orange-200/70 shadow-xs mb-8"
            >
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    <!-- Left Column: User info & Points Breakdown -->
                    <div class="lg:col-span-6 flex items-center gap-4">
                        <div class="relative shrink-0">
                            <div
                                class="w-16 h-16 rounded-2xl bg-white border-2 border-orange-300 shadow-md flex items-center justify-center overflow-hidden"
                            >
                                <img
                                    v-if="userAvatarUrl"
                                    :src="userAvatarUrl"
                                    :alt="userName"
                                    class="w-full h-full object-cover"
                                />
                                <i v-else class="pi pi-user text-3xl text-gray-300"></i>
                            </div>
                            <div
                                class="absolute -bottom-1 -right-1 w-6 h-6 rounded-lg bg-green-fks border-2 border-white flex items-center justify-center shadow-xs"
                            >
                                <i class="pi pi-star-fill text-[9px] text-blue-fks"></i>
                            </div>
                        </div>

                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg sm:text-xl font-display font-black text-gray-warm-900 truncate">
                                {{ userName }}
                            </h3>
                            <p class="text-xs text-gray-warm-600 font-beau mt-0.5">
                                Số Quả hiện có:
                                <strong class="text-red-fks font-black">{{ formatNumber(userPoints) }} Quả</strong>
                                <span class="text-gray-warm-400 text-[11px] ml-1">
                                    ({{ formatNumber(rewardPoints) }} THƯỞNG, {{ formatNumber(topupPoints) }} NẠP)
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Right Column: Current Rank & Target to Next -->
                    <div class="lg:col-span-6 flex flex-col justify-center bg-white/70 rounded-2xl p-4 border border-orange-100 shadow-2xs">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-green-fks text-blue-fks font-display font-black text-[10px] tracking-wider uppercase shadow-2xs"
                                >
                                    <i class="pi pi-crown text-[9px]"></i>
                                    Hạng hiện tại
                                </span>
                                <h4 class="text-xl sm:text-2xl font-display font-black text-blue-fks uppercase tracking-tight mt-0.5">
                                    {{ currentRankName }}
                                </h4>
                                <p class="text-[11px] text-gray-warm-400 italic font-beau">
                                    Hạng tính theo số Quả hiện có
                                </p>
                            </div>

                            <!-- Big Rank Avatar Icon -->
                            <div
                                class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white border-2 border-orange-300 shadow-md p-1 flex items-center justify-center shrink-0"
                            >
                                <img
                                    v-if="currentRankIcon"
                                    :src="currentRankIcon"
                                    :alt="currentRankName"
                                    class="w-full h-full object-cover rounded-xl"
                                    @error="(e) => e.target.src = '/assets/images/ranks/01_Nguoi_lam_vuon.png'"
                                />
                                <i v-else class="pi pi-shield text-2xl text-orange-400"></i>
                            </div>
                        </div>

                        <!-- Target to next rank progress -->
                        <div class="mt-3 pt-3 border-t border-orange-100/70">
                            <div class="flex items-center justify-between text-xs font-beau">
                                <span v-if="nextRank" class="text-gray-warm-600 font-medium">
                                    Tiếp theo: <strong class="text-gray-warm-900 font-black font-display uppercase">{{ nextRank.name }}</strong>
                                    - {{ formatNumber(nextRank.min_points) }} QUẢ
                                    <span class="text-red-fks font-bold">(còn thiếu {{ formatNumber(pointsToNext) }} Quả)</span>
                                </span>
                                <span v-else class="text-amber-700 font-bold font-display text-xs">
                                    👑 Bạn đã đạt cấp bậc vương giả cao nhất!
                                </span>
                                <span class="font-display font-black text-red-fks text-xs">
                                    {{ progressPercent }}%
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-orange-100/90 rounded-full h-3 sm:h-3.5 mt-2 overflow-hidden border border-orange-200/70 p-0.5 shadow-inner">
                                <div
                                    class="progress-fill h-full rounded-full transition-all duration-700"
                                    :style="{ width: `${Math.min(100, Math.max(0, progressPercent))}%` }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile hint for horizontal scroll -->
            <div class="lg:hidden flex items-center justify-center gap-1.5 text-xs text-orange-600/80 font-bold mb-3 font-beau">
                <span>👉</span>
                <span>Vuốt ngang để khám phá toàn bộ 8 mốc hành trình</span>
            </div>

            <!-- 3. CONNECTING PATHWAY: 8 MILESTONES (2 ROWS) -->
            <div class="overflow-x-auto lg:overflow-x-visible pb-6 custom-scrollbar">
                <div class="min-w-[760px] sm:min-w-[820px] lg:min-w-0 w-full px-2 sm:px-4 pt-0 pb-6">
                    <div class="relative w-full">
                        <!-- SVG Connecting Pathway -->
                        <svg
                            class="absolute inset-0 w-full pointer-events-none z-0"
                            style="height: 500px;"
                            viewBox="0 0 1000 500"
                            preserveAspectRatio="none"
                            fill="none"
                        >
                            <path
                                d="M 125 64 L 916 64 A 24 24 0 0 1 940 88 L 940 231 A 24 24 0 0 1 916 255 L 84 255 A 24 24 0 0 0 60 279 L 60 330 A 24 24 0 0 0 84 354 L 875 354"
                                stroke="#E8B98A"
                                stroke-width="1.75"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                vector-effect="non-scaling-stroke"
                            />
                        </svg>

                    <!-- Arrow nodes between items in Row 1 -->
                    <div
                        class="absolute w-5 h-5 rounded-full bg-white border border-[#E8B98A] shadow-2xs flex items-center justify-center -translate-x-1/2 -translate-y-1/2 z-10 pointer-events-none"
                        style="left: 25%; top: 64px;"
                    >
                        <svg class="w-2.5 h-2.5 translate-x-[0.5px]" viewBox="0 0 6 10" fill="none">
                            <path d="M 1 1 L 5 5 L 1 9" stroke="#E5A86D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div
                        class="absolute w-5 h-5 rounded-full bg-white border border-[#E8B98A] shadow-2xs flex items-center justify-center -translate-x-1/2 -translate-y-1/2 z-10 pointer-events-none"
                        style="left: 50%; top: 64px;"
                    >
                        <svg class="w-2.5 h-2.5 translate-x-[0.5px]" viewBox="0 0 6 10" fill="none">
                            <path d="M 1 1 L 5 5 L 1 9" stroke="#E5A86D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div
                        class="absolute w-5 h-5 rounded-full bg-white border border-[#E8B98A] shadow-2xs flex items-center justify-center -translate-x-1/2 -translate-y-1/2 z-10 pointer-events-none"
                        style="left: 75%; top: 64px;"
                    >
                        <svg class="w-2.5 h-2.5 translate-x-[0.5px]" viewBox="0 0 6 10" fill="none">
                            <path d="M 1 1 L 5 5 L 1 9" stroke="#E5A86D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

                    <!-- Arrow nodes between items in Row 2 -->
                    <div
                        class="absolute w-5 h-5 rounded-full bg-white border border-[#E8B98A] shadow-2xs flex items-center justify-center -translate-x-1/2 -translate-y-1/2 z-10 pointer-events-none"
                        style="left: 25%; top: 354px;"
                    >
                        <svg class="w-2.5 h-2.5 translate-x-[0.5px]" viewBox="0 0 6 10" fill="none">
                            <path d="M 1 1 L 5 5 L 1 9" stroke="#E5A86D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div
                        class="absolute w-5 h-5 rounded-full bg-white border border-[#E8B98A] shadow-2xs flex items-center justify-center -translate-x-1/2 -translate-y-1/2 z-10 pointer-events-none"
                        style="left: 50%; top: 354px;"
                    >
                        <svg class="w-2.5 h-2.5 translate-x-[0.5px]" viewBox="0 0 6 10" fill="none">
                            <path d="M 1 1 L 5 5 L 1 9" stroke="#E5A86D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div
                        class="absolute w-5 h-5 rounded-full bg-white border border-[#E8B98A] shadow-2xs flex items-center justify-center -translate-x-1/2 -translate-y-1/2 z-10 pointer-events-none"
                        style="left: 75%; top: 354px;"
                    >
                        <svg class="w-2.5 h-2.5 translate-x-[0.5px]" viewBox="0 0 6 10" fill="none">
                            <path d="M 1 1 L 5 5 L 1 9" stroke="#E5A86D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

                    <!-- ROW 1: MỐC 01 -> MỐC 04 -->
                    <div class="grid grid-cols-4 relative z-10 h-[240px]">
                        <div
                            v-for="(milestone, index) in row1"
                            :key="milestone.id || index"
                            class="flex flex-col items-center text-center relative px-1 h-full group"
                        >
                            <!-- Circle illustration (128px x 128px) -->
                            <div class="relative w-32 h-32 min-w-[128px] min-h-[128px] max-w-[128px] max-h-[128px] shrink-0 flex items-center justify-center">
                                <div
                                    class="w-32 h-32 min-w-[128px] min-h-[128px] max-w-[128px] max-h-[128px] rounded-full bg-white p-2 flex items-center justify-center overflow-hidden transition-all duration-300 relative z-10"
                                    :class="[
                                        isCurrent(milestone)
                                            ? 'border-2 border-amber-400 shadow-[0_0_25px_rgba(245,158,11,0.35)] ring-2 ring-amber-300/40'
                                            : isAchieved(milestone)
                                                ? 'border-2 border-amber-300/60 shadow-xs hover:border-amber-400'
                                                : 'border-2 border-gray-200/80'
                                    ]"
                                >
                                    <img
                                        v-if="milestone.avatar_icon_url"
                                        :src="milestone.avatar_icon_url"
                                        :alt="milestone.name"
                                        class="w-full h-full object-cover rounded-full transition-all duration-300"
                                        :class="!isAchieved(milestone) ? 'grayscale opacity-60 contrast-90' : ''"
                                        @error="(e) => e.target.src = '/assets/images/ranks/01_Nguoi_lam_vuon.png'"
                                    />
                                    <i v-else class="pi pi-crown text-3xl text-amber-400"></i>
                                </div>

                                <!-- Current badge (HIỆN TẠI) -->
                                <div
                                    v-if="isCurrent(milestone)"
                                    class="absolute -bottom-3 left-1/2 -translate-x-1/2 z-20"
                                >
                                    <span
                                        class="inline-block px-3 py-0.5 rounded-full bg-red-fks text-white border border-amber-300/80 shadow-xs text-[10px] font-display font-black tracking-wider uppercase whitespace-nowrap"
                                    >
                                        HIỆN TẠI
                                    </span>
                                </div>

                                <!-- Locked badge (Lock icon) -->
                                <div
                                    v-else-if="!isAchieved(milestone)"
                                    class="absolute -bottom-2.5 left-1/2 -translate-x-1/2 z-20 w-6 h-6 rounded-full bg-white border border-gray-300 text-gray-500 shadow-2xs flex items-center justify-center"
                                >
                                    <i class="pi pi-lock text-[10px]"></i>
                                </div>
                            </div>

                            <!-- Title -->
                            <h4
                                class="tracking-wide text-xs sm:text-sm mt-3 uppercase transition-colors"
                                :class="[
                                    isCurrent(milestone)
                                        ? 'text-red-fks font-black font-display'
                                        : isAchieved(milestone)
                                            ? 'font-black font-display text-gray-900'
                                            : 'text-gray-500 font-bold font-display'
                                ]"
                            >
                                {{ milestone.name }}
                            </h4>

                            <!-- Description -->
                            <p class="font-beau text-gray-500 text-[11px] sm:text-xs leading-relaxed max-w-[140px] sm:max-w-[155px] mx-auto text-center mt-1 min-h-[32px]">
                                {{ milestone.description }}
                            </p>

                            <!-- Sắp mở pill if locked -->
                            <div class="h-6 flex items-center justify-center mt-1">
                                <span
                                    v-if="!isAchieved(milestone)"
                                    class="inline-block px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-500 text-[10px] font-display font-bold uppercase tracking-wider border border-gray-200"
                                >
                                    SẮP MỞ
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Middle Spacer (Connecting pathway runs through here) -->
                    <div class="h-[50px]"></div>

                    <!-- ROW 2: MỐC 05 -> MỐC 08 -->
                    <div class="grid grid-cols-4 relative z-10 h-[240px]">
                        <div
                            v-for="(milestone, index) in row2"
                            :key="milestone.id || (index + 4)"
                            class="flex flex-col items-center text-center relative px-1 h-full group"
                        >
                            <!-- Circle illustration (128px x 128px) -->
                            <div class="relative w-32 h-32 min-w-[128px] min-h-[128px] max-w-[128px] max-h-[128px] shrink-0 flex items-center justify-center">
                                <div
                                    class="w-32 h-32 min-w-[128px] min-h-[128px] max-w-[128px] max-h-[128px] rounded-full bg-white p-2 flex items-center justify-center overflow-hidden transition-all duration-300 relative z-10"
                                    :class="[
                                        isCurrent(milestone)
                                            ? 'border-2 border-amber-400 shadow-[0_0_25px_rgba(245,158,11,0.35)] ring-2 ring-amber-300/40'
                                            : isAchieved(milestone)
                                                ? 'border-2 border-amber-300/60 shadow-xs hover:border-amber-400'
                                                : 'border-2 border-gray-200/80'
                                    ]"
                                >
                                    <img
                                        v-if="milestone.avatar_icon_url"
                                        :src="milestone.avatar_icon_url"
                                        :alt="milestone.name"
                                        class="w-full h-full object-cover rounded-full transition-all duration-300"
                                        :class="!isAchieved(milestone) ? 'grayscale opacity-60 contrast-90' : ''"
                                        @error="(e) => e.target.src = '/assets/images/ranks/08_Vua_mua_vang.png'"
                                    />
                                    <i v-else class="pi pi-crown text-3xl text-amber-400"></i>
                                </div>

                                <!-- Current badge (HIỆN TẠI) -->
                                <div
                                    v-if="isCurrent(milestone)"
                                    class="absolute -bottom-3 left-1/2 -translate-x-1/2 z-20"
                                >
                                    <span
                                        class="inline-block px-3 py-0.5 rounded-full bg-red-fks text-white border border-amber-300/80 shadow-xs text-[10px] font-display font-black tracking-wider uppercase whitespace-nowrap"
                                    >
                                        HIỆN TẠI
                                    </span>
                                </div>

                                <!-- Locked badge (Lock icon) -->
                                <div
                                    v-else-if="!isAchieved(milestone)"
                                    class="absolute -bottom-2.5 left-1/2 -translate-x-1/2 z-20 w-6 h-6 rounded-full bg-white border border-gray-300 text-gray-500 shadow-2xs flex items-center justify-center"
                                >
                                    <i class="pi pi-lock text-[10px]"></i>
                                </div>
                            </div>

                            <!-- Title -->
                            <h4
                                class="tracking-wide text-xs sm:text-sm mt-3 uppercase transition-colors"
                                :class="[
                                    isCurrent(milestone)
                                        ? 'text-red-fks font-black font-display'
                                        : isAchieved(milestone)
                                            ? 'font-black font-display text-gray-900'
                                            : 'text-gray-500 font-bold font-display'
                                ]"
                            >
                                {{ milestone.name }}
                            </h4>

                            <!-- Description -->
                            <p class="font-beau text-gray-500 text-[11px] sm:text-xs leading-relaxed max-w-[140px] sm:max-w-[155px] mx-auto text-center mt-1 min-h-[32px]">
                                {{ milestone.description }}
                            </p>

                            <!-- Sắp mở pill if locked -->
                            <div class="h-6 flex items-center justify-center mt-1">
                                <span
                                    v-if="!isAchieved(milestone)"
                                    class="inline-block px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-500 text-[10px] font-display font-bold uppercase tracking-wider border border-gray-200"
                                >
                                    SẮP MỞ
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</template>

<script>
const DEFAULT_MILESTONES = [
    {
        id: 1,
        position: 1,
        name: 'Người làm vườn',
        min_points: 0,
        description: 'Bắt đầu gieo những hạt giống đầu tiên.',
        avatar_icon_url: '/assets/images/ranks/01_Nguoi_lam_vuon.png',
    },
    {
        id: 2,
        position: 2,
        name: 'Tiểu nông',
        min_points: 100,
        description: 'Chăm chỉ vun trồng, thu hoạch mỗi ngày.',
        avatar_icon_url: '/assets/images/ranks/02_Tieu_nong.png',
    },
    {
        id: 3,
        position: 3,
        name: 'Điền chủ',
        min_points: 200,
        description: 'Mở rộng ruộng đồng, xây dựng cơ ngơi.',
        avatar_icon_url: '/assets/images/ranks/03_Dien_chu.png',
    },
    {
        id: 4,
        position: 4,
        name: 'Thương gia',
        min_points: 500,
        description: 'Buôn bán khắp nơi, kết nối muôn phương.',
        avatar_icon_url: '/assets/images/ranks/04_Thuong_gia.png',
    },
    {
        id: 5,
        position: 5,
        name: 'Phú hộ',
        min_points: 1000,
        description: 'Sở hữu vườn tược trù phú, cuộc sống sung túc.',
        avatar_icon_url: '/assets/images/ranks/05_Phu_ho.png',
    },
    {
        id: 6,
        position: 6,
        name: 'Bá hộ',
        min_points: 2000,
        description: 'Danh tiếng lan xa, đất đai trù mật.',
        avatar_icon_url: '/assets/images/ranks/06_Ba_ho.png',
    },
    {
        id: 7,
        position: 7,
        name: 'Quan lớn',
        min_points: 5000,
        description: 'Nắm quyền cai quản, phù trợ muôn dân.',
        avatar_icon_url: '/assets/images/ranks/07_Quan_lon.png',
    },
    {
        id: 8,
        position: 8,
        name: 'Vua mùa vàng',
        min_points: 10000,
        description: 'Biểu tượng thịnh vượng tối thượng.',
        avatar_icon_url: '/assets/images/ranks/08_Vua_mua_vang.png',
    },
]

export default {
    name: 'FruitJourneyBanner',
    props: {
        rankMilestones: {
            type: Array,
            default: () => [],
        },
        userRank: {
            type: Object,
            default: () => null,
        },
    },
    computed: {
        allMilestones() {
            if (this.rankMilestones && this.rankMilestones.length > 0) {
                return [...this.rankMilestones].sort((a, b) => {
                    const posA = a.position !== undefined ? a.position : 0
                    const posB = b.position !== undefined ? b.position : 0
                    if (posA !== posB) return posA - posB
                    return (a.min_points || 0) - (b.min_points || 0)
                })
            }
            return DEFAULT_MILESTONES
        },
        row1() {
            return this.allMilestones.slice(0, 4)
        },
        row2() {
            return this.allMilestones.slice(4, 8)
        },
        currentUser() {
            return this.$page?.props?.auth?.user || {}
        },
        userName() {
            return this.currentUser.name || 'Thành viên Funky'
        },
        userAvatarUrl() {
            return this.currentUser.avatar?.url || ''
        },
        userPoints() {
            return this.currentUser.points ?? 0
        },
        rewardPoints() {
            return this.currentUser.reward_points ?? 0
        },
        topupPoints() {
            return this.currentUser.topup_points ?? 0
        },
        lifetimePoints() {
            return this.userRank?.lifetime_points ?? (this.currentUser.lifetime_points ?? this.userPoints)
        },
        currentRank() {
            return this.userRank || this.currentUser.rank || this.row1[0] || {}
        },
        currentRankName() {
            return this.currentRank?.name || 'Người làm vườn'
        },
        currentRankIcon() {
            return this.currentRank?.avatar_icon_url || '/assets/images/ranks/01_Nguoi_lam_vuon.png'
        },
        nextRank() {
            return this.currentRank?.next_rank || null
        },
        pointsToNext() {
            return this.currentRank?.points_to_next ?? 0
        },
        progressPercent() {
            return this.currentRank?.progress_percent ?? 0
        },
    },
    methods: {
        formatNumber(val) {
            return new Intl.NumberFormat('vi-VN').format(val || 0)
        },
        formatOrder(num) {
            return String(num).padStart(2, '0')
        },
        formatPointsLabel(milestone, index) {
            if (milestone.min_points === 0) {
                return 'DƯỚI 100 QUẢ'
            }
            return `${this.formatNumber(milestone.min_points)} QUẢ`
        },
        isAchieved(milestone) {
            return this.userPoints >= (milestone.min_points || 0)
        },
        isCurrent(milestone) {
            if (this.currentRank?.id && milestone.id) {
                return this.currentRank.id === milestone.id
            }
            if (this.currentRank?.name && milestone.name) {
                return this.currentRank.name.trim().toLowerCase() === milestone.name.trim().toLowerCase()
            }
            // Fallback based on userPoints
            const pts = this.userPoints
            const list = this.allMilestones
            const idx = list.findIndex((m) => (m.id && m.id === milestone.id) || m.name === milestone.name)
            if (idx === -1) return false
            const currentMin = milestone.min_points || 0
            const nextMin = list[idx + 1]?.min_points
            if (nextMin !== undefined) {
                return pts >= currentMin && pts < nextMin
            }
            return pts >= currentMin
        },
    },
}
</script>

<style lang="scss" scoped>
.progress-fill {
    background: linear-gradient(90deg, #D34000 0%, #F97316 55%, #FBBF24 100%) !important;
    box-shadow: 0 0 10px rgba(249, 115, 22, 0.5);
}

.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(234, 88, 12, 0.3) transparent;

    &::-webkit-scrollbar {
        height: 6px;
    }
    &::-webkit-scrollbar-track {
        background: transparent;
    }
    &::-webkit-scrollbar-thumb {
        background: rgba(234, 88, 12, 0.25);
        border-radius: 9999px;

        &:hover {
            background: rgba(234, 88, 12, 0.45);
        }
    }
}
</style>
