<template>
    <Head title="Hướng dẫn sử dụng CMS - Thành Khang" />

    <div class="px-4 py-5 sm:px-6 sm:py-8 bg-[#FAFAFA] min-h-screen text-slate-800">
        <!-- Top Navigation / Breadcrumb -->
        <div class="flex flex-wrap items-center justify-between gap-3 sm:gap-4 mb-5 sm:mb-6">
            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-semibold text-slate-500 min-w-0">
                <Link
                    v-if="can('admin.dashboard.index')"
                    :href="route('admin.dashboard.index')"
                    class="hover:text-[#005EA4] transition-colors flex items-center gap-1 shrink-0"
                >
                    <i class="pi pi-home text-[11px]"></i>
                    <span>Bảng điều khiển</span>
                </Link>
                <span class="text-slate-300">/</span>
                <span class="text-slate-700">Hướng dẫn sử dụng CMS</span>
            </div>

            <div class="hidden sm:flex items-center gap-2 text-xs font-medium text-slate-500 bg-white px-3.5 py-1.5 rounded-lg border border-slate-200 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Thành Khang Admin v2.5 • Chuẩn B2B</span>
            </div>
        </div>

        <!-- Page Header -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-8 shadow-sm mb-6 sm:mb-8 relative overflow-hidden isolate">
            <div class="absolute -right-12 -top-12 w-64 h-64 bg-[#EDF6FF]/70 rounded-full blur-2xl pointer-events-none z-0"></div>

            <div class="max-w-4xl relative z-[1]">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#EDF6FF] text-[#005EA4] rounded-full text-[11px] sm:text-xs font-bold uppercase tracking-wider mb-3 max-w-full">
                    <i class="pi pi-book text-xs shrink-0"></i>
                    <span>Tài liệu nội bộ &amp; Quy chuẩn vận hành</span>
                </div>
                <h1 class="text-xl sm:text-3xl font-extrabold text-[#00284B] tracking-tight flex items-start sm:items-center gap-2.5 sm:gap-3">
                    <span class="w-2 sm:w-2.5 h-6 sm:h-7 bg-[#005EA4] rounded-full inline-block shrink-0 mt-1 sm:mt-0"></span>
                    <span>Hướng dẫn sử dụng Hệ thống Admin CMS</span>
                </h1>
                <p class="text-slate-600 text-sm sm:text-base mt-2 leading-relaxed">
                    Cẩm nang tra cứu quy trình quản trị catalog linh kiện thang cuốn, xích công nghiệp, hệ thống xử lý yêu cầu báo giá kỹ thuật (RFQ) &amp; tệp bản vẽ CAD, biên tập nội dung song ngữ VI/EN, quản trị hình ảnh và phân quyền hệ thống Thành Khang.
                </p>

                <!-- 3 Quick Info Badges -->
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2.5 sm:gap-6 mt-5 pt-5 border-t border-slate-100 text-xs font-semibold text-slate-600">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-md bg-[#EDF6FF] text-[#005EA4] flex items-center justify-center font-bold text-[11px] shrink-0">16</div>
                        <span>Module Quản trị Thực tế</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[11px] shrink-0">4</div>
                        <span>Trạng thái Xử lý Báo giá &amp; CAD</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-md bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-[11px] shrink-0">2</div>
                        <span>Ngôn ngữ Dữ liệu (VI / EN)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar & Instant Filter Section -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-sm mb-6 sm:mb-8 space-y-4">
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 md:gap-4">
                <!-- Search Input -->
                <div class="relative flex-1 min-w-0">
                    <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Tìm kiếm hướng dẫn (CAD, Báo giá, Sản phẩm, Hotline, SEO...)"
                        class="w-full pl-11 pr-11 py-2.5 sm:py-3 text-base sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-[#005EA4] focus:ring-2 focus:ring-[#005EA4]/20 transition-all text-slate-800 placeholder-slate-400 font-medium"
                    />
                    <button
                        v-if="searchQuery"
                        type="button"
                        @click="searchQuery = ''"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-slate-200 hover:bg-slate-300 text-slate-600 flex items-center justify-center transition-colors text-xs"
                        title="Xóa tìm kiếm"
                    >
                        <i class="pi pi-times text-[10px]"></i>
                    </button>
                </div>

                <!-- Result Counter -->
                <div class="text-xs font-bold text-slate-500 whitespace-nowrap px-1">
                    Hiển thị <span class="text-[#005EA4] font-extrabold">{{ filteredModules.length }}</span> / {{ allModules.length }} module
                </div>
            </div>

            <!-- 5 Filter Tabs -->
            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    type="button"
                    @click="activeTab = tab.id"
                    class="px-3 sm:px-3.5 py-2 rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5 sm:gap-2 max-w-full"
                    :class="
                        activeTab === tab.id
                            ? 'bg-[#005EA4] text-white shadow-sm ring-2 ring-[#005EA4]/20'
                            : 'bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200/80'
                    "
                >
                    <i :class="tab.icon" class="text-xs shrink-0"></i>
                    <span>{{ tab.label }}</span>
                    <span
                        class="px-1.5 py-0.5 rounded-full text-[10px] font-extrabold shrink-0"
                        :class="
                            activeTab === tab.id
                                ? 'bg-[#003967]/70 text-white'
                                : 'bg-slate-200/80 text-slate-600'
                        "
                    >
                        {{ getTabCount(tab.id) }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Quick Workflow Diagram: Quy trình 4 Trạng thái Xử lý Báo giá & Tệp CAD -->
        <div
            v-if="activeTab === 'ALL' || activeTab === 'FACTORY'"
            class="bg-gradient-to-br from-white to-[#EDF6FF]/50 rounded-2xl border border-[#C9E0FF] shadow-sm p-4 sm:p-7 mb-6 sm:mb-8"
        >
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center justify-between gap-2 sm:gap-3 mb-4 sm:mb-5">
                <div>
                    <div class="flex items-center gap-2 text-xs font-extrabold text-[#005EA4] uppercase tracking-wider">
                        <i class="pi pi-compass shrink-0"></i>
                        <span>QUY TRÌNH VẬN HÀNH CỐT LÕI</span>
                    </div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 mt-1">
                        Sơ đồ Xử lý Yêu cầu Báo giá (RFQ) &amp; Hồ sơ Kỹ thuật
                    </h2>
                </div>
                <div class="text-xs text-slate-500 italic">
                    Áp dụng cho module <span class="font-semibold text-slate-700">Báo giá &amp; Yêu cầu Kỹ thuật</span>
                </div>
            </div>

            <!-- 4 Main Steps Flow -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-5">
                <!-- Step 1: NEW -->
                <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-sm flex flex-col relative group hover:border-[#81BDFF] transition-colors min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-extrabold bg-[#EDF6FF] text-[#005EA4] border border-[#C9E0FF] uppercase tracking-wide">
                            1. NEW (Mới)
                        </span>
                        <span class="w-6 h-6 rounded-full bg-[#EDF6FF] text-[#005EA4] flex items-center justify-center font-bold text-xs shrink-0">
                            <i class="pi pi-inbox text-xs"></i>
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 mb-1">Tiếp nhận Yêu cầu &amp; Bản vẽ</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Hệ thống ghi nhận RFQ từ đối tác kèm tài liệu thông số hoặc bản vẽ (.STEP, .DWG, .PDF, .ZIP). Kiểm tra mã linh kiện, số lượng và tiến độ dự án.
                    </p>
                </div>

                <!-- Step 2: RESPONSE -->
                <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-sm flex flex-col relative group hover:border-amber-300 transition-colors min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200 uppercase tracking-wide">
                            2. RESPONSE (Đã phản hồi)
                        </span>
                        <span class="w-6 h-6 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs shrink-0">
                            <i class="pi pi-comments text-xs"></i>
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 mb-1">Đối chiếu Thông số &amp; Báo giá</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Kỹ sư đối chiếu bản vẽ kỹ thuật, bước xích, tải trọng, công suất quạt hoặc model thang cuốn; kiểm tra tồn kho / thời gian nhập OEM và gửi báo giá B2B.
                    </p>
                </div>

                <!-- Step 3: CONFIRMED -->
                <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-sm flex flex-col relative group hover:border-indigo-300 transition-colors min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-extrabold bg-indigo-100 text-indigo-800 border border-indigo-200 uppercase tracking-wide">
                            3. CONFIRMED (Đã xác nhận)
                        </span>
                        <span class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs shrink-0">
                            <i class="pi pi-check-circle text-xs"></i>
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 mb-1">Xác nhận Đơn hàng &amp; Điều phối</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Đối tác xác nhận báo giá / PO và vật liệu (Thép hợp kim 40Cr, Inox 304/316, POM, EPDM...). Kho hoặc đối tác OEM chuẩn bị lô hàng &amp; chứng từ CO/CQ.
                    </p>
                </div>

                <!-- Step 4: PROCESSED -->
                <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-sm flex flex-col relative group hover:border-emerald-300 transition-colors min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wide">
                            4. PROCESSED (Đã xử lý)
                        </span>
                        <span class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs shrink-0">
                            <i class="pi pi-box text-xs"></i>
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 mb-1">Kiểm định QC &amp; Bàn giao</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Kiểm tra dung sai kích thước, độ căng xích / thông số kỹ thuật, đóng gói kèm chứng chỉ CO/CQ và bàn giao lắp đặt tại công trình hoặc nhà máy.
                    </p>
                </div>
            </div>

            <!-- Auxiliary States -->
            <div class="bg-white/80 rounded-xl p-3.5 border border-slate-200 text-xs flex flex-col lg:flex-row lg:items-center justify-between gap-3 text-slate-600">
                <div class="flex items-center gap-2 shrink-0">
                    <i class="pi pi-info-circle text-[#005EA4] shrink-0"></i>
                    <span><strong>Hai trạng thái phụ kết thúc / lọc đơn:</strong></span>
                </div>
                <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-2.5 sm:gap-4">
                    <div class="flex items-start gap-2">
                        <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-bold text-[10px] shrink-0 whitespace-nowrap mt-0.5">CLOSE (Đóng)</span>
                        <span>Dùng khi khách hủy yêu cầu hoặc không phản hồi sau 3 ngày liên lạc.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 font-bold text-[10px] shrink-0 whitespace-nowrap mt-0.5">IS_SPAM (Spam)</span>
                        <span>Đánh dấu form rác/bot để loại bỏ khỏi thống kê hiệu suất KPI.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State When No Search Match -->
        <div
            v-if="!filteredModules.length"
            class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-12 text-center shadow-sm mb-6 sm:mb-8"
        >
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-4">
                <i class="pi pi-search"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">Không tìm thấy hướng dẫn phù hợp</h3>
            <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">
                Không có module hoặc từ khóa nào khớp với <span class="font-bold text-slate-700">"{{ searchQuery }}"</span> trong tab đang chọn. Vui lòng thử từ khóa khác hoặc xóa bộ lọc.
            </p>
            <button
                type="button"
                @click="resetSearch"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#005EA4] hover:bg-[#004D86] text-white font-semibold text-xs rounded-xl shadow-sm transition-all"
            >
                <i class="pi pi-refresh text-xs"></i>
                <span>Xóa bộ lọc &amp; Hiển thị tất cả</span>
            </button>
        </div>

        <!-- Module Cards Grid -->
        <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-8 sm:mb-12">
            <div
                v-for="item in filteredModules"
                :key="item.id"
                class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-7 flex flex-col hover:border-slate-300 hover:shadow-md transition-all relative overflow-hidden min-w-0"
            >
                <!-- Card Header -->
                <div class="flex items-start justify-between gap-3 mb-3.5 sm:mb-4">
                    <div class="flex items-start sm:items-center gap-3 min-w-0">
                        <div
                            class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center text-base sm:text-lg font-bold shadow-sm shrink-0"
                            :class="item.iconBg"
                        >
                            <i :class="item.icon"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-md inline-block mb-1" :class="item.categoryBadge">
                                {{ item.categoryName }}
                            </span>
                            <h2 class="text-base sm:text-lg font-bold text-slate-900 leading-snug">
                                {{ item.title }}
                            </h2>
                        </div>
                    </div>
                </div>

                <!-- Module Purpose / Description -->
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4">
                    {{ item.summary }}
                </p>

                <!-- Step-by-Step Instructions -->
                <div class="bg-slate-50/80 rounded-xl p-3.5 sm:p-4 border border-slate-100 mb-4 flex-1">
                    <div class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                        <i class="pi pi-list-check text-xs text-[#005EA4] shrink-0"></i>
                        <span>Quy trình thao tác từng bước:</span>
                    </div>
                    <ol class="space-y-2 text-xs text-slate-700">
                        <li
                            v-for="(step, sIndex) in item.steps"
                            :key="sIndex"
                            class="flex items-start gap-2.5 leading-relaxed"
                        >
                            <span class="w-4 h-4 rounded-full bg-[#EDF6FF] text-[#005EA4] font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5">
                                {{ sIndex + 1 }}
                            </span>
                            <span class="min-w-0 break-words">{{ step }}</span>
                        </li>
                    </ol>
                </div>

                <!-- Callout Note / Warning -->
                <div class="bg-amber-50/70 border border-amber-200/80 rounded-xl p-3 text-xs text-amber-900 flex items-start gap-2 mb-4 sm:mb-5">
                    <i class="pi pi-exclamation-triangle text-amber-600 text-xs shrink-0 mt-0.5"></i>
                    <div class="leading-relaxed min-w-0 break-words">
                        <strong class="font-bold">Lưu ý:</strong> {{ item.tips }}
                    </div>
                </div>

                <!-- Card Footer Action -->
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-auto">
                    <span class="text-[11px] font-medium text-slate-400 min-w-0 break-words">
                        Route: <code class="text-slate-600 font-mono text-[10px] bg-slate-100 px-1.5 py-0.5 rounded break-all">{{ item.routeName }}</code>
                    </span>

                    <Link
                        v-if="canAccess(item)"
                        :href="getRouteUrl(item)"
                        class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 sm:py-2 bg-[#005EA4] hover:bg-[#004D86] text-white font-semibold text-xs rounded-xl shadow-sm hover:shadow transition-all group whitespace-nowrap shrink-0 w-full sm:w-auto"
                    >
                        <span>Mở trang quản lý</span>
                        <i class="pi pi-arrow-right text-[10px] group-hover:translate-x-0.5 transition-transform shrink-0"></i>
                    </Link>

                    <div
                        v-else
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 sm:py-1.5 bg-slate-100 text-slate-400 font-medium text-xs rounded-lg select-none whitespace-nowrap shrink-0 w-full sm:w-auto"
                        title="Tài khoản hiện tại chưa được cấp quyền quản lý module này"
                    >
                        <i class="pi pi-lock text-[10px] shrink-0"></i>
                        <span>Yêu cầu quyền truy cập</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reference Section: Bảng Quy chuẩn Media, Tệp CAD & Checklist Đa ngôn ngữ VI/EN -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-8 mb-6 sm:mb-8 space-y-6 sm:space-y-8 min-w-0">
            <div class="border-b border-slate-100 pb-4 sm:pb-5">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-[11px] sm:text-xs font-bold uppercase tracking-wider mb-2 max-w-full">
                    <i class="pi pi-sliders-h text-xs shrink-0"></i>
                    <span>Tài liệu tham chiếu kỹ thuật</span>
                </div>
                <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                    Quy chuẩn Hình ảnh, Bản vẽ CAD &amp; Checklist Xuất bản
                </h2>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">
                    Bảng thông số kỹ thuật chuẩn giúp quản trị viên tối ưu tốc độ tải trang, hiển thị rõ bản vẽ sản phẩm và chuẩn SEO toàn diện.
                </p>
            </div>

            <!-- Table 1: Media Standards -->
            <div class="min-w-0">
                <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 uppercase tracking-wider mb-2 sm:mb-3 flex items-start sm:items-center gap-2">
                    <span class="w-2 h-4 bg-[#005EA4] rounded-full inline-block shrink-0 mt-0.5 sm:mt-0"></span>
                    <span>1. Bảng Quy chuẩn Kích thước Hình ảnh Website</span>
                </h3>
                <p class="sm:hidden text-[11px] text-slate-400 mb-2 flex items-center gap-1.5">
                    <i class="pi pi-arrows-h text-[11px]"></i>
                    <span>Vuốt ngang để xem đầy đủ bảng thông số</span>
                </p>
                <div class="overflow-x-auto rounded-xl border border-slate-200 max-w-full">
                    <table class="w-full min-w-[680px] text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                                <th class="py-3 px-4 whitespace-nowrap">Vị trí hiển thị</th>
                                <th class="py-3 px-4 whitespace-nowrap">Kích thước chuẩn</th>
                                <th class="py-3 px-4 whitespace-nowrap">Tỉ lệ</th>
                                <th class="py-3 px-4 whitespace-nowrap">Dung lượng tối đa</th>
                                <th class="py-3 px-4 whitespace-nowrap">Định dạng ưu tiên</th>
                                <th class="py-3 px-4 whitespace-nowrap">Ghi chú thiết kế</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">Hero Banner (Desktop)</td>
                                <td class="py-3 px-4 font-mono text-[#005EA4] font-bold whitespace-nowrap">1920 × 800 px</td>
                                <td class="py-3 px-4 whitespace-nowrap">2.4 : 1</td>
                                <td class="py-3 px-4 whitespace-nowrap">&lt; 350 KB</td>
                                <td class="py-3 px-4 font-bold whitespace-nowrap">WebP, PNG</td>
                                <td class="py-3 px-4 text-slate-500">Đặt nội dung/chữ chính trong vùng an toàn giữa 1200px</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">Hero Banner (Mobile)</td>
                                <td class="py-3 px-4 font-mono text-[#005EA4] font-bold whitespace-nowrap">768 × 600 px</td>
                                <td class="py-3 px-4 whitespace-nowrap">1.28 : 1</td>
                                <td class="py-3 px-4 whitespace-nowrap">&lt; 200 KB</td>
                                <td class="py-3 px-4 font-bold whitespace-nowrap">WebP, JPG</td>
                                <td class="py-3 px-4 text-slate-500">Hiển thị sắc nét trên điện thoại, tránh chữ nhỏ sát mép</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">Ảnh Sản phẩm &amp; Thiết bị</td>
                                <td class="py-3 px-4 font-mono text-[#005EA4] font-bold whitespace-nowrap">800 × 800 px</td>
                                <td class="py-3 px-4 whitespace-nowrap">1 : 1 (Vuông)</td>
                                <td class="py-3 px-4 whitespace-nowrap">&lt; 250 KB</td>
                                <td class="py-3 px-4 font-bold whitespace-nowrap">WebP, PNG</td>
                                <td class="py-3 px-4 text-slate-500">Nền trắng hoặc ảnh thực tế sạch, chụp rõ mã linh kiện và bản vẽ kỹ thuật</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">Ảnh Dịch vụ &amp; Quy trình</td>
                                <td class="py-3 px-4 font-mono text-[#005EA4] font-bold whitespace-nowrap">1200 × 800 px</td>
                                <td class="py-3 px-4 whitespace-nowrap">3 : 2</td>
                                <td class="py-3 px-4 whitespace-nowrap">&lt; 300 KB</td>
                                <td class="py-3 px-4 font-bold whitespace-nowrap">WebP, JPG</td>
                                <td class="py-3 px-4 text-slate-500">Chụp thực tế hệ thống thang cuốn, dây chuyền băng tải, quạt BLDC tại công trình</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">Ảnh Bài viết &amp; Tin tức</td>
                                <td class="py-3 px-4 font-mono text-[#005EA4] font-bold whitespace-nowrap">1200 × 675 px</td>
                                <td class="py-3 px-4 whitespace-nowrap">16 : 9</td>
                                <td class="py-3 px-4 whitespace-nowrap">&lt; 300 KB</td>
                                <td class="py-3 px-4 font-bold whitespace-nowrap">WebP, JPG</td>
                                <td class="py-3 px-4 text-slate-500">Ảnh bìa bài viết chuẩn hiển thị feed mạng xã hội</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">Ảnh Chia sẻ Mạng Xã hội (OG Image)</td>
                                <td class="py-3 px-4 font-mono text-[#005EA4] font-bold whitespace-nowrap">1200 × 630 px</td>
                                <td class="py-3 px-4 whitespace-nowrap">1.91 : 1</td>
                                <td class="py-3 px-4 whitespace-nowrap">&lt; 300 KB</td>
                                <td class="py-3 px-4 font-bold whitespace-nowrap">WebP, JPG</td>
                                <td class="py-3 px-4 text-slate-500">Hiển thị khi gửi link website qua Zalo, Facebook, LinkedIn</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table 2: Technical Drawing Standards -->
            <div class="min-w-0">
                <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-start sm:items-center gap-2">
                    <span class="w-2 h-4 bg-emerald-600 rounded-full inline-block shrink-0 mt-0.5 sm:mt-0"></span>
                    <span>2. Định dạng Bản vẽ CAD &amp; Hồ sơ Kỹ thuật Hợp Lệ</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 min-w-0">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded bg-[#EDF6FF] text-[#005EA4] font-mono font-bold text-xs shrink-0">.DWG / .DXF</span>
                            <span class="text-[11px] font-bold text-slate-400">Phổ biến nhất</span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 mb-1">AutoCAD Drawing Exchange</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Định dạng bản vẽ kỹ thuật 2D/3D tiêu chuẩn cho sơ đồ tuyến băng tải, hố thang cuốn và kích thước lắp đặt chi tiết.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 min-w-0">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-mono font-bold text-xs shrink-0">.STEP / .STP</span>
                            <span class="text-[11px] font-bold text-slate-400">Chuẩn kỹ thuật</span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 mb-1">Standard for Product Model Data</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            File CAD gốc hình học tham số tiêu chuẩn ISO từ SolidWorks, Fusion 360, Inventor, Catia. Cho phép chỉnh sửa kích thước chính xác.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 min-w-0">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-800 font-mono font-bold text-xs shrink-0">.OBJ</span>
                            <span class="text-[11px] font-bold text-slate-400">Mô hình &amp; Màu</span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 mb-1">Wavefront 3D Object</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Thích hợp cho mô hình nhân vật, tượng game, nghệ thuật điêu khắc số từ ZBrush hoặc Blender kèm thông tin texture/màu.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 min-w-0">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-mono font-bold text-xs shrink-0">.IGES / .IGS</span>
                            <span class="text-[11px] font-bold text-slate-400">Cơ khí chính xác</span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 mb-1">Initial Graphics Exchange</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Định dạng trao đổi bề mặt cong cơ khí chuẩn công nghiệp, tương thích với hầu hết các phần mềm CAD/CAM truyền thống.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 sm:col-span-2 min-w-0">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-800 font-mono font-bold text-xs shrink-0">.ZIP / .RAR</span>
                            <span class="text-[11px] font-bold text-slate-400">Nén nhiều tệp</span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 mb-1">Tệp nén cụm chi tiết lắp ráp</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Áp dụng khi khách gửi cụm chi tiết nhiều tệp hoặc bản vẽ kèm tệp CAD. Dung lượng khuyến nghị tải lên qua web &lt; 50MB. Nếu tệp lớn hơn, hướng dẫn khách gửi link Google Drive / OneDrive qua số Zalo Hotline.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Table 3: 5-Step Publishing Checklist -->
            <div class="min-w-0">
                <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-start sm:items-center gap-2">
                    <span class="w-2 h-4 bg-indigo-600 rounded-full inline-block shrink-0 mt-0.5 sm:mt-0"></span>
                    <span>3. Checklist 5 Bước Trước Khi Xuất Bản Dữ Liệu Song Ngữ &amp; SEO</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-white flex flex-col justify-between min-w-0">
                        <div>
                            <span class="w-6 h-6 rounded-md bg-[#EDF6FF] text-[#005EA4] font-extrabold text-xs flex items-center justify-center mb-2">1</span>
                            <h4 class="text-xs font-bold text-slate-800 mb-1">Đủ 2 ngôn ngữ</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Kiểm tra cả 2 tab <span class="font-bold">VI</span> và <span class="font-bold">EN</span> (Tiêu đề, mô tả, nội dung chi tiết) không bị bỏ trống.
                            </p>
                        </div>
                        <div class="text-[10px] font-bold text-emerald-600 mt-2 flex items-center gap-1">
                            <i class="pi pi-check text-[10px] shrink-0"></i> Đa ngôn ngữ
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-slate-200 bg-white flex flex-col justify-between min-w-0">
                        <div>
                            <span class="w-6 h-6 rounded-md bg-[#EDF6FF] text-[#005EA4] font-extrabold text-xs flex items-center justify-center mb-2">2</span>
                            <h4 class="text-xs font-bold text-slate-800 mb-1">Tối ưu thẻ SEO</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Điền Meta Title (dưới 65 ký tự) và Meta Description (140-160 ký tự) có chứa từ khóa thiết bị / phụ tùng chính.
                            </p>
                        </div>
                        <div class="text-[10px] font-bold text-emerald-600 mt-2 flex items-center gap-1">
                            <i class="pi pi-check text-[10px] shrink-0"></i> Chuẩn Onpage
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-slate-200 bg-white flex flex-col justify-between min-w-0">
                        <div>
                            <span class="w-6 h-6 rounded-md bg-[#EDF6FF] text-[#005EA4] font-extrabold text-xs flex items-center justify-center mb-2">3</span>
                            <h4 class="text-xs font-bold text-slate-800 mb-1">Kiểm tra Slug</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Đảm bảo đường dẫn tĩnh không dấu, ngăn cách bằng gạch nối <code class="text-slate-700">-</code>, không chứa ký tự lạ.
                            </p>
                        </div>
                        <div class="text-[10px] font-bold text-emerald-600 mt-2 flex items-center gap-1">
                            <i class="pi pi-check text-[10px] shrink-0"></i> Đường dẫn sạch
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-slate-200 bg-white flex flex-col justify-between min-w-0">
                        <div>
                            <span class="w-6 h-6 rounded-md bg-[#EDF6FF] text-[#005EA4] font-extrabold text-xs flex items-center justify-center mb-2">4</span>
                            <h4 class="text-xs font-bold text-slate-800 mb-1">Nén ảnh &amp; Alt</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Đảm bảo dung lượng ảnh dưới 300KB định dạng WebP và có đặt tên file chứa từ khóa mô tả sản phẩm.
                            </p>
                        </div>
                        <div class="text-[10px] font-bold text-emerald-600 mt-2 flex items-center gap-1">
                            <i class="pi pi-check text-[10px] shrink-0"></i> Tốc độ cao
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-slate-200 bg-white flex flex-col justify-between min-w-0 sm:col-span-2 lg:col-span-1">
                        <div>
                            <span class="w-6 h-6 rounded-md bg-[#EDF6FF] text-[#005EA4] font-extrabold text-xs flex items-center justify-center mb-2">5</span>
                            <h4 class="text-xs font-bold text-slate-800 mb-1">Xóa Cache</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Sau khi sửa Cài đặt chung hoặc Banner, làm mới cache hệ thống và mở trình duyệt ẩn danh để nghiệm thu.
                            </p>
                        </div>
                        <div class="text-[10px] font-bold text-emerald-600 mt-2 flex items-center gap-1">
                            <i class="pi pi-check text-[10px] shrink-0"></i> Đồng bộ ngay
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Footer -->
        <div class="text-center text-xs text-slate-400 py-4 px-2 leading-relaxed">
            Thành Khang CMS • Hotline Hỗ trợ Kỹ thuật &amp; Báo giá: <span class="font-semibold text-slate-600">0967.732.665 (Xem Cài đặt chung)</span>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'

export default {
    name: 'UserGuide',
    components: {
        Head,
        Link,
    },
    data() {
        return {
            searchQuery: '',
            activeTab: 'ALL',
            tabs: [
                { id: 'ALL', label: 'Tất cả Module', icon: 'pi pi-th-large' },
                { id: 'DASHBOARD', label: '1. Bảng điều khiển', icon: 'pi pi-chart-bar' },
                { id: 'FACTORY', label: '2. Danh mục & Báo giá B2B', icon: 'pi pi-box' },
                { id: 'CONTENT', label: '3. Nội dung & Thương hiệu', icon: 'pi pi-images' },
                { id: 'SYSTEM', label: '4. Hệ thống & SEO', icon: 'pi pi-cog' },
            ],
            allModules: [
                // 1. DASHBOARD
                {
                    id: 'dashboard',
                    category: 'DASHBOARD',
                    categoryName: '1. Bảng điều khiển',
                    categoryBadge: 'bg-[#EDF6FF] text-[#005EA4]',
                    icon: 'pi pi-chart-bar',
                    iconBg: 'bg-[#EDF6FF] text-[#005EA4]',
                    title: 'Bảng điều khiển & Thống kê RFQ',
                    summary: 'Theo dõi tổng quan số lượng yêu cầu báo giá phát sinh trong ngày, dự báo tháng, tốc độ tăng trưởng MoM và cơ cấu các nhóm sản phẩm/dịch vụ.',
                    steps: [
                        'Xem 4 thẻ chỉ số KPI ở đầu trang: Báo giá hôm nay, Dự báo báo giá tháng, Tăng trưởng MoM (%) và Tỉ lệ chuyển đổi đơn hàng.',
                        'Lựa chọn năm tại bộ lọc Năm để tra cứu biểu đồ xu hướng số lượng yêu cầu báo giá theo từng tháng.',
                        'Phân tích cơ cấu các lĩnh vực cốt lõi (Phụ tùng thang cuốn, Xích tải & Băng tải, Quạt công nghiệp BLDC, Tư vấn kỹ thuật) tại biểu đồ tròn.',
                        'Tra cứu nhanh danh sách 8 yêu cầu báo giá mới nhất ở bảng dưới cùng và nhấn "Xem" để mở chi tiết.',
                    ],
                    tips: 'Dữ liệu thẻ "Báo giá hôm nay" tự động tính từ 00:00 mỗi ngày. Nhấn nút "Xem tất cả" tại bảng báo giá gần đây để mở toàn bộ danh sách.',
                    permission: 'admin.dashboard.index',
                    routeName: 'admin.dashboard.index',
                    keywords: ['dashboard', 'kpi', 'thống kê', 'rfq', 'báo giá', 'doanh số', 'biểu đồ', 'tăng trưởng'],
                },

                // 2. FACTORY - Contacts
                {
                    id: 'contacts',
                    category: 'FACTORY',
                    categoryName: '2. Danh mục & Báo giá B2B',
                    categoryBadge: 'bg-emerald-100 text-emerald-700',
                    icon: 'pi pi-send',
                    iconBg: 'bg-emerald-50 text-emerald-600',
                    title: 'Báo giá & Yêu cầu Kỹ thuật (RFQ)',
                    summary: 'Quản lý luồng tiếp nhận yêu cầu từ form đối tác, tải bản vẽ CAD/catalogue (.STEP, .DWG, .PDF...), đối chiếu thông số và cập nhật trạng thái đơn.',
                    steps: [
                        'Vào danh sách yêu cầu, lọc theo trạng thái "Mới (NEW)" để ưu tiên xử lý các yêu cầu vừa gửi đến.',
                        'Mở chi tiết yêu cầu để xem thông tin liên hệ, mã linh kiện / vật liệu mong muốn (40Cr, Inox 304/316, POM, EPDM...), số lượng và ghi chú công trình.',
                        'Tải tệp kỹ thuật đính kèm (.STEP, .DWG, .PDF, .ZIP) về máy tính để đối chiếu bước xích, tải trọng đứt và thông số lắp đặt.',
                        'Liên hệ đối tác qua Điện thoại/Zalo để tư vấn kỹ thuật và gửi báo giá chi tiết, sau đó chuyển trạng thái sang "Đã phản hồi (RESPONSE)".',
                        'Khi đối tác chốt PO và xác nhận đơn hàng, chuyển sang "Đã xác nhận (CONFIRMED)". Sau khi xuất kho, kiểm định CO/CQ và bàn giao chuyển sang "Đã xử lý (PROCESSED)".',
                    ],
                    tips: 'Nếu yêu cầu là quảng cáo hoặc bot tự động, hãy chọn trạng thái "Spam (IS_SPAM)" để không tính vào báo cáo KPI tháng.',
                    permission: 'admin.contacts.index',
                    routeName: 'admin.contacts.index',
                    keywords: ['báo giá', 'cad', 'dwg', 'step', 'pdf', 'rfq', 'contact', 'yêu cầu', 'new', 'response', 'confirmed', 'processed', 'close', 'spam'],
                },

                // 2. FACTORY - Products
                {
                    id: 'products',
                    category: 'FACTORY',
                    categoryName: '2. Danh mục & Báo giá B2B',
                    categoryBadge: 'bg-emerald-100 text-emerald-700',
                    icon: 'pi pi-box',
                    iconBg: 'bg-emerald-50 text-emerald-600',
                    title: 'Quản lý Sản phẩm & Linh kiện',
                    summary: 'Đăng tải và quản lý các dòng xích bậc thang cuốn, xích tải công nghiệp Universal Chains, phụ tùng thang nâng và quạt BLDC với đầy đủ thông số kỹ thuật song ngữ.',
                    steps: [
                        'Nhấn "Thêm mới" hoặc chọn sản phẩm cần cập nhật từ danh sách.',
                        'Điền Tiêu đề và Mô tả sản phẩm ở cả 2 tab Tiếng Việt (VI) và English (EN).',
                        'Chọn Danh mục sản phẩm tương ứng và nhập Giá tham khảo (hoặc để liên hệ báo giá B2B).',
                        'Tải lên Ảnh đại diện chính (tỉ lệ 1:1 vuông, kích thước chuẩn 800x800px) và Thư viện ảnh / bản vẽ thông số kỹ thuật.',
                        'Điền Thông số kỹ thuật (Vật liệu, Bước xích / Kích thước, Tải trọng đứt, Tiêu chuẩn ISO/DIN) và kiểm tra thẻ Meta SEO trước khi bật "Kích hoạt (ACTIVE)".',
                    ],
                    tips: 'Để sản phẩm hiển thị lên đầu trang chủ hoặc trang danh mục, hãy chỉnh số thứ tự hiển thị (Position) nhỏ hơn (ví dụ: 1, 2, 3).',
                    permission: 'admin.products.index',
                    routeName: 'admin.products.index',
                    keywords: ['sản phẩm', 'products', 'xích tải', 'linh kiện', 'thang cuốn', 'active', 'song ngữ', 'tiếng anh'],
                },

                // 2. FACTORY - Product Categories
                {
                    id: 'product-categories',
                    category: 'FACTORY',
                    categoryName: '2. Danh mục & Báo giá B2B',
                    categoryBadge: 'bg-emerald-100 text-emerald-700',
                    icon: 'pi pi-tags',
                    iconBg: 'bg-emerald-50 text-emerald-600',
                    title: 'Danh mục Sản phẩm',
                    summary: 'Phân cấp các nhóm sản phẩm theo ứng dụng công nghiệp (Linh kiện thang cuốn, Thang nâng ô tô, Băng tải hành lý, Dây chuyền đóng gói).',
                    steps: [
                        'Tạo mới danh mục với Tên song ngữ VI/EN và kiểm tra đường dẫn tĩnh (Slug) tự động sinh ra.',
                        'Chọn Danh mục cha nếu là danh mục cấp 2, hoặc để trống nếu là danh mục gốc.',
                        'Tải lên Ảnh đại diện danh mục (kích thước chuẩn 600x400px hoặc 800x800px).',
                        'Thiết lập thứ tự sắp xếp và điền thông tin Meta Title, Meta Description chuẩn SEO.',
                    ],
                    tips: 'Không nên xóa danh mục đang có sản phẩm liên kết; hãy chuyển sản phẩm sang danh mục khác trước khi xóa.',
                    permission: 'admin.product-categories.index',
                    routeName: 'admin.product-categories.index',
                    keywords: ['danh mục', 'categories', 'phân loại sản phẩm', 'slug', 'thang cuốn', 'xích công nghiệp'],
                },

                // 2. FACTORY - Services
                {
                    id: 'services',
                    category: 'FACTORY',
                    categoryName: '2. Danh mục & Báo giá B2B',
                    categoryBadge: 'bg-emerald-100 text-emerald-700',
                    icon: 'pi pi-cog',
                    iconBg: 'bg-emerald-50 text-emerald-600',
                    title: 'Giải pháp & Dịch vụ Kỹ thuật',
                    summary: 'Biên tập trang giải pháp chuyên sâu với các khối JSON động: Hero, Bảng thông số kỹ thuật, Quy trình 5 bước, Ưu điểm, Cam kết và FAQ.',
                    steps: [
                        'Chọn dịch vụ cần chỉnh sửa (ví dụ: Cung ứng Phụ tùng Thang cuốn, Hệ thống Xích tải & Băng tải, Giải pháp Quạt BLDC HVLS...).',
                        'Cập nhật Tiêu đề, Tóm tắt và Nội dung chi tiết song ngữ VI/EN.',
                        'Cấu hình khối "Bảng thông số kỹ thuật": nhập tiêu chuẩn kỹ thuật (EN 115, ISO 9001, DIN/ANSI), vật liệu (40Cr, SUS304/316, EPDM...), tải trọng.',
                        'Kiểm tra khối "Quy trình 5 bước": Tiếp nhận thông số -> Đối chiếu OEM -> Báo giá & Tiến độ -> Kiểm định CO/CQ -> Giao hàng & Hỗ trợ.',
                        'Cập nhật khối "Câu hỏi thường gặp (FAQ)" đặc thù của từng mảng giải pháp và nhấn "Lưu thay đổi".',
                    ],
                    tips: 'Khối dữ liệu JSON động của trang dịch vụ quyết định giao diện hiển thị ngoài Frontend; kiểm tra cẩn thận cả 2 tab ngôn ngữ trước khi lưu.',
                    permission: 'admin.services.index',
                    routeName: 'admin.services.index',
                    keywords: ['dịch vụ', 'services', 'thang cuốn', 'băng tải', 'bldc', 'kỹ thuật', 'quy trình', 'thông số', 'faq'],
                },

                // 3. CONTENT - Sliders & Banners
                {
                    id: 'sliders',
                    category: 'CONTENT',
                    categoryName: '3. Nội dung & Thương hiệu',
                    categoryBadge: 'bg-purple-100 text-purple-700',
                    icon: 'pi pi-images',
                    iconBg: 'bg-purple-50 text-purple-600',
                    title: 'Banner & Hero Trang chủ',
                    summary: 'Quản lý Slider chuyển động lớn tại đầu trang chủ và thanh thông báo lật trên Header (HOME_FLIP_ANNOUNCEMENT).',
                    steps: [
                        'Chọn vị trí banner cần quản lý: Hero Banner Trang chủ hoặc lọc theo position "HOME_FLIP_ANNOUNCEMENT".',
                        'Với Hero Banner: Tải lên Ảnh Desktop (1920x800px) và Ảnh Mobile (768x600px) để banner hiển thị sắc nét trên điện thoại.',
                        'Điền Tiêu đề lớn (Headline), Mô tả ngắn và Nhãn nút bấm kèm đường dẫn liên kết (ví dụ: dẫn tới trang /san-pham hoặc nút Báo giá).',
                        'Với HOME_FLIP_ANNOUNCEMENT: Nhập nội dung thông điệp lật (ví dụ: "Tư vấn kỹ thuật & Báo giá nhanh B2B", "Cung ứng phụ tùng OEM toàn quốc").',
                        'Thiết lập Thứ tự hiển thị và chuyển trạng thái sang "Kích hoạt".',
                    ],
                    tips: 'Dung lượng ảnh Hero Desktop nên dưới 350KB định dạng WebP để tối ưu chỉ số Core Web Vitals (LCP) của trang web.',
                    permission: 'admin.sliders.index',
                    routeName: 'admin.sliders.index',
                    keywords: ['banner', 'slider', 'hero', 'home_flip_announcement', 'thông báo', 'trang chủ', 'hình ảnh'],
                },

                // 3. CONTENT - Posts
                {
                    id: 'posts',
                    category: 'CONTENT',
                    categoryName: '3. Nội dung & Thương hiệu',
                    categoryBadge: 'bg-purple-100 text-purple-700',
                    icon: 'pi pi-file-edit',
                    iconBg: 'bg-purple-50 text-purple-600',
                    title: 'Bài viết & Tin tức Kỹ thuật',
                    summary: 'Xuất bản các bài viết chia sẻ kiến thức bảo trì thang cuốn, lựa chọn xích tải công nghiệp, tiết kiệm điện nhà xưởng và tin tức dự án.',
                    steps: [
                        'Nhấn "Thêm bài viết mới" và nhập Tiêu đề hấp dẫn, chuẩn từ khóa SEO.',
                        'Soạn thảo nội dung bằng trình biên tập Rich Text (hỗ trợ chèn tiêu đề H2, H3, bảng so sánh và ảnh minh họa).',
                        'Tải lên Ảnh đại diện bài viết (tỉ lệ 16:9, kích thước chuẩn 1200x675px).',
                        'Chọn Danh mục bài viết tương ứng và gắn các thẻ Tag liên quan (Xích thang cuốn, Universal Chains, Quạt BLDC, Bảo trì...).',
                        'Điền Meta Title, Meta Description, kiểm tra đường dẫn Slug không dấu và chuyển sang trạng thái "Xuất bản (PUBLISHED)".',
                    ],
                    tips: 'Chèn liên kết nội bộ (Internal Link) trỏ về trang Sản phẩm hoặc form Báo giá B2B trong thân bài viết để tăng tỉ lệ chuyển đổi.',
                    permission: 'admin.posts.index',
                    routeName: 'admin.posts.index',
                    keywords: ['bài viết', 'posts', 'tin tức', 'blog', 'kiến thức kỹ thuật', 'seo', 'slug', 'rich text'],
                },

                // 3. CONTENT - Post Categories
                {
                    id: 'post-categories',
                    category: 'CONTENT',
                    categoryName: '3. Nội dung & Thương hiệu',
                    categoryBadge: 'bg-purple-100 text-purple-700',
                    icon: 'pi pi-folder',
                    iconBg: 'bg-purple-50 text-purple-600',
                    title: 'Danh mục Tin tức & Kiến thức',
                    summary: 'Phân loại các chủ đề bài viết như: Kiến thức Kỹ thuật, Cẩm nang Bảo trì, Dự án Tiêu biểu, Tin tức Thành Khang.',
                    steps: [
                        'Thêm danh mục mới với tên Tiếng Việt và Tiếng Anh.',
                        'Cấu hình Slug và Mô tả ngắn cho chuyên mục.',
                        'Thiết lập thứ tự ưu tiên hiển thị trên thanh điều hướng tin tức.',
                    ],
                    tips: 'Giữ số lượng danh mục bài viết từ 3-5 danh mục chính để cấu trúc SEO của website không bị phân mảnh.',
                    permission: 'admin.post-categories.index',
                    routeName: 'admin.post-categories.index',
                    keywords: ['danh mục bài viết', 'chuyên mục tin tức', 'post categories', 'kiến thức', 'vật liệu'],
                },

                // 3. CONTENT - Policies
                {
                    id: 'policies',
                    category: 'CONTENT',
                    categoryName: '3. Nội dung & Thương hiệu',
                    categoryBadge: 'bg-purple-100 text-purple-700',
                    icon: 'pi pi-shield',
                    iconBg: 'bg-purple-50 text-purple-600',
                    title: 'Chính sách & Cam kết Dịch vụ',
                    summary: 'Quản lý các trang quy định pháp lý và niềm tin: Chính sách bảo mật, Chính sách đổi trả/in lại, Cam kết bảo mật file CAD (NDA).',
                    steps: [
                        'Mở chính sách cần cập nhật nội dung từ danh sách (ví dụ: Cam kết bảo mật file mẫu in & bản vẽ kỹ thuật).',
                        'Soạn thảo điều khoản rõ ràng, minh bạch cho khách hàng cá nhân và doanh nghiệp.',
                        'Cập nhật đồng thời tab Tiếng Việt và English để phục vụ cả khách hàng quốc tế.',
                        'Nhấn "Lưu thay đổi" và kiểm tra hiển thị tại khu vực chân trang (Footer).',
                    ],
                    tips: 'Chính sách bảo mật bản vẽ CAD là yếu tố then chốt giúp khách hàng doanh nghiệp yên tâm gửi file thiết kế mật.',
                    permission: 'admin.policies.index',
                    routeName: 'admin.policies.index',
                    keywords: ['chính sách', 'policies', 'cam kết', 'bảo mật cad', 'đổi trả', 'footer', 'nda'],
                },

                // 4. SYSTEM - Users
                {
                    id: 'users',
                    category: 'SYSTEM',
                    categoryName: '4. Hệ thống & SEO',
                    categoryBadge: 'bg-slate-100 text-slate-700',
                    icon: 'pi pi-users',
                    iconBg: 'bg-slate-100 text-slate-700',
                    title: 'Khách hàng Đăng ký',
                    summary: 'Tra cứu danh sách thành viên đăng ký trên website, lịch sử tương tác, trạng thái kích hoạt tài khoản.',
                    steps: [
                        'Tra cứu khách hàng theo Họ tên, Email hoặc Số điện thoại thông qua thanh tìm kiếm.',
                        'Xem chi tiết thông tin đăng ký, ngày tạo tài khoản và các đơn hàng/yêu cầu liên kết.',
                        'Khóa hoặc kích hoạt lại tài khoản khách hàng khi có yêu cầu hỗ trợ.',
                    ],
                    tips: 'Không tự ý thay đổi mật khẩu của khách; hãy hướng dẫn khách sử dụng tính năng "Quên mật khẩu" trên trang đăng nhập.',
                    permission: 'admin.users.index',
                    routeName: 'admin.users.index',
                    keywords: ['khách hàng', 'users', 'thành viên', 'tài khoản', 'email', 'số điện thoại'],
                },

                // 4. SYSTEM - Email Templates
                {
                    id: 'email-templates',
                    category: 'SYSTEM',
                    categoryName: '4. Hệ thống & SEO',
                    categoryBadge: 'bg-slate-100 text-slate-700',
                    icon: 'pi pi-envelope',
                    iconBg: 'bg-slate-100 text-slate-700',
                    title: 'Mẫu Email Thông báo Tự động',
                    summary: 'Tùy biến nội dung email gửi tự động cho khách khi gửi yêu cầu báo giá, xác nhận đơn hàng hoặc cập nhật tiến độ giao nhận.',
                    steps: [
                        'Chọn mẫu email cần chỉnh sửa (ví dụ: Xác nhận đã nhận yêu cầu báo giá B2B, Thông báo đơn hàng hoàn tất).',
                        'Cập nhật Tiêu đề thư (Subject) và nội dung thân thư.',
                        'Sử dụng các biến động có sẵn: {customer_name}, {quote_id}, {service_name}, {phone}, {created_at}...',
                        'Lưu mẫu và gửi thử nghiệm một email test để kiểm tra định dạng hiển thị trên Gmail/Outlook.',
                    ],
                    tips: 'Luôn giữ nguyên các thẻ biến động dạng {ten_bien} để hệ thống tự động điền dữ liệu thực của khách hàng.',
                    permission: 'admin.email-templates.index',
                    routeName: 'admin.email-templates.index',
                    keywords: ['email', 'mẫu email', 'templates', 'thông báo', 'tự động', 'biến động', 'xác nhận'],
                },

                // 4. SYSTEM - Files
                {
                    id: 'files',
                    category: 'SYSTEM',
                    categoryName: '4. Hệ thống & SEO',
                    categoryBadge: 'bg-slate-100 text-slate-700',
                    icon: 'pi pi-image',
                    iconBg: 'bg-slate-100 text-slate-700',
                    title: 'Quản lý Tệp Media',
                    summary: 'Kho lưu trữ tập trung toàn bộ hình ảnh sản phẩm, banner, tài liệu kỹ thuật và file phương tiện của hệ thống.',
                    steps: [
                        'Tải lên hình ảnh hoặc tệp mới bằng cách kéo thả hoặc chọn từ máy tính.',
                        'Tổ chức tệp vào các thư mục tương ứng (banners, products, services, posts) để dễ quản lý.',
                        'Sao chép đường dẫn URL của tệp để nhúng vào bài viết hoặc giao diện khi cần.',
                        'Xóa các tệp rác hoặc ảnh trùng lặp để tiết kiệm dung lượng ổ cứng máy chủ.',
                    ],
                    tips: 'Trước khi tải ảnh lên, hãy đổi tên file tiếng Việt không dấu (ví dụ: xich-tai-thang-cuon-tl133.webp) để chuẩn SEO hình ảnh.',
                    permission: 'admin.files.index',
                    routeName: 'admin.files.index',
                    keywords: ['tệp', 'files', 'media', 'thư viện ảnh', 'upload', 'hình ảnh', 'tài liệu'],
                },

                // 4. SYSTEM - Settings
                {
                    id: 'settings',
                    category: 'SYSTEM',
                    categoryName: '4. Hệ thống & SEO',
                    categoryBadge: 'bg-slate-100 text-slate-700',
                    icon: 'pi pi-phone',
                    iconBg: 'bg-slate-100 text-slate-700',
                    title: 'Cài đặt chung & Hotline / Zalo',
                    summary: 'Cấu hình số điện thoại Hotline, số Zalo tư vấn kỹ thuật, địa chỉ văn phòng/kho, email liên hệ và mã nhúng script.',
                    steps: [
                        'Truy cập trang Cài đặt chung (/admin/settings/form/general).',
                        'Cập nhật Số điện thoại Hotline chính và Số Zalo kỹ thuật (dùng để đối tác gửi bản vẽ hoặc mã linh kiện nhanh).',
                        'Cập nhật Địa chỉ trụ sở & văn phòng giao dịch, Giờ làm việc và Email nhận RFQ.',
                        'Kiểm tra các liên kết Mạng xã hội: Facebook Fanpage, Kênh TikTok/YouTube kỹ thuật.',
                        'Nhấn "Lưu cấu hình" và tải lại trang chủ để kiểm tra số điện thoại trên nút gọi nhanh / widget Zalo.',
                    ],
                    tips: 'Số điện thoại và Zalo tại đây xuất hiện trên toàn bộ Header, Footer và popup Báo giá nhanh của website.',
                    permission: 'admin.settings.index',
                    routeName: 'admin.settings.form',
                    routeParams: { id: 'general' },
                    keywords: ['cài đặt', 'settings', 'hotline', 'zalo', 'địa chỉ xưởng', 'số điện thoại', 'general', 'cấu hình'],
                },

                // 4. SYSTEM - Admins
                {
                    id: 'admins',
                    category: 'SYSTEM',
                    categoryName: '4. Hệ thống & SEO',
                    categoryBadge: 'bg-slate-100 text-slate-700',
                    icon: 'pi pi-user-plus',
                    iconBg: 'bg-slate-100 text-slate-700',
                    title: 'Quản trị viên CMS',
                    summary: 'Quản lý danh sách nhân sự vận hành, kỹ sư tư vấn kỹ thuật và biên tập viên nội dung có quyền đăng nhập CMS.',
                    steps: [
                        'Nhấn "Thêm quản trị viên mới" và nhập Họ tên, Email công việc và Mật khẩu khởi tạo an toàn.',
                        'Gán Vai trò phù hợp cho nhân viên (ví dụ: Kỹ sư tư vấn, Biên tập viên nội dung hoặc Super Admin).',
                        'Kích hoạt tài khoản và gửi thông tin đăng nhập bảo mật cho nhân sự.',
                        'Khóa quyền truy cập ngay lập tức khi nhân sự chuyển đổi vị trí hoặc nghỉ việc.',
                    ],
                    tips: 'Tuyệt đối không cấp quyền Super Admin cho tài khoản chỉ làm nhiệm vụ viết bài hoặc trực hotline.',
                    permission: 'admin.admins.index',
                    routeName: 'admin.admins.index',
                    keywords: ['quản trị viên', 'admins', 'nhân sự', 'tài khoản cms', 'phân quyền', 'mật khẩu'],
                },

                // 4. SYSTEM - Roles
                {
                    id: 'roles',
                    category: 'SYSTEM',
                    categoryName: '4. Hệ thống & SEO',
                    categoryBadge: 'bg-slate-100 text-slate-700',
                    icon: 'pi pi-key',
                    iconBg: 'bg-slate-100 text-slate-700',
                    title: 'Phân quyền Vai trò (RBAC)',
                    summary: 'Thiết lập ma trận quyền hạn chi tiết (Xem, Thêm, Sửa, Xóa) cho từng nhóm vị trí công việc trong hệ thống quản trị.',
                    steps: [
                        'Xem danh sách các Vai trò hiện có hoặc nhấn "Thêm vai trò mới".',
                        'Tích chọn các nhóm quyền tương ứng với trách nhiệm: Quản lý Báo giá, Quản lý Sản phẩm, Biên tập Bài viết, Quản lý Cài đặt...',
                        'Lưu cấu hình vai trò và kiểm tra lại danh sách quản trị viên đang thuộc vai trò đó.',
                    ],
                    tips: 'Nguyên tắc đặc quyền tối thiểu (Least Privilege): Chỉ cấp đúng các quyền cần thiết để nhân viên hoàn thành công việc.',
                    permission: 'admin.roles.index',
                    routeName: 'admin.roles.index',
                    keywords: ['vai trò', 'roles', 'phân quyền', 'rbac', 'permissions', 'bảo mật', 'quyền hạn'],
                },

                // 4. SYSTEM - System Logs
                {
                    id: 'system-logs',
                    category: 'SYSTEM',
                    categoryName: '4. Hệ thống & SEO',
                    categoryBadge: 'bg-slate-100 text-slate-700',
                    icon: 'pi pi-server',
                    iconBg: 'bg-slate-100 text-slate-700',
                    title: 'Logs Hệ thống & Quản lý Cache',
                    summary: 'Theo dõi nhật ký lỗi ứng dụng (laravel.log), kiểm tra hoạt động nền và giải phóng bộ nhớ đệm khi cập nhật nội dung.',
                    steps: [
                        'Mở trang Logs Hệ thống để xem các thông báo lỗi hoặc cảnh báo gần nhất của server.',
                        'Nhấn vào từng file log để đọc chi tiết Stack Trace khi có lỗi phát sinh trong quá trình vận hành.',
                        'Nhấn "Xóa nội dung file log" (Clear) để giải phóng dung lượng log định kỳ.',
                        'Khi vừa cập nhật Banner, Dịch vụ hoặc Bản dịch đa ngôn ngữ mà ngoài web chưa đổi, hãy làm mới cache hệ thống.',
                    ],
                    tips: 'Nếu web gặp hiện tượng tải chậm hoặc dữ liệu cũ không cập nhật, kiểm tra log để xem có lỗi kết nối Redis/MySQL không.',
                    permission: 'admin.system-logs.index',
                    routeName: 'admin.system-logs.index',
                    keywords: ['logs', 'nhật ký', 'system logs', 'cache', 'xóa cache', 'lỗi', 'stack trace', 'laravel.log'],
                },
            ],
        }
    },
    computed: {
        filteredModules() {
            let list = this.allModules

            // Filter by active Tab
            if (this.activeTab !== 'ALL') {
                list = list.filter((m) => m.category === this.activeTab)
            }

            // Filter by Search Query
            if (this.searchQuery && this.searchQuery.trim()) {
                const q = this.normalizeStr(this.searchQuery.trim())
                list = list.filter((m) => {
                    const matchTitle = this.normalizeStr(m.title).includes(q)
                    const matchSummary = this.normalizeStr(m.summary).includes(q)
                    const matchCategory = this.normalizeStr(m.categoryName).includes(q)
                    const matchTips = this.normalizeStr(m.tips).includes(q)
                    const matchSteps = m.steps.some((s) => this.normalizeStr(s).includes(q))
                    const matchKeywords = m.keywords && m.keywords.some((k) => this.normalizeStr(k).includes(q))

                    return matchTitle || matchSummary || matchCategory || matchTips || matchSteps || matchKeywords
                })
            }

            return list
        },
    },
    methods: {
        normalizeStr(str) {
            if (!str) return ''
            return str
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
        },
        getTabCount(tabId) {
            if (tabId === 'ALL') return this.allModules.length
            return this.allModules.filter((m) => m.category === tabId).length
        },
        canAccess(item) {
            if (!item.permission) return true
            try {
                return typeof this.can === 'function' ? this.can(item.permission) : true
            } catch (e) {
                return true
            }
        },
        getRouteUrl(item) {
            try {
                return this.route(item.routeName, item.routeParams || {})
            } catch (e) {
                return '#'
            }
        },
        resetSearch() {
            this.searchQuery = ''
            this.activeTab = 'ALL'
        },
    },
}
</script>
