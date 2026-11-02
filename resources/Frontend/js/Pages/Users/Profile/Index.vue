<template>
    <div class="bg-[#fdfcfb] min-h-screen py-10 lg:py-20 lg:pt-[180px] pt-[120px] relative overflow-hidden">
        <!-- Abstract background elements -->
        <div
            class="absolute top-0 right-0 w-[500px] h-[500px] bg-red-fks/5 rounded-full blur-[120px] -z-10 translate-x-1/2 -translate-y-1/2">
        </div>
        <div
            class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-fks/5 rounded-full blur-[120px] -z-10 -translate-x-1/2 translate-y-1/2">
        </div>

        <div class="container relative z-10">
            <!-- Page Title -->
            <div class="mb-10 text-center lg:text-left" data-aos="fade-down">
                <span
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-fks/10 text-red-fks font-bold label-2 mb-4">
                    <i class="pi pi-shield text-xs"></i>
                    Trung tâm người dùng
                </span>
                <h1 class="headline-1 font-display font-black text-gray-warm-900 leading-tight">
                    Hồ sơ <span class="text-red-fks">cá nhân</span>
                </h1>
                <p class="body-1 text-gray-warm-500 mt-2 font-beau max-w-xl">Nơi quản lý dữ liệu, bảo mật và quyền lợi
                    tích lũy của bạn tại Funky.</p>
            </div>

            <div class="grid grid-cols-12 gap-8">
                <!-- Sidebar -->
                <div class="col-span-full lg:col-span-4 space-y-6" data-aos="fade-right">
                    <!-- User Summary Card -->
                    <div
                        class="bg-white rounded-[2rem] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.02)] border border-gray-100 relative overflow-hidden group">
                        <!-- Subtle pattern (Họa tiết) -->
                        <div class="absolute inset-0 z-0 opacity-[0.05]"
                            style="background-image: miter-limit(1); background-image: radial-gradient(circle, #000 0.5px, transparent 0.5px); background-size: 10px 10px;">
                        </div>
                        <div
                            class="absolute -top-24 -right-24 w-48 h-48 bg-red-fks/5 rounded-full blur-3xl group-hover:bg-red-fks/10 transition-colors">
                        </div>

                        <div class="relative z-10 flex flex-col items-center text-center">
                            <div class="relative mb-6 group/avatar cursor-pointer" @click="triggerAvatarUpload">
                                <div
                                    class="w-28 h-28 rounded-3xl bg-gradient-to-tr from-red-fks to-orange-400 rotate-3 absolute inset-0 -z-10 opacity-20 blur-xl group-hover:rotate-6 transition-transform">
                                </div>
                                <div
                                    class="w-28 h-28 rounded-[2rem] bg-gray-50 border-4 border-white shadow-lg flex items-center justify-center relative overflow-hidden">
                                    <img v-if="avatarUrl" :src="avatarUrl" class="w-full h-full object-cover" alt="Avatar" />
                                    <i v-else class="pi pi-user text-5xl text-gray-200"></i>
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover/avatar:opacity-100 transition-opacity">
                                        <i class="pi pi-camera text-white text-2xl"></i>
                                    </div>
                                    <div v-if="avatarUploading" class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                        <i class="pi pi-spin pi-spinner text-white text-2xl"></i>
                                    </div>
                                </div>
                                <input type="file" ref="avatarInput" @change="onAvatarChange" accept="image/*" class="hidden" />
                                <div
                                    class="absolute -bottom-1 -right-1 w-8 h-8 bg-green-fks rounded-xl border-4 border-white flex items-center justify-center shadow-sm">
                                    <i class="pi pi-star-fill text-blue-fks text-[10px]"></i>
                                </div>
                            </div>

                            <h3 class="title-1 font-display font-black text-gray-warm-900 mb-1">{{ form.name }}</h3>
                            <p class="body-2 text-gray-warm-400 font-beau mb-6">{{ form.email }}</p>

                            <div class="w-full h-px bg-gradient-to-r from-transparent via-gray-100 to-transparent mb-6">
                            </div>

                            <div class="grid grid-cols-2 gap-3 sm:gap-4 w-full text-left items-stretch">
                                <!-- Card Xếp hạng -->
                                <div class="bg-gray-50/60 backdrop-blur-sm p-3.5 rounded-2xl border border-gray-100/90 flex flex-col justify-between hover:border-orange-200/70 transition-colors shadow-2xs">
                                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-1.5 font-beau">Xếp hạng</p>
                                    <div class="flex items-center gap-2.5 min-h-[40px]">
                                        <img
                                            v-if="currentRankIcon"
                                            :src="currentRankIcon"
                                            :alt="currentRankName"
                                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl object-cover bg-white p-0.5 border-2 border-orange-200/80 shadow-xs shrink-0"
                                            @error="(e) => e.target.style.display = 'none'"
                                        />
                                        <div v-else class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-orange-100 text-orange-600 border-2 border-orange-200/80 flex items-center justify-center shrink-0 shadow-xs">
                                            <i class="pi pi-crown text-base sm:text-lg"></i>
                                        </div>
                                        <p class="font-display font-black text-gray-warm-900 text-sm sm:text-base leading-tight truncate" :title="currentRankName">
                                            {{ currentRankName }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Card Ưu đãi -->
                                <div
                                    class="p-3.5 rounded-2xl border transition-all flex flex-col justify-between shadow-2xs"
                                    :class="$page.props.auth?.user?.is_reward_locked ? 'bg-red-50/70 border-red-200/80' : 'bg-gray-50/60 backdrop-blur-sm border-gray-100/90'"
                                >
                                    <p
                                        class="text-[10px] uppercase font-bold tracking-wider mb-1.5 font-beau"
                                        :class="$page.props.auth?.user?.is_reward_locked ? 'text-red-500 font-extrabold' : 'text-gray-400'"
                                    >
                                        {{ $page.props.auth?.user?.is_reward_locked ? 'Bị khóa thưởng' : 'Ưu đãi' }}
                                    </p>
                                    <div class="flex items-center min-h-[40px]">
                                        <p
                                            class="font-display font-black text-sm sm:text-base leading-tight truncate"
                                            :class="$page.props.auth?.user?.is_reward_locked ? 'text-red-600' : 'text-gray-warm-900'"
                                        >
                                            <template v-if="$page.props.auth?.user?.is_reward_locked">
                                                Còn {{ $page.props.auth?.user?.reward_locked_days_left || 1 }} ngày
                                            </template>
                                            <template v-else>
                                                Vô hạn
                                            </template>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Points Card - High Contrast & Prominent -->
                    <div
                        class="bg-white/80 backdrop-blur-xl rounded-[2rem] p-8 text-gray-warm-900 shadow-[0_8px_30px_rgb(0,0,0,0.02)] relative overflow-hidden group border border-gray-100">
                        <!-- Abstract patterns/glows (Họa tiết) -->
                        <div
                            class="absolute inset-0 bg-[radial-gradient(circle_at_30%_30%,rgba(211,64,0,0.03)_0%,transparent_50%)]">
                        </div>
                        <div
                            class="absolute inset-0 bg-[radial-gradient(circle_at_70%_70%,rgba(251,191,36,0.03)_0%,transparent_50%)]">
                        </div>
                        <div
                            class="absolute -top-20 -right-20 w-64 h-64 bg-red-fks/10 rounded-full blur-[80px] group-hover:bg-red-fks/15 transition-all duration-700">
                        </div>
                        <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-orange-500/5 rounded-full blur-[80px]">
                        </div>

                        <!-- Grid pattern for 'họa tiết' detail -->
                        <div class="absolute inset-0 z-0 opacity-[0.015]"
                            style="background-image: radial-gradient(circle, #000 1.5px, transparent 1.5px); background-size: 20px 20px;">
                        </div>

                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-10">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center border border-gray-100 group-hover:scale-110 transition-transform overflow-hidden">
                                    <img src="/assets/images/qua_nap.png" class="w-8 h-8 object-contain" alt="Quả" />
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-warm-400 mb-0.5">
                                        SỐ QUẢ ĐANG TÍCH LŨY</p>
                                    <h4 class="label-1 font-display font-black text-gray-warm-900 tracking-wide">Funky Rewards
                                    </h4>
                                </div>
                            </div>

                            <div class="flex items-baseline gap-4 mb-4">
                                <span
                                    class="text-7xl font-display font-black leading-none text-red-fks drop-shadow-[0_4px_12px_rgba(211,64,0,0.15)] group-hover:scale-105 transition-transform duration-500 origin-left">
                                    {{ userPoints || 0 }}
                                </span>
                                <div class="flex flex-col">
                                    <span
                                        class="label-1 font-bold text-red-fks uppercase tracking-widest flex items-center gap-1">
                                        QUẢ 
                                        <img src="/assets/images/qua_nap.png" class="w-5 h-5 object-contain inline-block" alt="Quả" />
                                    </span>
                                    <span class="text-[10px] text-gray-warm-400 font-beau font-medium mt-1 italic">
                                        Tổng Quả = Quả thưởng + Quả nạp
                                    </span>
                                </div>
                            </div>

                            <!-- Breakdown: Quả thưởng & Quả nạp -->
                            <div class="grid grid-cols-2 gap-3 mb-6">
                                <div class="bg-amber-500/5 border border-amber-500/15 p-3 rounded-2xl">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <img src="/assets/images/qua_thuong.png" class="w-4 h-4 object-contain" alt="Quả thưởng" />
                                        <span class="text-[10px] uppercase font-bold text-amber-700 tracking-wider">Quả thưởng</span>
                                    </div>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-lg font-black text-amber-800 font-display">{{ rewardPoints }}</span>
                                        <span class="text-xs text-amber-600 font-bold">/ {{ maxRewardPoints }}</span>
                                    </div>
                                    <p class="text-[9px] text-amber-600/80 font-medium mt-0.5">Tối đa {{ maxRewardPoints }} Quả</p>
                                </div>
                                <div class="bg-green-600/5 border border-green-600/15 p-3 rounded-2xl">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <img src="/assets/images/qua_nap.png" class="w-4 h-4 object-contain" alt="Quả nạp" />
                                        <span class="text-[10px] uppercase font-bold text-green-700 tracking-wider">Quả nạp</span>
                                    </div>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-lg font-black text-green-700 font-display">{{ topupPoints }}</span>
                                    </div>
                                    <p class="text-[9px] text-green-600/80 font-medium mt-0.5">Không giới hạn</p>
                                </div>
                            </div>

                            <!-- Button nạp thêm Quả -->
                            <div class="mb-6">
                                <Link
                                    :href="route('users.points.index')"
                                    class="nap-qua-btn"
                                >
                                    <img
                                        src="/assets/images/qua_nap.png"
                                        class="nap-qua-icon"
                                        alt="Quả nạp"
                                    />
                                    <span class="nap-qua-text">
                                        Nạp Thêm Quả
                                    </span>
                                </Link>
                            </div>

                            <div class="border-t border-gray-100 pt-6 space-y-4">
                                <p
                                    class="body-2 text-gray-warm-500 leading-relaxed font-beau group-hover:text-gray-warm-700 transition-colors">
                                    Mỗi ngày đăng nhập: <span class="text-success-600 font-bold">+{{ dailyLoginPoints }} Quả thưởng</span> (tối đa {{ maxRewardPoints }} Quả).
                                    Khi tải tài nguyên, hệ thống ưu tiên <span class="text-red-fks font-bold">trừ Quả thưởng trước</span>, sau đó mới trừ Quả nạp.
                                </p>
                                <div class="p-4 bg-red-fks/5 border border-red-fks/10 rounded-2xl flex items-start gap-2.5">
                                    <i class="pi pi-gift text-red-fks mt-0.5 text-sm shrink-0"></i>
                                    <p class="text-xs font-semibold text-gray-warm-700 leading-relaxed font-beau">
                                        Cập nhật đầy đủ thông tin cá nhân và thông tin học vấn <span class="text-red-fks font-bold">+{{ profileCompletionPoints }} Quả thưởng</span>
                                    </p>
                                </div>
                                <div v-if="adminNote" class="p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-2.5 shadow-2xs">
                                    <span class="text-sm mt-0.5 shrink-0">📌</span>
                                    <div class="space-y-0.5">
                                        <p class="text-[11px] font-bold uppercase tracking-wider text-amber-800">Ghi chú từ Ban Quản trị</p>
                                        <p class="text-xs font-medium text-amber-900 leading-relaxed font-beau whitespace-pre-line">
                                            {{ adminNote }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Navigation glassmorphism Sidebar -->
                    <div
                        class="bg-white/80 backdrop-blur-xl rounded-[2rem] p-2 shadow-[0_8px_30px_rgb(0,0,0,0.02)] border border-gray-100">
                        <nav class="space-y-1">
                            <button @click="scrollTo('personal')"
                                class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl text-left hover:bg-red-fks/5 transition-all group relative overflow-hidden outline-none">
                                <i class="pi pi-user text-gray-400 group-hover:text-red-fks transition-colors"></i>
                                <span
                                    class="label-2 font-bold text-gray-warm-600 group-hover:text-gray-warm-900 transition-colors font-beau">Thông
                                    tin cá nhân</span>
                                <div
                                    class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-0 bg-red-fks group-hover:h-8 transition-all rounded-r-full">
                                </div>
                            </button>
                            <button @click="scrollTo('security')"
                                class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl text-left hover:bg-blue-fks/5 transition-all group relative overflow-hidden outline-none">
                                <i class="pi pi-lock text-gray-400 group-hover:text-blue-fks transition-colors"></i>
                                <span
                                    class="label-2 font-bold text-gray-warm-600 group-hover:text-gray-warm-900 transition-colors font-beau">Bảo
                                    mật tài khoản</span>
                                <div
                                    class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-0 bg-blue-fks group-hover:h-8 transition-all rounded-r-full">
                                </div>
                            </button>
                            <!-- <button @click="scrollTo('extra')"
                                class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl text-left hover:bg-purple-50 transition-all group relative overflow-hidden outline-none">
                                <i
                                    class="pi pi-map-marker text-gray-400 group-hover:text-purple-600 transition-colors"></i>
                                <span
                                    class="label-2 font-bold text-gray-warm-600 group-hover:text-gray-warm-900 transition-colors font-beau">Liên
                                    hệ & Địa chỉ</span>
                                <div
                                    class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-0 bg-purple-600 group-hover:h-8 transition-all rounded-r-full">
                                </div>
                            </button> -->
                            <div class="mx-4 my-2 h-px bg-gray-100"></div>
                            <button @click="logout" :disabled="logoutProcessing"
                                class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl text-left text-red-600 hover:bg-red-50/50 transition-all group outline-none">
                                <i class="pi pi-sign-out group-hover:translate-x-1 transition-transform"></i>
                                <span class="label-2 font-black uppercase tracking-widest">Đăng xuất</span>
                            </button>
                        </nav>
                    </div>
                </div>

                <div class="col-span-full lg:col-span-8 space-y-8" data-aos="fade-left">
                    <!-- Banner Hành trình tích lũy Quả (Phương án B) -->
                    <FruitJourneyBanner
                        :rank-milestones="rankMilestones"
                        :user-rank="userRank"
                    />

                    <!-- Notification tùy chỉnh khi lưu thành công -->
                    <transition enter-active-class="transition-all duration-500 ease-out"
                        enter-from-class="opacity-0 -translate-y-4" enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition-all duration-300 ease-in"
                        leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-4">
                        <div v-if="showSuccess"
                            class="flex items-center gap-4 p-5 rounded-2xl border transition-all"
                            :class="avatarSuccess ? 'bg-red-50 text-red-600 border border-red-200 shadow-red-500/5' : 'bg-green-50 text-green-600 border border-green-200 shadow-green-500/5'">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                :class="avatarSuccess ? 'bg-red-100' : 'bg-green-100'">
                                <i class="pi text-lg" :class="avatarSuccess ? 'pi-check text-red-600' : 'pi-check text-green-600'"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-black text-sm" :class="avatarSuccess ? 'text-red-700' : 'text-green-700'">
                                    {{ avatarSuccess ? 'Cập nhật ảnh đại diện thành công! 🎉' : 'Cập nhật thành công! 🎉' }}
                                </p>
                                <p class="text-xs mt-0.5" :class="avatarSuccess ? 'text-red-500' : 'text-green-500'">
                                    {{ avatarSuccess ? 'Hình ảnh đại diện của bạn đã được thay đổi.' : 'Thông tin hồ sơ của bạn đã được lưu lại.' }}
                                </p>
                            </div>
                            <button @click="showSuccess = false"
                                class="w-7 h-7 rounded-full flex items-center justify-center transition-all"
                                :class="avatarSuccess ? 'bg-red-100 hover:bg-red-200 text-red-600' : 'bg-green-100 hover:bg-green-200 text-green-600'">
                                <i class="pi pi-times text-xs"></i>
                            </button>
                        </div>
                    </transition>

                    <!-- Error notification -->
                    <transition enter-active-class="transition-all duration-500 ease-out"
                        enter-from-class="opacity-0 -translate-y-4" enter-to-class="opacity-100 translate-y-0">
                        <div v-if="showError"
                            class="flex items-center gap-4 p-5 rounded-2xl bg-red-50 text-red-600 border border-red-200 shadow-lg shadow-red-500/5">
                            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                                <i class="pi pi-times text-red-600 text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-black text-sm text-red-700">Có lỗi xảy ra</p>
                                <p class="text-red-600 text-xs mt-0.5 font-bold">{{ errorMessage }}</p>
                            </div>
                            <button @click="showError = false"
                                class="w-7 h-7 rounded-full bg-red-100 hover:bg-red-200 flex items-center justify-center transition-all">
                                <i class="pi pi-times text-xs text-red-600"></i>
                            </button>
                        </div>
                    </transition>

                    <!-- Admin Note notification banner (Rõ ràng, nổi bật trên Profile học viên) -->
                    <div v-if="adminNote"
                        class="flex items-start gap-4 p-5 rounded-2xl bg-amber-50/90 text-amber-900 border border-amber-300 shadow-sm">
                        <div class="w-10 h-10 rounded-xl bg-amber-200/80 text-amber-800 flex items-center justify-center shrink-0 text-xl shadow-2xs">
                            📢
                        </div>
                        <div class="flex-1 space-y-1">
                            <p class="font-bold text-xs uppercase tracking-wider text-amber-800">Thông báo từ Ban Quản trị</p>
                            <p class="text-sm font-medium text-amber-950 whitespace-pre-line leading-relaxed font-beau">{{ adminNote }}</p>
                        </div>
                    </div>

                    <form @submit.prevent="submitProfile" class="space-y-8">
                        <!-- Section: Personal Info -->
                        <div id="personal"
                            class="bg-white rounded-[2.5rem] p-8 lg:p-12 shadow-[0_10px_40px_rgb(0,0,0,0.03)] border border-gray-100/50 relative overflow-hidden hover:shadow-[0_15px_50px_rgb(0,0,0,0.05)] transition-all">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-red-fks/5 rounded-bl-[100px] -z-10"></div>

                            <div class="flex items-center gap-4 mb-10">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-red-fks/10 text-red-fks flex items-center justify-center shadow-inner">
                                    <i class="pi pi-user text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="title-1 font-bold text-gray-warm-900 font-display">Thông tin cá nhân</h2>
                                    <p class="body-2 text-gray-warm-400 font-beau">Tên hiển thị và cách FUNKY liên hệ
                                        với bạn</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
                                <div class="space-y-3">
                                    <label
                                        class="label-2 font-black uppercase tracking-widest text-gray-warm-400 flex items-center gap-2 font-beau">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-fks"></span>
                                        Họ và tên
                                    </label>
                                    <div class="relative group">
                                        <input v-model="form.name" type="text" placeholder="Họ và tên của bạn..."
                                            class="w-full px-6 py-4 rounded-2xl border-gray-100 border bg-gray-50 focus:bg-white focus:ring-4 focus:ring-red-fks/5 focus:border-red-fks transition-all outline-none font-bold text-gray-warm-800 font-beau"
                                            :class="errors.name ? 'border-red-600 ring-red-600/10' : ''" />
                                    </div>
                                    <p v-if="errors.name" class="text-xs text-red-600 font-bold ml-2">{{ errors.name }}
                                    </p>
                                </div>

                                <div class="space-y-3 opacity-80">
                                    <label
                                        class="label-2 font-black uppercase tracking-widest text-gray-warm-400 flex items-center gap-2 font-beau">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                        Email tài khoản
                                    </label>
                                    <div class="relative">
                                        <input v-model="form.email" type="email"
                                            class="w-full px-6 py-4 rounded-2xl border-gray-100 border bg-gray-100 text-gray-400 transition-all outline-none cursor-not-allowed font-beau italic"
                                            readonly disabled />
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <label
                                        class="label-2 font-black uppercase tracking-widest text-gray-warm-400 flex items-center gap-2 font-beau">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-fks"></span>
                                        Số điện thoại (Zalo) <span class="text-red-600 text-xs font-bold font-sans">*</span>
                                    </label>
                                    <div class="relative group">
                                        <input v-model="form.phone" type="text" placeholder="Ví dụ: 0938039170..."
                                            class="w-full px-6 py-4 rounded-2xl border-gray-100 border bg-gray-50 focus:bg-white focus:ring-4 focus:ring-red-fks/5 focus:border-red-fks transition-all outline-none font-bold text-gray-warm-800 font-beau"
                                            :class="errors.phone ? 'border-red-600 ring-red-600/10' : ''" />
                                    </div>
                                    <p v-if="errors.phone" class="text-xs text-red-600 font-bold ml-2">{{ errors.phone
                                    }}</p>
                                </div>

                                <div class="space-y-3">
                                    <label
                                        class="label-2 font-black uppercase tracking-widest text-gray-warm-400 flex items-center gap-2 font-beau">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-fks"></span>
                                        Link Facebook <span class="text-red-600 text-xs font-bold font-sans">*</span>
                                    </label>
                                    <div class="relative group">
                                        <input v-model="form.zalo" type="text" placeholder="https://www.facebook.com/your.profile"
                                            class="w-full px-6 py-4 rounded-2xl border-gray-100 border bg-gray-50 focus:bg-white focus:ring-4 focus:ring-red-fks/5 focus:border-red-fks transition-all outline-none font-bold text-gray-warm-800 font-beau"
                                            :class="errors.zalo ? 'border-red-600 ring-red-600/10' : ''" />
                                    </div>
                                    <p v-if="errors.zalo" class="text-xs text-red-600 font-bold ml-2">{{ errors.zalo
                                    }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Security -->
                        <div id="security"
                            class="bg-white rounded-[2.5rem] p-8 lg:p-12 shadow-[0_10px_40px_rgb(0,0,0,0.03)] border border-gray-100/50 relative overflow-hidden hover:shadow-[0_15px_50px_rgb(0,0,0,0.05)] transition-all">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-fks/5 rounded-bl-[100px] -z-10"></div>

                            <div class="flex items-center gap-4 mb-10">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-blue-fks/10 text-blue-fks flex items-center justify-center shadow-inner">
                                    <i class="pi pi-lock text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="title-1 font-bold text-gray-warm-900 font-display">Bảo mật</h2>
                                    <p class="body-2 text-gray-warm-400 font-beau">Chỉ điền nếu bạn muốn thay đổi mật
                                        khẩu mới</p>
                                </div>
                            </div>

                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8 rounded-3xl bg-blue-fks/[0.02] border border-blue-fks/10">
                                <div class="space-y-3">
                                    <label class="label-2 font-bold text-blue-fks font-beau">Mật khẩu mới</label>
                                    <input v-model="form.password" type="password" placeholder="Tối thiểu 6 ký tự..."
                                        class="w-full px-6 py-4 rounded-2xl border-gray-200/50 border bg-white focus:ring-4 focus:ring-blue-fks/5 focus:border-blue-fks transition-all outline-none font-bold font-beau" />
                                    <p v-if="errors.password" class="text-xs text-red-600 font-bold font-beau">{{
                                        errors.password }}</p>
                                </div>

                                <div class="space-y-3">
                                    <label class="label-2 font-bold text-blue-fks font-beau">Xác nhận lại</label>
                                    <input v-model="form.password_confirmation" type="password"
                                        placeholder="Nhập lại mật khẩu..."
                                        class="w-full px-6 py-4 rounded-2xl border-gray-200/50 border bg-white focus:ring-4 focus:ring-blue-fks/5 focus:border-blue-fks transition-all outline-none font-bold font-beau" />
                                </div>
                            </div>
                        </div>

                        <!-- Section: Trường học & Chuyên ngành -->
                        <div id="school"
                            class="bg-white rounded-[2.5rem] p-8 lg:p-12 shadow-[0_10px_40px_rgb(0,0,0,0.03)] border border-gray-100/50 relative overflow-hidden hover:shadow-[0_15px_50px_rgb(0,0,0,0.05)] transition-all">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-400/5 rounded-bl-[100px] -z-10">
                            </div>

                            <div class="flex items-center gap-4 mb-10">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-yellow-400/10 text-yellow-600 flex items-center justify-center shadow-inner">
                                    <i class="pi pi-graduation-cap text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="title-1 font-bold text-gray-warm-900 font-display">Thông tin học vấn
                                    </h2>
                                    <p class="body-2 text-gray-warm-400 font-beau">Giúp Funky gợi ý tài liệu phù hợp
                                        cho bạn</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Trường học -->
                                <div class="md:col-span-2 space-y-3">
                                    <label
                                        class="label-2 font-black uppercase tracking-widest text-gray-warm-400 flex items-center gap-2 font-beau">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                        Trường đang / đã học <span class="text-red-600 text-xs font-bold font-sans">*</span>
                                    </label>
                                    <select v-model="form.school_id"
                                        class="w-full px-6 py-4 rounded-2xl border-gray-100 border bg-gray-50 focus:bg-white focus:ring-4 focus:ring-yellow-400/10 focus:border-yellow-400 transition-all outline-none font-bold text-gray-warm-800 font-beau appearance-none"
                                        :class="(errors.school_id || errors.school) ? 'border-red-600 ring-red-600/10' : ''">
                                        <option :value="null">-- Chọn trường --</option>
                                        <option v-for="school in schools" :key="school.id" :value="school.id">
                                            {{ school.label }}
                                        </option>
                                    </select>
                                    <!-- Trường không có trong danh sách -->
                                    <div v-if="form.school_id === null" class="mt-3">
                                        <input v-model="form.school" type="text"
                                            placeholder="Hoặc nhập tên trường thủ công..."
                                            class="w-full px-6 py-4 rounded-2xl border-gray-100 border bg-gray-50 focus:bg-white focus:ring-4 focus:ring-yellow-400/10 focus:border-yellow-400 transition-all outline-none font-bold text-gray-warm-800 font-beau"
                                            :class="errors.school ? 'border-red-600 ring-red-600/10' : ''" />
                                    </div>
                                    <p v-if="errors.school_id" class="text-xs text-red-600 font-bold ml-2">{{ errors.school_id }}</p>
                                    <p v-if="errors.school" class="text-xs text-red-600 font-bold ml-2">{{ errors.school }}</p>
                                </div>

                                <!-- Chuyên ngành -->
                                <div class="space-y-3">
                                    <label
                                        class="label-2 font-black uppercase tracking-widest text-gray-warm-400 flex items-center gap-2 font-beau">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                        Chuyên ngành <span class="text-red-600 text-xs font-bold font-sans">*</span>
                                    </label>
                                    <select v-model="form.major"
                                        class="w-full px-6 py-4 rounded-2xl border-gray-100 border bg-gray-50 focus:bg-white focus:ring-4 focus:ring-yellow-400/10 focus:border-yellow-400 transition-all outline-none font-bold text-gray-warm-800 font-beau appearance-none"
                                        :class="errors.major ? 'border-red-600 ring-red-600/10' : ''">
                                        <option :value="null">-- Chọn chuyên ngành --</option>
                                        <option v-for="m in majors" :key="m.value" :value="m.value">
                                            {{ m.label }}
                                        </option>
                                    </select>
                                    <p v-if="errors.major" class="text-xs text-red-600 font-bold ml-2">{{ errors.major }}</p>
                                </div>

                                <!-- Trạng thái hiện tại -->
                                <div class="space-y-3">
                                    <label
                                        class="label-2 font-black uppercase tracking-widest text-gray-warm-400 flex items-center gap-2 font-beau">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                        Trạng thái hiện tại <span class="text-red-600 text-xs font-bold font-sans">*</span>
                                    </label>
                                    <select v-model="form.current_status"
                                        class="w-full px-6 py-4 rounded-2xl border-gray-100 border bg-gray-50 focus:bg-white focus:ring-4 focus:ring-yellow-400/10 focus:border-yellow-400 transition-all outline-none font-bold text-gray-warm-800 font-beau appearance-none"
                                        :class="errors.current_status ? 'border-red-600 ring-red-600/10' : ''">
                                        <option :value="null">-- Chọn trạng thái --</option>
                                        <option v-for="s in currentStatusOptions" :key="s.value" :value="s.value">
                                            {{ s.label }}
                                        </option>
                                    </select>
                                    <p v-if="errors.current_status" class="text-xs text-red-600 font-bold ml-2">{{ errors.current_status }}</p>
                                </div>

                                <!-- Năm học -->
                                <div class="space-y-3">
                                    <label
                                        class="label-2 font-black uppercase tracking-widest text-gray-warm-400 flex items-center gap-2 font-beau">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                        Năm học / Khóa học <span class="text-red-600 text-xs font-bold font-sans">*</span>
                                    </label>
                                    <select v-model="form.school_year"
                                        class="w-full px-6 py-4 rounded-2xl border-gray-100 border bg-gray-50 focus:bg-white focus:ring-4 focus:ring-yellow-400/10 focus:border-yellow-400 transition-all outline-none font-bold text-gray-warm-800 font-beau appearance-none"
                                        :class="errors.school_year ? 'border-red-600 ring-red-600/10' : ''">
                                        <option :value="''">-- Chọn năm học / khóa --</option>
                                        <option v-for="sy in schoolYears" :key="sy.value" :value="sy.value">
                                            {{ sy.label }}
                                        </option>
                                    </select>
                                    <p v-if="errors.school_year" class="text-xs text-red-600 font-bold ml-2">{{ errors.school_year }}</p>
                                </div>

                                <!-- Ghi chú cho Funky -->
                                <div class="md:col-span-2 space-y-3">
                                    <label
                                        class="label-2 font-black uppercase tracking-widest text-gray-warm-400 font-beau">Ghi
                                        chú cho Funky</label>
                                    <textarea v-model="form.funky_note" rows="3"
                                        placeholder="Ghi chú thêm về chuyên ngành, mục tiêu học tập..."
                                        class="w-full px-6 py-4 rounded-2xl border-gray-100 border bg-gray-50 focus:bg-white focus:ring-4 focus:ring-yellow-400/10 focus:border-yellow-400 transition-all outline-none resize-none font-bold text-gray-warm-800 font-beau"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="flex flex-col sm:flex-row justify-end items-center gap-6 pt-6 pb-20 lg:pb-0">
                            <p class="body-2 text-gray-warm-400 font-beau text-center sm:text-right italic">
                                Hãy kiểm tra kỹ thông tin trước khi lưu
                            </p>
                            <button type="submit"
                                class="w-full sm:w-80 h-16 bg-red-fks text-white rounded-3xl title-2 font-black font-display hover:shadow-2xl hover:shadow-red-fks/40 active:scale-95 transition-all flex items-center justify-center gap-4 disabled:opacity-50 relative overflow-hidden group">
                                <div
                                    class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                </div>
                                <i class="pi pi-check-circle text-xl"></i>
                                <span class="relative z-10">CẬP NHẬT HỒ SƠ</span>
                                <i v-if="form.processing" class="pi pi-spin pi-spinner ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import axios from 'axios'
import FruitJourneyBanner from './Components/FruitJourneyBanner.vue'

export default {
    components: {
        Link,
        FruitJourneyBanner,
    },
    props: {
        // Dữ liệu trường học từ controller qua Inertia props
        schools: { type: Array, default: () => [] },
        majors: { type: Array, default: () => [] },
        currentStatusOptions: { type: Array, default: () => [] },
        schoolYears: { type: Array, default: () => [] },
        // Dữ liệu xếp hạng & hành trình quả
        rankMilestones: { type: Array, default: () => [] },
        userRank: { type: Object, default: () => null },
    },
    data() {
        const user = this.$page.props.auth?.user || {}
        return {
            form: this.$inertia.form({
                name: user.name || '',
                email: user.email || '',
                phone: user.phone || '',
                zalo: user.zalo || '',
                address: user.address || '',
                note: user.note || '',
                avatar: '',
                password: '',
                password_confirmation: '',
                // Thông tin học vấn - lấy từ user hiện tại
                school: user.school || '',
                school_id: user.school_id || null,
                major: user.major || null,
                current_status: user.current_status || null,
                school_year: user.school_year || '',
                funky_note: user.funky_note || '',
            }),
            logoutProcessing: false,
            showSuccess: false,
            avatarSuccess: false,
            showError: false,
            errorMessage: '',
            avatarUploading: false,
            avatarUrl: user.avatar?.url || '',
        }
    },
    watch: {
        '$page.props.auth.user.avatar'(newAvatar) {
            if (newAvatar) {
                this.avatarUrl = newAvatar.url || ''
            }
        },
        '$page.props.flash.success'(val) {
            if (val) {
                this.avatarSuccess = false
                this.showSuccess = true
                setTimeout(() => { this.showSuccess = false }, 4000)
            }
        },
        '$page.props.flash.error'(val) {
            if (val) {
                this.errorMessage = val
                this.showError = true
                setTimeout(() => { this.showError = false }, 5000)
            }
        },
    },
    computed: {
        errors() {
            return this.$page.props.errors || {}
        },
        userPoints() {
            return this.$page.props.auth?.user?.points ?? 0
        },
        rewardPoints() {
            return this.$page.props.auth?.user?.reward_points ?? 0
        },
        topupPoints() {
            return this.$page.props.auth?.user?.topup_points ?? 0
        },
        dailyLoginPoints() {
            return this.$page.props.auth?.daily_login_points ?? 10
        },
        profileCompletionPoints() {
            return this.$page.props.auth?.profile_completion_points ?? 10
        },
        maxRewardPoints() {
            return this.$page.props.auth?.max_reward_points ?? 100
        },
        adminNote() {
            return this.$page.props.auth?.admin_note || this.$page.props.auth?.user?.funky_note
        },
        currentRank() {
            return this.userRank || this.$page.props.auth?.user?.rank || {
                name: this.memberRank,
                avatar_icon_url: '/assets/images/ranks/01_Nguoi_lam_vuon.png',
                min_points: 0,
            }
        },
        currentRankName() {
            return this.currentRank?.name || this.memberRank
        },
        currentRankIcon() {
            return this.currentRank?.avatar_icon_url || null
        },
        memberRank() {
            const pts = this.userPoints
            if (pts < 50) return 'NGƯỜI LÀM VƯỜN'
            if (pts <= 100) return 'TÁ ĐIỀN'
            if (pts <= 300) return 'ĐIỀN CHỦ'
            if (pts <= 1000) return 'PHÚ HỘ'
            return 'BÁ HỘ'
        },
    },
    methods: {
        triggerAvatarUpload() {
            if (this.avatarUploading) return
            this.$refs.avatarInput.click()
        },
        async onAvatarChange(e) {
            const file = e.target.files[0]
            if (!file) return

            // Simple client-side size check (2MB)
            if (file.size > 2 * 1024 * 1024) {
                this.errorMessage = 'Dung lượng hình ảnh tối đa là 2MB.'
                this.showError = true
                setTimeout(() => { this.showError = false }, 5000)
                return
            }

            this.avatarUploading = true
            const formData = new FormData()
            formData.append('avatar', file)

            try {
                const response = await axios.post(this.route('users.profile.upload-avatar'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })

                if (response.data?.success) {
                    this.avatarUrl = response.data.avatar?.url || ''
                    
                    // Update layout user details in inertia page state
                    if (this.$page.props.auth?.user) {
                        this.$page.props.auth.user.avatar = response.data.avatar
                    }

                    this.avatarSuccess = true
                    this.showSuccess = true
                    setTimeout(() => { 
                        this.showSuccess = false 
                        this.avatarSuccess = false
                    }, 4000)
                } else {
                    this.errorMessage = response.data?.message || 'Tải ảnh lên thất bại.'
                    this.showError = true
                    setTimeout(() => { this.showError = false }, 5000)
                }
            } catch (err) {
                console.error(err)
                this.errorMessage = err.response?.data?.message || 'Lỗi hệ thống khi tải ảnh.'
                this.showError = true
                setTimeout(() => { this.showError = false }, 5000)
            } finally {
                this.avatarUploading = false
                // Reset file input value
                if (this.$refs.avatarInput) {
                    this.$refs.avatarInput.value = ''
                }
            }
        },
        scrollTo(id) {
            const el = document.getElementById(id)
            if (el) {
                const offset = 160
                const bodyRect = document.body.getBoundingClientRect().top
                const elementRect = el.getBoundingClientRect().top
                const elementPosition = elementRect - bodyRect
                const offsetPosition = elementPosition - offset

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                })
            }
        },
        submitProfile() {
            const data = {
                name: this.form.name,
                email: this.form.email,
                phone: this.form.phone,
                zalo: this.form.zalo || '',
                address: this.form.address || '',
                note: this.form.note || '',
                // Thông tin học vấn
                school: this.form.school_id ? '' : (this.form.school || ''),
                school_id: this.form.school_id || null,
                major: this.form.major || null,
                current_status: this.form.current_status || null,
                school_year: this.form.school_year || '',
                funky_note: this.form.funky_note || '',
            }
            if (this.form.password && this.form.password_confirmation) {
                data.password = this.form.password
                data.password_confirmation = this.form.password_confirmation
            }
            this.$inertia.post(this.route('users.profile.update'), data, {
                preserveScroll: true,
                onSuccess: () => {
                    this.form.password = ''
                    this.form.password_confirmation = ''
                    this.showSuccess = true
                    setTimeout(() => { this.showSuccess = false }, 4000)
                },
                onError: (errors) => {
                    const firstError = Object.values(errors)[0]
                    if (firstError) {
                        this.errorMessage = firstError
                        this.showError = true
                        setTimeout(() => { this.showError = false }, 5000)
                    }
                    console.error('Validation errors:', errors)
                },
            })
        },
        logout() {
            if (this.logoutProcessing) return
            this.logoutProcessing = true
            this.$inertia.post(
                this.route('users.logout'),
                {},
                {
                    onSuccess: () => {
                        this.$toast?.success('Đăng xuất thành công. Hẹn gặp lại bạn! 👋')
                        this.$inertia.visit(this.route('home'))
                    },
                    onFinish: () => {
                        this.logoutProcessing = false
                    },
                }
            )
        },
    },
}
</script>

<style lang="scss" scoped>
input::placeholder,
textarea::placeholder {
    color: #cbd5e1;
    font-size: 0.875rem;
    font-weight: 400;
}

// Custom scroll smoothing for the dashboard
html {
    scroll-behavior: smooth;
}

// Nút Nạp thêm Quả — nền xanh lá, chữ đỏ to
.nap-qua-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 100%;
    padding: 14px 20px;
    background: #87d81a;
    border-radius: 20px;
    text-decoration: none;
    box-shadow: 0 6px 20px rgba(135, 216, 26, 0.4), inset 0 1px 0 rgba(255,255,255,0.25);
    border: 3px solid #70be0e;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    position: relative;
    overflow: hidden;

    &:hover {
        transform: translateY(-3px) scale(1.01);
        background: #94e622;
        box-shadow: 0 10px 28px rgba(135, 216, 26, 0.5);
    }

    &:active {
        transform: translateY(0) scale(0.98);
        box-shadow: 0 3px 10px rgba(135, 216, 26, 0.3);
    }
}

.nap-qua-text {
    font-family: 'BT-BeauSans', 'Phudu', sans-serif;
    font-size: 22px;
    font-weight: 900;
    line-height: 1;
    color: #e31c1c;
    letter-spacing: -0.5px;
    text-shadow: 2px 2px 0 rgba(0,0,0,0.1);
    text-transform: uppercase;
}

.nap-qua-icon {
    width: 48px;
    height: 48px;
    object-fit: contain;
    filter: drop-shadow(2px 3px 4px rgba(0,0,0,0.2));
    flex-shrink: 0;
    transition: transform 0.3s ease;

    .nap-qua-btn:hover & {
        transform: scale(1.12) rotate(8deg);
    }
}
</style>
