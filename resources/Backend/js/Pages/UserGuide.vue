<template>
    <Head title="Hướng dẫn sử dụng CMS - HH Maker 3D" />

    <div class="px-6 py-8 bg-[#F8FAFC] min-h-screen text-slate-800">
        <!-- Top Navigation / Breadcrumb -->
        <div class="flex items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <Link
                    v-if="can('admin.dashboard.index')"
                    :href="route('admin.dashboard.index')"
                    class="hover:text-blue-600 transition-colors flex items-center gap-1"
                >
                    <i class="pi pi-home text-[11px]"></i>
                    <span>Bảng điều khiển</span>
                </Link>
                <span class="text-slate-300">/</span>
                <span class="text-slate-700">Hướng dẫn sử dụng CMS</span>
            </div>

            <div class="hidden sm:flex items-center gap-2 text-xs font-medium text-slate-500 bg-white px-3.5 py-1.5 rounded-lg border border-slate-200 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>HH Maker 3D Admin v2.5 • Chuẩn Vận hành</span>
            </div>
        </div>

        <!-- Page Header -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm mb-8 relative overflow-hidden">
            <div class="absolute -right-12 -top-12 w-64 h-64 bg-blue-50/60 rounded-full blur-2xl pointer-events-none"></div>

            <div class="max-w-4xl relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold uppercase tracking-wider mb-3">
                    <i class="pi pi-book text-xs"></i>
                    <span>Tài liệu nội bộ &amp; Quy chuẩn vận hành</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-2.5 h-7 bg-blue-600 rounded-full inline-block"></span>
                    Hướng dẫn sử dụng Hệ thống Admin CMS
                </h1>
                <p class="text-slate-600 text-sm sm:text-base mt-2 leading-relaxed">
                    Cẩm nang tra cứu quy trình vận hành xưởng in 3D HH Maker, hướng dẫn xử lý yêu cầu báo giá &amp; tệp CAD 4 trạng thái, biên tập nội dung song ngữ VI/EN, quản trị hình ảnh theo chuẩn kích thước và quản lý quyền hạn hệ thống.
                </p>

                <!-- 3 Quick Info Badges -->
                <div class="flex flex-wrap items-center gap-3 sm:gap-6 mt-5 pt-5 border-t border-slate-100 text-xs font-semibold text-slate-600">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[11px]">16</div>
                        <span>Module Quản trị Thực tế</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[11px]">4</div>
                        <span>Trạng thái Xử lý Báo giá &amp; CAD</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-md bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-[11px]">2</div>
                        <span>Ngôn ngữ Dữ liệu (VI / EN)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar & Instant Filter Section -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-sm mb-8 space-y-4">
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Tìm kiếm hướng dẫn (ví dụ: CAD, .STL, Báo giá, Sản phẩm, Flip Announcement, Hotline, Xóa Cache, SEO...)"
                        class="w-full pl-11 pr-10 py-3 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all text-slate-800 placeholder-slate-400 font-medium"
                    />
                    <button
                        v-if="searchQuery"
                        @click="searchQuery = ''"
                        class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-slate-200 hover:bg-slate-300 text-slate-600 flex items-center justify-center transition-colors text-xs"
                        title="Xóa tìm kiếm"
                    >
                        <i class="pi pi-times text-[10px]"></i>
                    </button>
                </div>

                <!-- Result Counter -->
                <div class="text-xs font-bold text-slate-500 whitespace-nowrap px-1">
                    Hiển thị <span class="text-blue-600 font-extrabold">{{ filteredModules.length }}</span> / {{ allModules.length }} module
                </div>
            </div>

            <!-- 5 Filter Tabs -->
            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2"
                    :class="
                        activeTab === tab.id
                            ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-600/20'
                            : 'bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200/80'
                    "
                >
                    <i :class="tab.icon" class="text-xs"></i>
                    <span>{{ tab.label }}</span>
                    <span
                        class="px-1.5 py-0.5 rounded-full text-[10px] font-extrabold"
                        :class="
                            activeTab === tab.id
                                ? 'bg-blue-700/60 text-white'
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
            class="bg-gradient-to-br from-white to-blue-50/30 rounded-2xl border border-blue-100 shadow-sm p-6 sm:p-7 mb-8"
        >
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div>
                    <div class="flex items-center gap-2 text-xs font-extrabold text-blue-600 uppercase tracking-wider">
                        <i class="pi pi-compass"></i>
                        <span>QUY TRÌNH VẬN HÀNH CỐT LÕI</span>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 mt-1">
                        Sơ đồ Xử lý Yêu cầu Báo giá &amp; Tệp CAD In 3D
                    </h2>
                </div>
                <div class="text-xs text-slate-500 italic">
                    Áp dụng cho module <span class="font-semibold text-slate-700">Báo giá &amp; Yêu cầu in 3D</span>
                </div>
            </div>

            <!-- 4 Main Steps Flow -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
                <!-- Step 1: NEW -->
                <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-sm flex flex-col relative group hover:border-blue-300 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-extrabold bg-blue-100 text-blue-800 border border-blue-200 uppercase tracking-wide">
                            1. NEW (Mới)
                        </span>
                        <span class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                            <i class="pi pi-inbox text-xs"></i>
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 mb-1">Tiếp nhận &amp; Tải file CAD</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Hệ thống tự động ghi nhận RFQ từ khách hàng kèm tệp CAD (.STL, .STEP, .OBJ, .ZIP). Kiểm tra thông tin liên hệ và số lượng đặt in.
                    </p>
                </div>

                <!-- Step 2: RESPONSE -->
                <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-sm flex flex-col relative group hover:border-amber-300 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200 uppercase tracking-wide">
                            2. RESPONSE (Đã phản hồi)
                        </span>
                        <span class="w-6 h-6 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">
                            <i class="pi pi-comments text-xs"></i>
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 mb-1">Kiểm tra lưới &amp; Báo giá</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Kỹ thuật viên mở file CAD trên phần mềm Slicer (Cura, Prusa, Bambu Studio) để kiểm tra lỗi lưới, tính thời gian in, trọng lượng nhựa và gửi báo giá cho khách.
                    </p>
                </div>

                <!-- Step 3: CONFIRMED -->
                <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-sm flex flex-col relative group hover:border-indigo-300 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-extrabold bg-indigo-100 text-indigo-800 border border-indigo-200 uppercase tracking-wide">
                            3. CONFIRMED (Đã xác nhận)
                        </span>
                        <span class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">
                            <i class="pi pi-check-circle text-xs"></i>
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 mb-1">Chốt vật liệu &amp; Chạy máy in</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Khách hàng xác nhận giá, chọn vật liệu (PLA, ABS, PETG, Resin...), đặt cọc đơn hàng. Xưởng xếp lịch máy in và tiến hành in 3D theo thông số cam kết.
                    </p>
                </div>

                <!-- Step 4: PROCESSED -->
                <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-sm flex flex-col relative group hover:border-emerald-300 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wide">
                            4. PROCESSED (Đã xử lý)
                        </span>
                        <span class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                            <i class="pi pi-box text-xs"></i>
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 mb-1">Hậu xử lý &amp; Bàn giao</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Bóc support, rửa siêu âm / sấy UV (nếu in Resin), chà nhám mịn, kiểm tra kích thước thành phẩm bằng thước kẹp và đóng gói gửi tới khách hàng.
                    </p>
                </div>
            </div>

            <!-- Auxiliary States -->
            <div class="bg-white/80 rounded-xl p-3.5 border border-slate-200 text-xs flex flex-wrap items-center justify-between gap-3 text-slate-600">
                <div class="flex items-center gap-2">
                    <i class="pi pi-info-circle text-blue-600"></i>
                    <span><strong>Hai trạng thái phụ kết thúc / lọc đơn:</strong></span>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-bold text-[10px]">CLOSE (Đóng)</span>
                        <span>Dùng khi khách hủy yêu cầu hoặc không phản hồi sau 3 ngày liên lạc.</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 font-bold text-[10px]">IS_SPAM (Spam)</span>
                        <span>Đánh dấu form rác/bot để loại bỏ khỏi thống kê hiệu suất KPI.</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Empty State When No Search Match -->
        <div
            v-if="!filteredModules.length"
            class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm mb-8"
        >
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-4">
                <i class="pi pi-search"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">Không tìm thấy hướng dẫn phù hợp</h3>
            <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">
                Không có module hoặc từ khóa nào khớp với <span class="font-bold text-slate-700">"{{ searchQuery }}"</span> trong tab đang chọn. Vui lòng thử từ khóa khác hoặc xóa bộ lọc.
            </p>
            <button
                @click="resetSearch"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-sm transition-all"
            >
                <i class="pi pi-refresh text-xs"></i>
                <span>Xóa bộ lọc &amp; Hiển thị tất cả</span>
            </button>
        </div>

        <!-- Module Cards Grid -->
        <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">
            <div
                v-for="item in filteredModules"
                :key="item.id"
                class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-7 flex flex-col hover:border-slate-300 hover:shadow-md transition-all relative overflow-hidden"
            >
                <!-- Card Header -->
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-11 h-11 rounded-xl flex items-center justify-center text-lg font-bold shadow-sm"
                            :class="item.iconBg"
                        >
                            <i :class="item.icon"></i>
                        </div>
                        <div>
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
                <div class="bg-slate-50/80 rounded-xl p-4 border border-slate-100 mb-4 flex-1">
                    <div class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                        <i class="pi pi-list-check text-xs text-blue-600"></i>
                        <span>Quy trình thao tác từng bước:</span>
                    </div>
                    <ol class="space-y-2 text-xs text-slate-700">
                        <li
                            v-for="(step, sIndex) in item.steps"
                            :key="sIndex"
                            class="flex items-start gap-2.5 leading-relaxed"
                        >
                            <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5">
                                {{ sIndex + 1 }}
                            </span>
                            <span>{{ step }}</span>
                        </li>
                    </ol>
                </div>

                <!-- Callout Note / Warning -->
                <div class="bg-amber-50/70 border border-amber-200/80 rounded-xl p-3 text-xs text-amber-900 flex items-start gap-2 mb-5">
                    <i class="pi pi-exclamation-triangle text-amber-600 text-xs shrink-0 mt-0.5"></i>
                    <div class="leading-relaxed">
                        <strong class="font-bold">Lưu ý:</strong> {{ item.tips }}
                    </div>
                </div>

                <!-- Card Footer Action -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-4 mt-auto">
                    <span class="text-[11px] font-medium text-slate-400">
                        Route: <code class="text-slate-600 font-mono text-[10px] bg-slate-100 px-1.5 py-0.5 rounded">{{ item.routeName }}</code>
                    </span>

                    <Link
                        v-if="canAccess(item)"
                        :href="getRouteUrl(item)"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-sm hover:shadow transition-all group"
                    >
                        <span>Mở trang quản lý</span>
                        <i class="pi pi-arrow-right text-[10px] group-hover:translate-x-0.5 transition-transform"></i>
                    </Link>

                    <div
                        v-else
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-slate-400 font-medium text-xs rounded-lg select-none"
                        title="Tài khoản hiện tại chưa được cấp quyền quản lý module này"
                    >
                        <i class="pi pi-lock text-[10px]"></i>
                        <span>Yêu cầu quyền truy cập</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reference Section: Bảng Quy chuẩn Media, Tệp CAD & Checklist Đa ngôn ngữ VI/EN -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-8 space-y-8">
            <div class="border-b border-slate-100 pb-5">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="pi pi-sliders-h text-xs"></i>
                    <span>Tài liệu tham chiếu kỹ thuật</span>
                </div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">
                    Quy chuẩn Hình ảnh, Tệp CAD In 3D &amp; Checklist Xuất bản
                </h2>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">
                    Bảng thông số kỹ thuật chuẩn giúp quản trị viên tối ưu tốc độ tải trang, đảm bảo chất lượng in 3D và chuẩn SEO toàn diện.
                </p>
            </div>

            <!-- Table 1: Media Standards -->
            <div>
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-2 h-4 bg-blue-600 rounded-full inline-block"></span>
                    1. Bảng Quy chuẩn Kích thước Hình ảnh Website
                </h3>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                                <th class="py-3 px-4">Vị trí hiển thị</th>
                                <th class="py-3 px-4">Kích thước chuẩn</th>
                                <th class="py-3 px-4">Tỉ lệ</th>
                                <th class="py-3 px-4">Dung lượng tối đa</th>
                                <th class="py-3 px-4">Định dạng ưu tiên</th>
                                <th class="py-3 px-4">Ghi chú thiết kế</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-bold text-slate-900">Hero Banner (Desktop)</td>
                                <td class="py-3 px-4 font-mono text-blue-600 font-bold">1920 × 800 px</td>
                                <td class="py-3 px-4">2.4 : 1</td>
                                <td class="py-3 px-4">&lt; 350 KB</td>
                                <td class="py-3 px-4 font-bold">WebP, PNG</td>
                                <td class="py-3 px-4 text-slate-500">Đặt nội dung/chữ chính trong vùng an toàn giữa 1200px</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-bold text-slate-900">Hero Banner (Mobile)</td>
                                <td class="py-3 px-4 font-mono text-blue-600 font-bold">768 × 600 px</td>
                                <td class="py-3 px-4">1.28 : 1</td>
                                <td class="py-3 px-4">&lt; 200 KB</td>
                                <td class="py-3 px-4 font-bold">WebP, JPG</td>
                                <td class="py-3 px-4 text-slate-500">Hiển thị sắc nét trên điện thoại, tránh chữ nhỏ sát mép</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-bold text-slate-900">Ảnh Sản phẩm In 3D</td>
                                <td class="py-3 px-4 font-mono text-blue-600 font-bold">800 × 800 px</td>
                                <td class="py-3 px-4">1 : 1 (Vuông)</td>
                                <td class="py-3 px-4">&lt; 250 KB</td>
                                <td class="py-3 px-4 font-bold">WebP, PNG</td>
                                <td class="py-3 px-4 text-slate-500">Nền trắng hoặc studio sạch, chụp rõ chi tiết lớp in</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-bold text-slate-900">Ảnh Dịch vụ &amp; Quy trình</td>
                                <td class="py-3 px-4 font-mono text-blue-600 font-bold">1200 × 800 px</td>
                                <td class="py-3 px-4">3 : 2</td>
                                <td class="py-3 px-4">&lt; 300 KB</td>
                                <td class="py-3 px-4 font-bold">WebP, JPG</td>
                                <td class="py-3 px-4 text-slate-500">Chụp thực tế máy in FDM/SLA vận hành tại xưởng</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-bold text-slate-900">Ảnh Bài viết &amp; Tin tức</td>
                                <td class="py-3 px-4 font-mono text-blue-600 font-bold">1200 × 675 px</td>
                                <td class="py-3 px-4">16 : 9</td>
                                <td class="py-3 px-4">&lt; 300 KB</td>
                                <td class="py-3 px-4 font-bold">WebP, JPG</td>
                                <td class="py-3 px-4 text-slate-500">Ảnh bìa bài viết chuẩn chuẩn hiển thị feed mạng xã hội</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-bold text-slate-900">Ảnh Chia sẻ Mạng Xã hội (OG Image)</td>
                                <td class="py-3 px-4 font-mono text-blue-600 font-bold">1200 × 630 px</td>
                                <td class="py-3 px-4">1.91 : 1</td>
                                <td class="py-3 px-4">&lt; 300 KB</td>
                                <td class="py-3 px-4 font-bold">WebP, JPG</td>
                                <td class="py-3 px-4 text-slate-500">Hiển thị khi gửi link website qua Zalo, Facebook, LinkedIn</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table 2: 3D CAD File Standards -->
            <div>
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-2 h-4 bg-emerald-600 rounded-full inline-block"></span>
                    2. Định dạng Tệp CAD In 3D &amp; Thiết kế Hợp Lệ
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-mono font-bold text-xs">.STL</span>
                            <span class="text-[11px] font-bold text-slate-400">Phổ biến nhất</span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 mb-1">Standard Triangle Language</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Định dạng lưới tam giác 3D tiêu chuẩn hàng đầu cho in FDM và SLA. Mở trực tiếp bằng mọi phần mềm cắt lớp (Slicer).
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-mono font-bold text-xs">.STEP / .STP</span>
                            <span class="text-[11px] font-bold text-slate-400">Chuẩn kỹ thuật</span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 mb-1">Standard for Product Model Data</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            File CAD gốc hình học tham số tiêu chuẩn ISO từ SolidWorks, Fusion 360, Inventor, Catia. Cho phép chỉnh sửa kích thước chính xác.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-800 font-mono font-bold text-xs">.OBJ</span>
                            <span class="text-[11px] font-bold text-slate-400">Mô hình &amp; Màu</span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 mb-1">Wavefront 3D Object</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Thích hợp cho mô hình nhân vật, tượng game, nghệ thuật điêu khắc số từ ZBrush hoặc Blender kèm thông tin texture/màu.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-mono font-bold text-xs">.IGES / .IGS</span>
                            <span class="text-[11px] font-bold text-slate-400">Cơ khí chính xác</span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800 mb-1">Initial Graphics Exchange</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Định dạng trao đổi bề mặt cong cơ khí chuẩn công nghiệp, tương thích với hầu hết các phần mềm CAD/CAM truyền thống.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 sm:col-span-2">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-800 font-mono font-bold text-xs">.ZIP / .RAR</span>
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
            <div>
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-2 h-4 bg-indigo-600 rounded-full inline-block"></span>
                    3. Checklist 5 Bước Trước Khi Xuất Bản Dữ Liệu Song Ngữ &amp; SEO
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-white flex flex-col justify-between">
                        <div>
                            <span class="w-6 h-6 rounded-md bg-blue-100 text-blue-700 font-extrabold text-xs flex items-center justify-center mb-2">1</span>
                            <h4 class="text-xs font-bold text-slate-800 mb-1">Đủ 2 ngôn ngữ</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Kiểm tra cả 2 tab <span class="font-bold">VI</span> và <span class="font-bold">EN</span> (Tiêu đề, mô tả, nội dung chi tiết) không bị bỏ trống.
                            </p>
                        </div>
                        <div class="text-[10px] font-bold text-emerald-600 mt-2 flex items-center gap-1">
                            <i class="pi pi-check text-[10px]"></i> Đa ngôn ngữ
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-slate-200 bg-white flex flex-col justify-between">
                        <div>
                            <span class="w-6 h-6 rounded-md bg-blue-100 text-blue-700 font-extrabold text-xs flex items-center justify-center mb-2">2</span>
                            <h4 class="text-xs font-bold text-slate-800 mb-1">Tối ưu thẻ SEO</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Điền Meta Title (dưới 65 ký tự) và Meta Description (140-160 ký tự) có chứa từ khóa in 3D chính.
                            </p>
                        </div>
                        <div class="text-[10px] font-bold text-emerald-600 mt-2 flex items-center gap-1">
                            <i class="pi pi-check text-[10px]"></i> Chuẩn Onpage
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-slate-200 bg-white flex flex-col justify-between">
                        <div>
                            <span class="w-6 h-6 rounded-md bg-blue-100 text-blue-700 font-extrabold text-xs flex items-center justify-center mb-2">3</span>
                            <h4 class="text-xs font-bold text-slate-800 mb-1">Kiểm tra Slug</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Đảm bảo đường dẫn tĩnh không dấu, ngăn cách bằng gạch nối <code class="text-slate-700">-</code>, không chứa ký tự lạ.
                            </p>
                        </div>
                        <div class="text-[10px] font-bold text-emerald-600 mt-2 flex items-center gap-1">
                            <i class="pi pi-check text-[10px]"></i> Đường dẫn sạch
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-slate-200 bg-white flex flex-col justify-between">
                        <div>
                            <span class="w-6 h-6 rounded-md bg-blue-100 text-blue-700 font-extrabold text-xs flex items-center justify-center mb-2">4</span>
                            <h4 class="text-xs font-bold text-slate-800 mb-1">Nén ảnh &amp; Alt</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Đảm bảo dung lượng ảnh dưới 300KB định dạng WebP và có đặt tên file chứa từ khóa mô tả sản phẩm.
                            </p>
                        </div>
                        <div class="text-[10px] font-bold text-emerald-600 mt-2 flex items-center gap-1">
                            <i class="pi pi-check text-[10px]"></i> Tốc độ cao
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-slate-200 bg-white flex flex-col justify-between">
                        <div>
                            <span class="w-6 h-6 rounded-md bg-blue-100 text-blue-700 font-extrabold text-xs flex items-center justify-center mb-2">5</span>
                            <h4 class="text-xs font-bold text-slate-800 mb-1">Xóa Cache</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Sau khi sửa Cài đặt chung hoặc Banner, làm mới cache hệ thống và mở trình duyệt ẩn danh để nghiệm thu.
                            </p>
                        </div>
                        <div class="text-[10px] font-bold text-emerald-600 mt-2 flex items-center gap-1">
                            <i class="pi pi-check text-[10px]"></i> Đồng bộ ngay
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Footer -->
        <div class="text-center text-xs text-slate-400 py-4">
            HH Maker 3D CMS • Hotline Hỗ trợ Kỹ thuật Nội bộ &amp; Xưởng: <span class="font-semibold text-slate-600">Xem mục Cài đặt chung</span>
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
                { id: 'FACTORY', label: '2. Vận hành Xưởng In 3D', icon: 'pi pi-box' },
                { id: 'CONTENT', label: '3. Nội dung & Thương hiệu', icon: 'pi pi-images' },
                { id: 'SYSTEM', label: '4. Hệ thống & SEO', icon: 'pi pi-cog' },
            ],
            allModules: [
                // 1. DASHBOARD
                {
                    id: 'dashboard',
                    category: 'DASHBOARD',
                    categoryName: '1. Bảng điều khiển',
                    categoryBadge: 'bg-blue-100 text-blue-700',
                    icon: 'pi pi-chart-bar',
                    iconBg: 'bg-blue-50 text-blue-600',
                    title: 'Bảng điều khiển & Thống kê RFQ',
                    summary: 'Theo dõi tổng quan số lượng báo giá in 3D phát sinh trong ngày, dự báo tháng, tốc độ tăng trưởng MoM và cơ cấu các nhóm dịch vụ.',
                    steps: [
                        'Xem 4 thẻ chỉ số KPI ở đầu trang: Báo giá hôm nay, Dự báo báo giá tháng, Tăng trưởng MoM (%) và Tỉ lệ chuyển đổi đơn hàng.',
                        'Lựa chọn năm tại bộ lọc Năm để tra cứu biểu đồ xu hướng số lượng yêu cầu báo giá theo từng tháng.',
                        'Phân tích cơ cấu 4 nhóm dịch vụ in 3D (Gia công in 3D, Thiết kế 3D & CAD, Làm đồ custom & Quà tặng, Tư vấn kỹ thuật khác) tại biểu đồ tròn.',
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
                    categoryName: '2. Vận hành Xưởng In 3D',
                    categoryBadge: 'bg-emerald-100 text-emerald-700',
                    icon: 'pi pi-send',
                    iconBg: 'bg-emerald-50 text-emerald-600',
                    title: 'Báo giá & Yêu cầu In 3D (RFQ)',
                    summary: 'Quản lý luồng tiếp nhận yêu cầu từ form khách hàng, tải file CAD (.STL, .STEP...), tính toán chi phí và cập nhật trạng thái đơn.',
                    steps: [
                        'Vào danh sách yêu cầu, lọc theo trạng thái "Mới (NEW)" để ưu tiên xử lý các yêu cầu vừa gửi đến.',
                        'Mở chi tiết yêu cầu để xem thông tin liên hệ, vật liệu mong muốn (PLA, ABS, PETG, Resin...), số lượng và ghi chú của khách.',
                        'Tải tệp CAD đính kèm (.STL, .STEP, .OBJ, .ZIP) về máy tính để kiểm tra độ dày thành, lưới polygon và ước tính thời gian in.',
                        'Liên hệ khách hàng qua Điện thoại/Zalo để tư vấn và báo giá chi tiết, sau đó chuyển trạng thái sang "Đã phản hồi (RESPONSE)".',
                        'Khi khách hàng chốt in và đặt cọc, chuyển sang "Đã xác nhận (CONFIRMED)". Sau khi xưởng in xong và bàn giao chuyển sang "Đã xử lý (PROCESSED)".',
                    ],
                    tips: 'Nếu yêu cầu là quảng cáo hoặc bot tự động, hãy chọn trạng thái "Spam (IS_SPAM)" để không tính vào báo cáo KPI tháng.',
                    permission: 'admin.contacts.index',
                    routeName: 'admin.contacts.index',
                    keywords: ['báo giá', 'cad', 'stl', 'step', 'obj', 'rfq', 'contact', 'yêu cầu', 'new', 'response', 'confirmed', 'processed', 'close', 'spam'],
                },

                // 2. FACTORY - Products
                {
                    id: 'products',
                    category: 'FACTORY',
                    categoryName: '2. Vận hành Xưởng In 3D',
                    categoryBadge: 'bg-emerald-100 text-emerald-700',
                    icon: 'pi pi-box',
                    iconBg: 'bg-emerald-50 text-emerald-600',
                    title: 'Quản lý Sản phẩm In 3D',
                    summary: 'Đăng tải và quản lý các sản phẩm mẫu, linh kiện in sẵn, mô hình decor với đầy đủ thông số kỹ thuật và hình ảnh song ngữ.',
                    steps: [
                        'Nhấn "Thêm mới" hoặc chọn sản phẩm cần cập nhật từ danh sách.',
                        'Điền Tiêu đề và Mô tả sản phẩm ở cả 2 tab Tiếng Việt (VI) và English (EN).',
                        'Chọn Danh mục sản phẩm tương ứng và nhập Giá bán (hoặc để liên hệ báo giá).',
                        'Tải lên Ảnh đại diện chính (tỉ lệ 1:1 vuông, kích thước chuẩn 800x800px) và Thư viện ảnh chi tiết các góc chụp thực tế.',
                        'Điền Thông số kỹ thuật (Vật liệu, Kích thước tối đa, Độ chính xác lớp in, Trọng lượng) và kiểm tra thẻ Meta SEO trước khi bật "Kích hoạt (ACTIVE)".',
                    ],
                    tips: 'Để sản phẩm hiển thị lên đầu trang chủ hoặc trang danh mục, hãy chỉnh số thứ tự hiển thị (Position) nhỏ hơn (ví dụ: 1, 2, 3).',
                    permission: 'admin.products.index',
                    routeName: 'admin.products.index',
                    keywords: ['sản phẩm', 'products', 'mô hình', 'linh kiện', 'giá bán', 'active', 'song ngữ', 'tiếng anh'],
                },

                // 2. FACTORY - Product Categories
                {
                    id: 'product-categories',
                    category: 'FACTORY',
                    categoryName: '2. Vận hành Xưởng In 3D',
                    categoryBadge: 'bg-emerald-100 text-emerald-700',
                    icon: 'pi pi-tags',
                    iconBg: 'bg-emerald-50 text-emerald-600',
                    title: 'Danh mục Sản phẩm',
                    summary: 'Phân cấp các nhóm sản phẩm in 3D theo ứng dụng (Cơ khí, Kiến trúc, Y tế, Decor, Quà tặng cá nhân hóa).',
                    steps: [
                        'Tạo mới danh mục với Tên song ngữ VI/EN và kiểm tra đường dẫn tĩnh (Slug) tự động sinh ra.',
                        'Chọn Danh mục cha nếu là danh mục cấp 2, hoặc để trống nếu là danh mục gốc.',
                        'Tải lên Ảnh đại diện danh mục (kích thước chuẩn 600x400px hoặc 800x800px).',
                        'Thiết lập thứ tự sắp xếp và điền thông tin Meta Title, Meta Description chuẩn SEO.',
                    ],
                    tips: 'Không nên xóa danh mục đang có sản phẩm liên kết; hãy chuyển sản phẩm sang danh mục khác trước khi xóa.',
                    permission: 'admin.product-categories.index',
                    routeName: 'admin.product-categories.index',
                    keywords: ['danh mục', 'categories', 'phân loại sản phẩm', 'slug', 'cơ khí', 'decor'],
                },

                // 2. FACTORY - Services
                {
                    id: 'services',
                    category: 'FACTORY',
                    categoryName: '2. Vận hành Xưởng In 3D',
                    categoryBadge: 'bg-emerald-100 text-emerald-700',
                    icon: 'pi pi-cog',
                    iconBg: 'bg-emerald-50 text-emerald-600',
                    title: 'Dịch vụ In 3D & Thiết kế CAD',
                    summary: 'Biên tập trang dịch vụ chuyên sâu với các khối JSON động: Hero, Bảng thông số kỹ thuật, Quy trình 5 bước, Ưu điểm, Bảng giá và FAQ.',
                    steps: [
                        'Chọn dịch vụ cần chỉnh sửa (ví dụ: In 3D FDM, In 3D SLA Resin, Thiết kế 3D & Reverse Engineering...).',
                        'Cập nhật Tiêu đề, Tóm tắt và Nội dung chi tiết song ngữ VI/EN.',
                        'Cấu hình khối "Bảng thông số kỹ thuật": nhập các thông số độ chính xác (±0.1mm), vật liệu (PLA, ABS, Carbon, Resin...), kích thước bàn in.',
                        'Kiểm tra khối "Quy trình 5 bước": Tải file -> Báo giá -> Duyệt mẫu -> In 3D -> Giao hàng.',
                        'Cập nhật khối "Câu hỏi thường gặp (FAQ)" đặc thù của từng công nghệ in và nhấn "Lưu thay đổi".',
                    ],
                    tips: 'Khối dữ liệu JSON động của trang dịch vụ quyết định giao diện hiển thị ngoài Frontend; kiểm tra cẩn thận cả 2 tab ngôn ngữ trước khi lưu.',
                    permission: 'admin.services.index',
                    routeName: 'admin.services.index',
                    keywords: ['dịch vụ', 'services', 'fdm', 'sla', 'sls', 'resin', 'thiết kế cad', 'quy trình', 'thông số', 'faq'],
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
                        'Điền Tiêu đề lớn (Headline), Mô tả ngắn và Nhãn nút bấm kèm đường dẫn liên kết (ví dụ: dẫn tới trang /dich-vu-in-3d hoặc nút Báo giá).',
                        'Với HOME_FLIP_ANNOUNCEMENT: Nhập nội dung thông điệp lật (ví dụ: "Nhận báo giá in 3D nhanh trong 15 phút", "Giao hàng toàn quốc").',
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
                    title: 'Bài viết & Tin tức Công nghệ 3D',
                    summary: 'Xuất bản các bài viết chia sẻ kiến thức công nghệ in 3D, hướng dẫn chọn vật liệu, case study dự án cơ khí và tin tức xưởng.',
                    steps: [
                        'Nhấn "Thêm bài viết mới" và nhập Tiêu đề hấp dẫn, chuẩn từ khóa SEO.',
                        'Soạn thảo nội dung bằng trình biên tập Rich Text (hỗ trợ chèn tiêu đề H2, H3, bảng so sánh và ảnh minh họa).',
                        'Tải lên Ảnh đại diện bài viết (tỉ lệ 16:9, kích thước chuẩn 1200x675px).',
                        'Chọn Danh mục bài viết tương ứng và gắn các thẻ Tag liên quan (FDM, SLA, Vật liệu PLA, Thiết kế CAD...).',
                        'Điền Meta Title, Meta Description, kiểm tra đường dẫn Slug không dấu và chuyển sang trạng thái "Xuất bản (PUBLISHED)".',
                    ],
                    tips: 'Chèn liên kết nội bộ (Internal Link) trỏ về trang Dịch vụ in 3D hoặc form Báo giá trong thân bài viết để tăng tỉ lệ chuyển đổi khách hàng.',
                    permission: 'admin.posts.index',
                    routeName: 'admin.posts.index',
                    keywords: ['bài viết', 'posts', 'tin tức', 'blog', 'kiến thức in 3d', 'seo', 'slug', 'rich text'],
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
                    summary: 'Phân loại các chủ đề bài viết như: Kiến thức In 3D, Vật liệu & Kỹ thuật, Dự án Tiêu biểu, Tin tức HH Maker.',
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
                    summary: 'Tùy biến nội dung email gửi tự động cho khách khi gửi yêu cầu báo giá, xác nhận đơn hàng hoặc cập nhật tiến độ in.',
                    steps: [
                        'Chọn mẫu email cần chỉnh sửa (ví dụ: Xác nhận đã nhận yêu cầu báo giá, Thông báo đơn in 3D hoàn tất).',
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
                    tips: 'Trước khi tải ảnh lên, hãy đổi tên file tiếng Việt không dấu (ví dụ: mo-hinh-in-3d-sla-nhua-trong.jpg) để chuẩn SEO hình ảnh.',
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
                    summary: 'Cấu hình số điện thoại Hotline, số Zalo nhận file CAD nhanh, địa chỉ xưởng in, email liên hệ và mã nhúng script.',
                    steps: [
                        'Truy cập trang Cài đặt chung (/admin/settings/form/general).',
                        'Cập nhật Số điện thoại Hotline chính và Số Zalo kỹ thuật (dùng để khách chat gửi file CAD dung lượng lớn).',
                        'Cập nhật Địa chỉ xưởng in 3D, Thời gian mở cửa nhận file và Email nhận RFQ.',
                        'Kiểm tra các liên kết Mạng xã hội: Facebook Fanpage, Kênh TikTok/YouTube mô hình 3D.',
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
                    summary: 'Quản lý danh sách nhân sự vận hành xưởng, kỹ thuật viên CAD và biên tập viên nội dung có quyền đăng nhập CMS.',
                    steps: [
                        'Nhấn "Thêm quản trị viên mới" và nhập Họ tên, Email công việc và Mật khẩu khởi tạo an toàn.',
                        'Gán Vai trò phù hợp cho nhân viên (ví dụ: Kỹ thuật viên xưởng, Biên tập viên nội dung hoặc Super Admin).',
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
                    summary: 'Thiết lập ma trận quyền hạn chi tiết (Xem, Thêm, Sửa, Xóa) cho từng nhóm vị trí công việc trong xưởng in 3D.',
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
