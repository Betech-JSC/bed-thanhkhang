<template layout>
    <Form v-model="formData">
        <template #default="{ form }">
            <!-- 1. Thông tin chung -->
            <div class="card">
                <div class="card-header font-bold text-slate-800">
                    {{ tt('models.setting.general_information') }}
                </div>
                <div class="card-body space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field
                            v-model="form.title"
                            :field="{
                                type: 'text',
                                name: 'title',
                                label: 'Tên sản phẩm',
                                placeholder: 'VD: Xích thang cuốn bước lớn TL133',
                            }"
                        />
                        <Field
                            v-model="form.slug"
                            :field="{
                                type: 'text',
                                name: 'slug',
                                label: 'Slug đường dẫn',
                                placeholder: 'VD: xich-thang-cuon-buoc-lon-tl133',
                            }"
                        />
                    </div>
                    <small v-if="form.id && form.url" class="block text-xs text-slate-500">
                        <span v-for="(item, locale) in form.url" :key="locale" class="block">
                            {{ locale }}:
                            <a :href="item" target="_blank" class="link">{{ decodeURI(item) }}</a>
                        </span>
                    </small>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field
                            v-model="form.sku"
                            :field="{
                                type: 'text',
                                name: 'sku',
                                label: 'Mã SKU / Mã sản phẩm',
                                placeholder: 'VD: TK-TC-TL133',
                            }"
                        />
                        <Field
                            v-model="form.categories"
                            :field="{
                                type: 'select_multiple',
                                name: 'categories',
                                label: 'Danh mục sản phẩm',
                                labelBy: 'title',
                                source: {
                                    model: 'App\\Models\\Product\\ProductCategory',
                                    method: 'getCategories',
                                    only: ['id', 'title'],
                                },
                            }"
                        />
                    </div>
                </div>
            </div>

            <!-- 2. Thông số kỹ thuật & Đặc tính cơ khí (B2B Specifications) -->
            <div class="card">
                <div class="card-header font-bold text-slate-800 flex items-center justify-between">
                    <span>Thông số kỹ thuật &amp; Đặc tính cơ khí</span>
                    <span class="text-xs text-[#005EA4] font-semibold">Tiêu chuẩn kỹ thuật Thành Khang</span>
                </div>
                <div class="card-body space-y-4">
                    <!-- Dòng 1, 2 & Lead time -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <Field
                            v-model="form.print_technology"
                            :field="{
                                type: 'select_single',
                                name: 'print_technology',
                                label: '1. Tiêu chuẩn / Công nghệ chế tạo',
                                options: printTechnologyOptions,
                            }"
                        />
                        <Field
                            v-model="form.material"
                            :field="{
                                type: 'select_single',
                                name: 'material',
                                label: '2. Vật liệu / Cấu tạo cơ khí',
                                options: materialOptions,
                            }"
                        />
                        <Field
                            v-model="form.lead_time"
                            :field="{
                                type: 'text',
                                name: 'lead_time',
                                label: 'Thời gian cấp hàng (Lead time)',
                                placeholder: 'VD: Sẵn kho / Đặt hàng 3-7 ngày làm việc',
                            }"
                        />
                    </div>

                    <!-- Dòng 3, 4, 5 của bảng Thông số kỹ thuật -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <Field
                            v-model="form.sample_size"
                            :field="{
                                type: 'text',
                                name: 'sample_size',
                                label: '3. Kích thước / Bước xích / Đường kính',
                                placeholder: 'VD: Bước xích p=100mm, Bản rộng B=400mm',
                            }"
                        />
                        <Field
                            v-model="form.layer_height"
                            :field="{
                                type: 'text',
                                name: 'layer_height',
                                label: '4. Tải trọng làm việc / Giới hạn đứt',
                                placeholder: 'VD: Tải trọng phá hủy tối thiểu 120 kN',
                            }"
                        />
                        <Field
                            v-model="form.nozzle_size"
                            :field="{
                                type: 'text',
                                name: 'nozzle_size',
                                label: '5. Xử lý bề mặt / Bôi trơn',
                                placeholder: 'VD: Mạ kẽm / Thấm Nitơ / Tra mỡ chịu nhiệt',
                            }"
                        />
                    </div>

                    <!-- Dòng 6, 7 & Tiêu chuẩn -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field
                            v-model="form.infill"
                            :field="{
                                type: 'text',
                                name: 'infill',
                                label: '6. Tiêu chuẩn áp dụng / Độ cứng',
                                placeholder: 'VD: ISO 606 / DIN 8187 / JIS B1801',
                            }"
                        />
                        <Field
                            v-model="form.support_material"
                            :field="{
                                type: 'text',
                                name: 'support_material',
                                label: '7. Chứng chỉ / Kiểm định',
                                placeholder: 'VD: CO/CQ, Test report nhà máy',
                            }"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                        <Field
                            v-model="form.image_color_label"
                            :field="{
                                type: 'text',
                                name: 'image_color_label',
                                label: 'Màu trong ảnh (Hiển thị trên PDP)',
                                placeholder: 'VD: Cam',
                            }"
                        />
                        <Field
                            v-model="form.customization_label"
                            :field="{
                                type: 'text',
                                name: 'customization_label',
                                label: 'Có thể tùy chỉnh (Hiển thị trên PDP)',
                                placeholder: 'VD: Màu sắc · Kích thước · Số lượng',
                            }"
                        />
                    </div>

                    <!-- Bảng thông số kỹ thuật tùy biến (specifications JSON) -->
                    <div class="pt-4 mt-2 border-t border-slate-200 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <h4 class="text-sm font-semibold text-slate-800">
                                    Bảng thông số kỹ thuật chi tiết (Specifications)
                                </h4>
                                <p class="text-xs text-slate-500">
                                    Thêm từng cặp tên thông số và giá trị hiển thị ở bảng Thông số kỹ thuật trên PDP.
                                </p>
                            </div>
                            <button
                                type="button"
                                @click="addSpecificationRow(form)"
                                class="px-3 py-1.5 text-xs font-medium rounded bg-[#005EA4] hover:bg-[#004D86] text-white transition-colors"
                            >
                                + Thêm dòng thông số
                            </button>
                        </div>

                        <div
                            v-if="Array.isArray(form.specifications) && form.specifications.length > 0"
                            class="space-y-2"
                        >
                            <div
                                v-for="(spec, sIdx) in form.specifications"
                                :key="sIdx"
                                class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center bg-slate-50 p-2.5 rounded border border-slate-200"
                            >
                                <div class="md:col-span-5">
                                    <input
                                        v-model="spec.label"
                                        type="text"
                                        class="w-full rounded border border-slate-300 px-3 py-1.5 text-sm bg-white focus:border-[#005EA4] focus:outline-none"
                                        placeholder="Tên thông số (VD: Bước xích)"
                                    />
                                </div>
                                <div class="md:col-span-6">
                                    <input
                                        v-model="spec.value"
                                        type="text"
                                        class="w-full rounded border border-slate-300 px-3 py-1.5 text-sm bg-white focus:border-[#005EA4] focus:outline-none"
                                        placeholder="Giá trị (VD: 133.33 mm)"
                                    />
                                </div>
                                <div class="md:col-span-1 flex justify-end">
                                    <button
                                        type="button"
                                        @click="removeSpecificationRow(form, sIdx)"
                                        class="px-2.5 py-1.5 text-xs font-medium rounded text-red-600 hover:bg-red-50 border border-red-200 transition-colors"
                                        title="Xóa dòng"
                                    >
                                        Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-xs italic text-slate-400">
                            Chưa có dòng thông số tùy chỉnh nào.
                        </p>
                    </div>
                </div>
            </div>

            <!-- 3. Mô tả & Nội dung PDP -->
            <div class="card">
                <div class="card-header font-bold text-slate-800">Mô tả &amp; Nội dung chi tiết sản phẩm (PDP)</div>
                <div class="card-body space-y-4">
                    <Field
                        v-model="form.description"
                        :field="{
                            type: 'textarea',
                            name: 'description',
                            label: 'Mô tả ngắn (Hiển thị đầu trang chi tiết)',
                            rows: 3,
                            placeholder: 'Tóm tắt đặc điểm nổi bật, ứng dụng của sản phẩm...',
                        }"
                    />

                    <!-- [UI-04] Badges đầu trang PDP (pdp_metadata.badges) -->
                    <div class="pt-4 mt-2 border-t border-slate-100 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <h4 class="text-sm font-semibold text-slate-800">
                                    Nhãn nổi bật đầu trang sản phẩm (Badges)
                                </h4>
                                <p class="text-xs text-slate-500">
                                    Hiển thị ngay dưới tên sản phẩm trên trang chi tiết (VD: Chuẩn OEM / ISO, Cung ứng theo thông số). Để trống để ẩn.
                                </p>
                            </div>
                            <button
                                type="button"
                                @click="addBadgeRow(form)"
                                class="px-3 py-1.5 text-xs font-medium rounded bg-[#005EA4] hover:bg-[#004D86] text-white transition-colors"
                            >
                                + Thêm nhãn
                            </button>
                        </div>

                        <div
                            v-if="Array.isArray(form.pdp_metadata?.badges) && form.pdp_metadata.badges.length > 0"
                            class="space-y-2"
                        >
                            <div
                                v-for="(badge, bIdx) in form.pdp_metadata.badges"
                                :key="bIdx"
                                class="flex items-center gap-2 bg-slate-50 p-2.5 rounded border border-slate-200"
                            >
                                <input
                                    v-model="form.pdp_metadata.badges[bIdx]"
                                    type="text"
                                    class="flex-1 rounded border border-slate-300 px-3 py-1.5 text-sm bg-white focus:border-[#005EA4] focus:outline-none"
                                    placeholder="VD: Chuẩn OEM / ISO"
                                />
                                <button
                                    type="button"
                                    @click="removeBadgeRow(form, bIdx)"
                                    class="px-2.5 py-1.5 text-xs font-medium rounded text-red-600 hover:bg-red-50 border border-red-200 transition-colors shrink-0"
                                    title="Xóa nhãn"
                                >
                                    Xóa
                                </button>
                            </div>
                        </div>
                        <p v-else class="text-xs italic text-slate-400">
                            Chưa có nhãn nổi bật nào (khối badges trên PDP sẽ tự ẩn).
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                        <Field
                            v-model="form.pdp_metadata.story_title"
                            :field="{
                                type: 'text',
                                name: 'story_title',
                                label: 'Tiêu đề câu chuyện sản phẩm (Tab Tổng quan)',
                                placeholder: 'VD: Độ bền chuẩn công nghiệp cho hệ thống vận hành liên tục.',
                            }"
                        />
                        <Field
                            v-model="form.pdp_metadata.story_intro"
                            :field="{
                                type: 'textarea',
                                name: 'story_intro',
                                label: 'Đoạn mở đầu câu chuyện (Tab Tổng quan)',
                                rows: 2,
                                placeholder: 'Mô tả đặc tính cơ khí, quy trình nhiệt luyện và ứng dụng công nghiệp của sản phẩm...',
                            }"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field
                            v-model="form.pdp_metadata.feature1_title"
                            :field="{
                                type: 'text',
                                name: 'feature1_title',
                                label: 'Tiêu đề điểm nổi bật 1',
                                placeholder: 'VD: Chịu tải cao & chống mài mòn vượt trội',
                            }"
                        />
                        <Field
                            v-model="form.pdp_metadata.feature1_desc"
                            :field="{
                                type: 'textarea',
                                name: 'feature1_desc',
                                label: 'Nội dung điểm nổi bật 1',
                                rows: 2,
                            }"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field
                            v-model="form.pdp_metadata.feature2_title"
                            :field="{
                                type: 'text',
                                name: 'feature2_title',
                                label: 'Tiêu đề điểm nổi bật 2',
                                placeholder: 'VD: Tùy chỉnh theo yêu cầu',
                            }"
                        />
                        <Field
                            v-model="form.pdp_metadata.feature2_desc"
                            :field="{
                                type: 'textarea',
                                name: 'feature2_desc',
                                label: 'Nội dung điểm nổi bật 2',
                                rows: 2,
                            }"
                        />
                    </div>

                    <Field
                        v-model="form.content"
                        :field="{
                            type: 'richtext',
                            name: 'content',
                            label: 'Bài viết chi tiết sản phẩm (Bổ sung)',
                        }"
                    />

                    <!-- Thông số PDP tùy chọn (ghi đè Settings mặc định) -->
                    <div class="pt-4 mt-2 border-t border-slate-200 space-y-3">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">
                                Thông số PDP tùy chọn (PDP Metadata)
                            </h4>
                            <p class="text-xs text-slate-500">
                                Tùy chọn ghi đè cho hộp 6 thông số nhanh trên Trang Chi tiết Sản phẩm. Để trống để dùng mặc định từ Cài đặt chung.
                            </p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <Field
                                v-model="form.pdp_metadata.brand_name"
                                :field="{
                                    type: 'text',
                                    name: 'pdp_brand_name',
                                    label: 'Thương hiệu riêng',
                                    placeholder: 'Để trống dùng mặc định Settings',
                                }"
                            />
                            <Field
                                v-model="form.pdp_metadata.origin"
                                :field="{
                                    type: 'text',
                                    name: 'pdp_origin',
                                    label: 'Xuất xứ',
                                    placeholder: 'Để trống dùng mặc định Settings',
                                }"
                            />
                            <Field
                                v-model="form.pdp_metadata.warranty"
                                :field="{
                                    type: 'text',
                                    name: 'pdp_warranty',
                                    label: 'Bảo hành',
                                    placeholder: 'Để trống dùng mặc định Settings',
                                }"
                            />
                            <Field
                                v-model="form.pdp_metadata.condition"
                                :field="{
                                    type: 'text',
                                    name: 'pdp_condition',
                                    label: 'Tình trạng',
                                    placeholder: 'Để trống dùng mặc định Settings',
                                }"
                            />
                            <Field
                                v-model="form.pdp_metadata.stock_status"
                                :field="{
                                    type: 'text',
                                    name: 'pdp_stock_status',
                                    label: 'Trạng thái kho',
                                    placeholder: 'Để trống dùng mặc định Settings',
                                }"
                            />
                            <Field
                                v-model="form.pdp_metadata.technical_support"
                                :field="{
                                    type: 'text',
                                    name: 'pdp_technical_support',
                                    label: 'Hỗ trợ kỹ thuật',
                                    placeholder: 'Để trống dùng mặc định Settings',
                                }"
                            />
                        </div>
                    </div>

                    <!-- [UI-01] Tab 2: Thông số kỹ thuật mở rộng (pdp_metadata.specs_*) -->
                    <div class="pt-4 mt-2 border-t border-slate-200 space-y-4">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">
                                Tab 2: Thông số kỹ thuật mở rộng (PDP)
                            </h4>
                            <p class="text-xs text-slate-500">
                                Nội dung hiển thị trong Tab &quot;Thông số kỹ thuật&quot; trên trang chi tiết sản phẩm. Tab tự động ẩn nếu toàn bộ các trường đều trống.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.pdp_metadata.specs_title"
                                :field="{
                                    type: 'text',
                                    name: 'pdp_specs_title',
                                    label: 'Tiêu đề Tab Thông số kỹ thuật',
                                    placeholder: 'VD: Tiêu chuẩn kỹ thuật & Kiểm định cơ khí',
                                }"
                            />
                            <Field
                                v-model="form.pdp_metadata.specs_desc"
                                :field="{
                                    type: 'textarea',
                                    name: 'pdp_specs_desc',
                                    label: 'Mô tả ngắn Tab Thông số kỹ thuật',
                                    rows: 2,
                                    placeholder: 'Mô tả quy chuẩn kiểm định, dung sai và độ bền vận hành...',
                                }"
                            />
                        </div>

                        <Field
                            v-model="form.pdp_metadata.specs_content"
                            :field="{
                                type: 'richtext',
                                name: 'pdp_specs_content',
                                size: 'sm',
                                label: 'Nội dung chi tiết Tab Thông số kỹ thuật',
                            }"
                        />

                        <!-- Repeater specs_cards -->
                        <div class="space-y-3 pt-2">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <h5 class="text-xs font-semibold uppercase tracking-wide text-slate-700">
                                        Thẻ thông số nổi bật (Specs Cards)
                                    </h5>
                                    <p class="text-xs text-slate-500">
                                        Hiển thị dạng lưới thẻ 3 cột trong Tab Thông số kỹ thuật (Nhãn, Giá trị, Mô tả).
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="addSpecsCardRow(form)"
                                    class="px-3 py-1.5 text-xs font-medium rounded bg-[#005EA4] hover:bg-[#004D86] text-white transition-colors"
                                >
                                    + Thêm thẻ thông số
                                </button>
                            </div>

                            <div
                                v-if="Array.isArray(form.pdp_metadata?.specs_cards) && form.pdp_metadata.specs_cards.length > 0"
                                class="space-y-2"
                            >
                                <div
                                    v-for="(card, cIdx) in form.pdp_metadata.specs_cards"
                                    :key="cIdx"
                                    class="bg-slate-50 p-3 rounded border border-slate-200 space-y-2"
                                >
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                                        <div class="md:col-span-5">
                                            <input
                                                v-model="card.label"
                                                type="text"
                                                class="w-full rounded border border-slate-300 px-3 py-1.5 text-sm bg-white focus:border-[#005EA4] focus:outline-none"
                                                placeholder="Nhãn (VD: Độ chính xác)"
                                            />
                                        </div>
                                        <div class="md:col-span-6">
                                            <input
                                                v-model="card.value"
                                                type="text"
                                                class="w-full rounded border border-slate-300 px-3 py-1.5 text-sm bg-white focus:border-[#005EA4] focus:outline-none"
                                                placeholder="Giá trị (VD: ±0.05 mm)"
                                            />
                                        </div>
                                        <div class="md:col-span-1 flex justify-end">
                                            <button
                                                type="button"
                                                @click="removeSpecsCardRow(form, cIdx)"
                                                class="px-2.5 py-1.5 text-xs font-medium rounded text-red-600 hover:bg-red-50 border border-red-200 transition-colors"
                                                title="Xóa thẻ"
                                            >
                                                Xóa
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <textarea
                                            v-model="card.description"
                                            rows="2"
                                            class="w-full rounded border border-slate-300 px-3 py-1.5 text-sm bg-white focus:border-[#005EA4] focus:outline-none"
                                            placeholder="Mô tả chi tiết thẻ thông số..."
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-xs italic text-slate-400">
                                Chưa có thẻ thông số nào.
                            </p>
                        </div>
                    </div>

                    <!-- [UI-01] Tab 3: Gia công theo bản vẽ / Tùy chỉnh (pdp_metadata.custom_*) -->
                    <div class="pt-4 mt-2 border-t border-slate-200 space-y-4">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">
                                Tab 3: Gia công theo bản vẽ / Tùy chỉnh (PDP)
                            </h4>
                            <p class="text-xs text-slate-500">
                                Nội dung hiển thị trong Tab &quot;Gia công theo bản vẽ&quot; trên trang chi tiết sản phẩm. Tab tự động ẩn nếu toàn bộ các trường đều trống.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.pdp_metadata.custom_title"
                                :field="{
                                    type: 'text',
                                    name: 'pdp_custom_title',
                                    label: 'Tiêu đề Tab Gia công theo bản vẽ',
                                    placeholder: 'VD: Quy trình gia công & cung ứng theo bản vẽ OEM',
                                }"
                            />
                            <Field
                                v-model="form.pdp_metadata.custom_desc"
                                :field="{
                                    type: 'textarea',
                                    name: 'pdp_custom_desc',
                                    label: 'Mô tả ngắn Tab Gia công theo bản vẽ',
                                    rows: 2,
                                    placeholder: 'Mô tả khả năng tùy chỉnh bước xích, vật liệu, xử lý bề mặt...',
                                }"
                            />
                        </div>

                        <Field
                            v-model="form.pdp_metadata.custom_content"
                            :field="{
                                type: 'richtext',
                                name: 'pdp_custom_content',
                                size: 'sm',
                                label: 'Nội dung chi tiết Tab Gia công theo bản vẽ',
                            }"
                        />

                        <Field
                            v-model="form.pdp_metadata.custom_cta_text"
                            :field="{
                                type: 'text',
                                name: 'pdp_custom_cta_text',
                                label: 'Chữ nút CTA Tab Gia công theo bản vẽ',
                                placeholder: 'VD: Trao đổi yêu cầu tùy chỉnh với kỹ sư',
                            }"
                        />

                        <!-- Repeater custom_steps -->
                        <div class="space-y-3 pt-2">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <h5 class="text-xs font-semibold uppercase tracking-wide text-slate-700">
                                        Các bước gia công / tùy chỉnh (Custom Steps)
                                    </h5>
                                    <p class="text-xs text-slate-500">
                                        Hiển thị dạng lưới 3 cột trong Tab Gia công theo bản vẽ (Số bước, Tiêu đề, Mô tả).
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="addCustomStepRow(form)"
                                    class="px-3 py-1.5 text-xs font-medium rounded bg-[#005EA4] hover:bg-[#004D86] text-white transition-colors"
                                >
                                    + Thêm bước gia công
                                </button>
                            </div>

                            <div
                                v-if="Array.isArray(form.pdp_metadata?.custom_steps) && form.pdp_metadata.custom_steps.length > 0"
                                class="space-y-2"
                            >
                                <div
                                    v-for="(step, csIdx) in form.pdp_metadata.custom_steps"
                                    :key="csIdx"
                                    class="bg-slate-50 p-3 rounded border border-slate-200 space-y-2"
                                >
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                                        <div class="md:col-span-3">
                                            <input
                                                v-model="step.step"
                                                type="text"
                                                class="w-full rounded border border-slate-300 px-3 py-1.5 text-sm bg-white focus:border-[#005EA4] focus:outline-none"
                                                placeholder="Số bước (VD: Bước 01)"
                                            />
                                        </div>
                                        <div class="md:col-span-8">
                                            <input
                                                v-model="step.title"
                                                type="text"
                                                class="w-full rounded border border-slate-300 px-3 py-1.5 text-sm bg-white focus:border-[#005EA4] focus:outline-none"
                                                placeholder="Tiêu đề bước (VD: Gửi bản vẽ & thông số)"
                                            />
                                        </div>
                                        <div class="md:col-span-1 flex justify-end">
                                            <button
                                                type="button"
                                                @click="removeCustomStepRow(form, csIdx)"
                                                class="px-2.5 py-1.5 text-xs font-medium rounded text-red-600 hover:bg-red-50 border border-red-200 transition-colors"
                                                title="Xóa bước"
                                            >
                                                Xóa
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <textarea
                                            v-model="step.description"
                                            rows="2"
                                            class="w-full rounded border border-slate-300 px-3 py-1.5 text-sm bg-white focus:border-[#005EA4] focus:outline-none"
                                            placeholder="Mô tả chi tiết bước gia công..."
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-xs italic text-slate-400">
                                Chưa có bước gia công nào.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <SeoFields :modelValue="form" @update:modelValue="form = $event" />
        </template>
        <template #aside="{ form }">
            <div class="card">
                <div class="card-body">
                    <Field
                        v-model="form.status"
                        :field="{
                            type: 'radio_list',
                            name: 'status',
                            label: 'Trạng thái',
                            options: schema.columns.status.list,
                        }"
                    />
                    <Field
                        v-model="form.position"
                        :field="{
                            type: 'number',
                            name: 'position',
                            label: 'Thứ tự hiển thị (Position)',
                            placeholder: 'VD: 1 (số nhỏ hiển thị trước)',
                        }"
                    />
                    <Field
                        v-model="form.image"
                        :field="{
                            type: 'file_upload',
                            name: 'image',
                            label: 'Ảnh đại diện chính',
                            multiple: false,
                        }"
                    />
                    <Field
                        v-model="form.images"
                        :field="{
                            type: 'file_upload',
                            name: 'images',
                            label: 'Bộ sưu tập hình ảnh',
                            multiple: true,
                        }"
                    />
                    <div class="grid grid-cols-2 gap-2 pt-2">
                        <Field
                            v-model="form.is_featured"
                            :field="{
                                type: 'checkbox',
                                name: 'is_featured',
                                label: 'Sản phẩm nổi bật',
                            }"
                        />
                        <Field
                            v-model="form.is_new"
                            :field="{
                                type: 'checkbox',
                                name: 'is_new',
                                label: 'Sản phẩm mới',
                            }"
                        />
                    </div>
                    <Field
                        v-model="form.view_count"
                        :disabled="true"
                        :field="{
                            type: 'number',
                            name: 'view_count',
                            label: 'Lượt xem',
                        }"
                    />
                </div>
            </div>
        </template>
    </Form>
</template>
<script>
const DEFAULT_TECH_OPTIONS = [
    { id: 'Gia công CNC chính xác', label: 'Gia công CNC chính xác & Tiện phay' },
    { id: 'Đúc & Nhiệt luyện cao tần', label: 'Đúc & Nhiệt luyện cao tần chịu mỏi' },
    { id: 'Lắp ráp tiêu chuẩn OEM/ODM', label: 'Lắp ráp tiêu chuẩn OEM/ODM công nghiệp' },
    { id: 'Dập nguội & Chốt rỗng chuẩn ISO', label: 'Dập nguội & Chốt rỗng chuẩn ISO/DIN' },
    { id: 'Động cơ BLDC không chổi than', label: 'Công nghệ động cơ BLDC tiết kiệm điện' },
    { id: 'Custom', label: 'Gia công & Chế tạo theo yêu cầu bản vẽ' },
]

const DEFAULT_MATERIAL_OPTIONS = [
    { id: 'Thép hợp kim 40Cr/SCM435', label: 'Thép hợp kim 40Cr / SCM435 (Tôi cao tần)' },
    { id: 'Inox 304 chống rỉ', label: 'Inox 304 / SUS304 (Chống ăn mòn hóa chất)' },
    { id: 'Inox 316', label: 'Inox 316 / SUS316 (Môi trường thực phẩm & biển)' },
    { id: 'Polyurethane (PU) chịu tải', label: 'Polyurethane (PU) chịu tải cao & giảm chấn' },
    { id: 'Nhựa POM kỹ thuật', label: 'Nhựa kỹ thuật POM / Polyacetal (Hệ số ma sát thấp)' },
    { id: 'Nhựa PA66 / Nylon', label: 'Nhựa PA66 / Nylon chịu mài mòn' },
    { id: 'Cao su EPDM / NBR', label: 'Cao su kỹ thuật EPDM / NBR (Chịu nhiệt & dầu)' },
    { id: 'Hợp kim nhôm hàng không', label: 'Hợp kim nhôm hàng không nhẹ & bền' },
    { id: 'Thép Carbon C45', label: 'Thép Carbon C45 cường độ cao' },
]

export default {
    props: ['item', 'schema'],
    data() {
        return {
            formData: this.buildFormData(this.item),
        }
    },

    computed: {
        printTechnologyOptions() {
            const list = [...DEFAULT_TECH_OPTIONS]
            const current = this.formData?.print_technology
            if (current && !list.some((opt) => opt.id === current)) {
                list.unshift({ id: current, label: current })
            }
            return list
        },
        materialOptions() {
            const list = [...DEFAULT_MATERIAL_OPTIONS]
            const current = this.formData?.material
            if (current && !list.some((opt) => opt.id === current)) {
                list.unshift({ id: current, label: current })
            }
            return list
        },
    },

    watch: {
        item(newVal) {
            this.formData = this.buildFormData(newVal)
        },
    },

    methods: {
        ensurePdpMetadata(form) {
            if (!form.pdp_metadata || typeof form.pdp_metadata !== 'object') {
                form.pdp_metadata = {}
            }
        },
        addSpecificationRow(form) {
            if (!Array.isArray(form.specifications)) {
                form.specifications = []
            }
            form.specifications.push({ label: '', value: '' })
        },
        removeSpecificationRow(form, index) {
            if (Array.isArray(form.specifications)) {
                form.specifications.splice(index, 1)
            }
        },
        addBadgeRow(form) {
            this.ensurePdpMetadata(form)
            if (!Array.isArray(form.pdp_metadata.badges)) {
                form.pdp_metadata.badges = []
            }
            form.pdp_metadata.badges.push('')
        },
        removeBadgeRow(form, index) {
            if (Array.isArray(form.pdp_metadata?.badges)) {
                form.pdp_metadata.badges.splice(index, 1)
            }
        },
        addSpecsCardRow(form) {
            this.ensurePdpMetadata(form)
            if (!Array.isArray(form.pdp_metadata.specs_cards)) {
                form.pdp_metadata.specs_cards = []
            }
            form.pdp_metadata.specs_cards.push({ label: '', value: '', description: '' })
        },
        removeSpecsCardRow(form, index) {
            if (Array.isArray(form.pdp_metadata?.specs_cards)) {
                form.pdp_metadata.specs_cards.splice(index, 1)
            }
        },
        addCustomStepRow(form) {
            this.ensurePdpMetadata(form)
            if (!Array.isArray(form.pdp_metadata.custom_steps)) {
                form.pdp_metadata.custom_steps = []
            }
            form.pdp_metadata.custom_steps.push({ step: '', title: '', description: '' })
        },
        removeCustomStepRow(form, index) {
            if (Array.isArray(form.pdp_metadata?.custom_steps)) {
                form.pdp_metadata.custom_steps.splice(index, 1)
            }
        },
        buildFormData(item = {}) {
            const safeItem = item && typeof item === 'object' ? item : {}
            const specs = Array.isArray(safeItem.specifications)
                ? safeItem.specifications.map((row) => ({
                      label: row?.label ?? '',
                      value: row?.value ?? '',
                  }))
                : []
            const meta =
                safeItem.pdp_metadata && typeof safeItem.pdp_metadata === 'object'
                    ? safeItem.pdp_metadata
                    : {}

            const rawSpecsCards =
                Array.isArray(meta.specs_cards) && meta.specs_cards.length > 0
                    ? meta.specs_cards
                    : Array.isArray(meta.specsCards) && meta.specsCards.length > 0
                      ? meta.specsCards
                      : Array.isArray(meta.manufacturing_info)
                        ? meta.manufacturing_info
                        : []

            const normalizedSpecsCards = rawSpecsCards
                .filter((c) => c && typeof c === 'object')
                .map((c) => ({
                    label: String(c.label ?? c.title ?? ''),
                    value: String(c.value ?? c.heading ?? ''),
                    description: String(c.description ?? c.desc ?? ''),
                }))

            const rawCustomSteps =
                Array.isArray(meta.custom_steps) && meta.custom_steps.length > 0
                    ? meta.custom_steps
                    : Array.isArray(meta.customSteps) && meta.customSteps.length > 0
                      ? meta.customSteps
                      : Array.isArray(meta.care_instructions)
                        ? meta.care_instructions
                        : []

            const normalizedCustomSteps = rawCustomSteps
                .filter((s) => s && typeof s === 'object')
                .map((s) => ({
                    step: String(s.step ?? s.number ?? ''),
                    title: String(s.title ?? s.name ?? ''),
                    description: String(s.description ?? s.desc ?? ''),
                }))

            const normalizedBadges =
                Array.isArray(meta.badges) && meta.badges.length > 0
                    ? meta.badges
                          .map((b) => {
                              if (typeof b === 'string') return b
                              if (b && typeof b === 'object') {
                                  return String(b.label ?? b.text ?? b.title ?? b.name ?? '')
                              }
                              return ''
                          })
                          .filter((b) => typeof b === 'string' && b.trim() !== '')
                    : [meta.badge_1, meta.badge_2]
                          .filter((b) => typeof b === 'string' && b.trim() !== '')
                          .map((b) => b.trim())

            const normalizedMeta = {
                ...meta,
                brand_name: meta.brand_name ?? meta.brand ?? '',
                origin: meta.origin ?? '',
                warranty: meta.warranty ?? '',
                condition: meta.condition ?? '',
                stock_status: meta.stock_status ?? '',
                technical_support: meta.technical_support ?? '',
                story_title: meta.story_title ?? meta.storyTitle ?? '',
                story_intro: meta.story_intro ?? meta.storyIntro ?? '',
                feature1_title: meta.feature1_title ?? meta.feature1Title ?? '',
                feature1_desc: meta.feature1_desc ?? meta.feature1Desc ?? '',
                feature2_title: meta.feature2_title ?? meta.feature2Title ?? '',
                feature2_desc: meta.feature2_desc ?? meta.feature2Desc ?? '',
                specs_title: meta.specs_title ?? meta.specsTitle ?? '',
                specs_desc: meta.specs_desc ?? meta.specsDesc ?? meta.specs_description ?? '',
                specs_content: meta.specs_content ?? meta.specsContent ?? '',
                specs_cards: normalizedSpecsCards,
                custom_title: meta.custom_title ?? meta.customTitle ?? '',
                custom_desc: meta.custom_desc ?? meta.customDesc ?? meta.custom_description ?? '',
                custom_content: meta.custom_content ?? meta.customContent ?? '',
                custom_steps: normalizedCustomSteps,
                custom_cta_text: meta.custom_cta_text ?? meta.customCtaText ?? '',
                badges: normalizedBadges,
            }

            const redundantAliases = [
                'specsTitle',
                'specsDesc',
                'specs_description',
                'specsContent',
                'specsCards',
                'manufacturing_info',
                'customTitle',
                'customDesc',
                'custom_description',
                'customContent',
                'customSteps',
                'care_instructions',
                'customCtaText',
                'badge_1',
                'badge_2',
            ]
            redundantAliases.forEach((alias) => {
                delete normalizedMeta[alias]
            })

            return {
                ...safeItem,
                slug: safeItem.slug ?? '',
                categories: safeItem.categories ?? [],
                price: safeItem.price ?? null,
                compare_at_price: safeItem.compare_at_price ?? null,
                print_technology: safeItem.print_technology ?? '',
                material: safeItem.material ?? '',
                lead_time: safeItem.lead_time ?? '',
                sample_size: safeItem.sample_size ?? '',
                layer_height: safeItem.layer_height ?? '',
                nozzle_size: safeItem.nozzle_size ?? '',
                infill: safeItem.infill ?? '',
                support_material: safeItem.support_material ?? '',
                image_color_label: safeItem.image_color_label ?? meta.image_color_label ?? '',
                customization_label:
                    safeItem.customization_label ?? meta.customization_label ?? '',
                specifications: specs,
                pdp_metadata: normalizedMeta,
                images: safeItem.images ?? [],
                status: safeItem.status ?? 'ACTIVE',
                position: safeItem.position ?? null,
                view_count: safeItem.view_count ?? 0,
            }
        },
    },
}
</script>
