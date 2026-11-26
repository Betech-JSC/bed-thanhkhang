<template layout>
    <Head :title="tt('models.titles.setting')" />
    <WrapSetting>
        <Form v-model="formData" :config="{ canDestroy: false, reverse: true, addGrid: false, resource: 'settings' }">
            <template #default="{ form }">
                <div class="card">
                    <div class="card-body">
                        <Field
                            v-model="form.general_logo"
                            :field="{
                                type: 'file_upload',
                                label: 'Logo thương hiệu',
                                name: 'general_logo',
                            }"
                        />
                        <Field
                            v-model="form.general_business_name"
                            :field="{
                                name: 'general_business_name',
                            }"
                        />
                        <Field
                            v-model="form.general_business_name_en"
                            :field="{
                                label: 'Tên doanh nghiệp (EN)',
                                name: 'general_business_name_en',
                                placeholder: 'Để trống tự động lấy Tiếng Việt',
                            }"
                        />
                        <Field
                            v-model="form.general_site_name"
                            :field="{
                                name: 'general_site_name',
                            }"
                        />
                        <Field
                            v-model="form.general_site_name_en"
                            :field="{
                                label: 'Tên website (EN)',
                                name: 'general_site_name_en',
                                placeholder: 'Để trống tự động lấy Tiếng Việt',
                            }"
                        />
                        <Field
                            v-model="form.general_description"
                            :field="{
                                type: 'textarea',
                                label: 'Mô tả ngắn doanh nghiệp (Footer & SEO)',
                                name: 'general_description',
                                rows: 3,
                            }"
                        />
                        <Field
                            v-model="form.general_description_en"
                            :field="{
                                type: 'textarea',
                                label: 'Mô tả ngắn doanh nghiệp (EN)',
                                name: 'general_description_en',
                                rows: 3,
                                placeholder: 'Để trống tự động lấy Tiếng Việt',
                            }"
                        />
                        <Field
                            v-model="form.general_email"
                            :field="{
                                label: 'Email',
                                name: 'general_email',
                            }"
                        />
                        <Field
                            v-model="form.general_tax_code"
                            :field="{
                                name: 'general_tax_code',
                                label: 'Mã số thuế doanh nghiệp',
                                placeholder: 'VD: 0107621881',
                            }"
                        />
                        <Field
                            v-model="form.general_address"
                            :field="{
                                label: 'Địa chỉ',
                                name: 'general_address',
                            }"
                        />
                        <Field
                            v-model="form.general_address_en"
                            :field="{
                                label: 'Địa chỉ (EN)',
                                name: 'general_address_en',
                                placeholder: 'Để trống tự động lấy Tiếng Việt',
                            }"
                        />
                        <Field
                            v-model="form.showroom_address"
                            :field="{
                                label: 'Địa chỉ Showroom / Văn phòng giao dịch',
                                name: 'showroom_address',
                            }"
                        />
                        <Field
                            v-model="form.showroom_address_en"
                            :field="{
                                label: 'Địa chỉ Showroom / Văn phòng giao dịch (EN)',
                                name: 'showroom_address_en',
                                placeholder: 'Để trống tự động lấy Tiếng Việt',
                            }"
                        />
                        <Field
                            v-model="form.general_phone"
                            :field="{
                                label: 'Phone',
                                name: 'general_phone',
                            }"
                        />
                        <Field
                            v-model="form.general_zalo"
                            :field="{
                                label: 'Số / Link Zalo tư vấn kỹ thuật',
                                name: 'general_zalo',
                                placeholder: 'VD: 0967732665 hoặc https://zalo.me/0967732665',
                            }"
                        />
                        <Field
                            v-model="form.general_working_hours"
                            :field="{
                                label: 'Giờ làm việc',
                                name: 'general_working_hours',
                                placeholder: 'VD: Thứ 2 - Thứ 7: 08:00 - 17:30',
                            }"
                        />
                        <Field
                            v-model="form.general_working_hours_en"
                            :field="{
                                label: 'Giờ làm việc (EN)',
                                name: 'general_working_hours_en',
                                placeholder: 'VD: Mon - Sat: 08:00 - 17:30',
                            }"
                        />
                        <Field
                            v-model="form.general_facebook"
                            :field="{
                                label: 'Link Fanpage Facebook',
                                name: 'general_facebook',
                                placeholder: 'https://www.facebook.com/...',
                            }"
                        />
                        <Field
                            v-model="form.general_linkedin"
                            :field="{
                                label: 'Link LinkedIn',
                                name: 'general_linkedin',
                                placeholder: 'https://www.linkedin.com/company/...',
                            }"
                        />
                        <Field
                            v-model="form.general_tiktok"
                            :field="{
                                label: 'Link Kênh TikTok',
                                name: 'general_tiktok',
                                placeholder: 'https://tiktok.com/@...',
                            }"
                        />
                        <Field
                            v-model="form.map_iframe"
                            :field="{
                                type: 'textarea',
                                label: 'Google Maps Embed URL / Iframe',
                                name: 'map_iframe',
                                rows: 3,
                                placeholder: 'Dán URL embed hoặc nguyên thẻ <iframe src=...> Google Maps',
                            }"
                        />
                        <Field
                            v-model="form.notification_popup_mode"
                            :field="{
                                type: 'dropdown',
                                label: 'Chế độ Popup thông báo góc màn hình',
                                name: 'notification_popup_mode',
                                options: [
                                    { id: 'fake', label: 'Chỉ thông báo ảo (Dùng Message Pool)' },
                                    { id: 'real', label: 'Chỉ thông báo thật (Lượt tải tài nguyên từ hệ thống)' },
                                    { id: 'hybrid', label: 'Kết hợp cả thật và ảo (Ưu tiên dữ liệu thật)' },
                                ],
                                placeholder: 'Chọn chế độ hiển thị',
                            }"
                        />
                        <Field
                            v-show="form.notification_popup_mode !== 'fake'"
                            v-model="form.notification_popup_name_style"
                            :field="{
                                type: 'dropdown',
                                label: 'Định dạng tên người dùng trên Popup thật',
                                name: 'notification_popup_name_style',
                                options: [
                                    { id: 'masked', label: 'Viết tắt bảo mật (VD: Bảo N*** vừa tải về...)' },
                                    { id: 'anonymous', label: 'Ẩn danh chung (VD: Ai đó vừa tải về...)' },
                                    { id: 'full', label: 'Tên đầy đủ (VD: Nguyễn Văn Bảo vừa tải về...)' },
                                ],
                                placeholder: 'Chọn định dạng tên',
                            }"
                        />
                        <Field
                            v-model="form.messagePool"
                            :field="{
                                type: 'textarea',
                                label: 'Message Pool (Dữ liệu thông báo ảo)',
                                name: 'messagePool',
                                rows: 8,
                                help: 'Các câu thông báo ngăn cách bởi dấu phẩy, dùng khi ở chế độ Ảo hoặc Kết hợp.',
                            }"
                        />
                    </div>
                </div>

                <!-- Trang chủ — Phần Giới thiệu (Home About Section) -->
                <div class="card">
                    <div class="card-header font-bold text-slate-800">
                        Trang chủ — Phần Giới thiệu (Về chúng tôi)
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.home_about_eyebrow"
                                :field="{
                                    label: 'Nhãn trên tiêu đề (VI)',
                                    name: 'home_about_eyebrow',
                                }"
                            />
                            <Field
                                v-model="form.home_about_eyebrow_en"
                                :field="{
                                    label: 'Nhãn trên tiêu đề (EN)',
                                    name: 'home_about_eyebrow_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.home_about_lead_highlight"
                                :field="{
                                    label: 'Tiêu đề nổi bật (VI)',
                                    name: 'home_about_lead_highlight',
                                }"
                            />
                            <Field
                                v-model="form.home_about_lead_highlight_en"
                                :field="{
                                    label: 'Tiêu đề nổi bật (EN)',
                                    name: 'home_about_lead_highlight_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.home_about_lead_rest"
                                :field="{
                                    type: 'textarea',
                                    rows: 2,
                                    label: 'Phần tiếp theo tiêu đề (VI)',
                                    name: 'home_about_lead_rest',
                                }"
                            />
                            <Field
                                v-model="form.home_about_lead_rest_en"
                                :field="{
                                    type: 'textarea',
                                    rows: 2,
                                    label: 'Phần tiếp theo tiêu đề (EN)',
                                    name: 'home_about_lead_rest_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.home_about_paragraph_1"
                                :field="{
                                    type: 'textarea',
                                    rows: 4,
                                    label: 'Đoạn mô tả 1 (VI)',
                                    name: 'home_about_paragraph_1',
                                }"
                            />
                            <Field
                                v-model="form.home_about_paragraph_1_en"
                                :field="{
                                    type: 'textarea',
                                    rows: 4,
                                    label: 'Đoạn mô tả 1 (EN)',
                                    name: 'home_about_paragraph_1_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.home_about_paragraph_2"
                                :field="{
                                    type: 'textarea',
                                    rows: 4,
                                    label: 'Đoạn mô tả 2 (VI)',
                                    name: 'home_about_paragraph_2',
                                }"
                            />
                            <Field
                                v-model="form.home_about_paragraph_2_en"
                                :field="{
                                    type: 'textarea',
                                    rows: 4,
                                    label: 'Đoạn mô tả 2 (EN)',
                                    name: 'home_about_paragraph_2_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.home_about_commitment_quote"
                                :field="{
                                    label: 'Câu trích dẫn cam kết (VI)',
                                    name: 'home_about_commitment_quote',
                                }"
                            />
                            <Field
                                v-model="form.home_about_commitment_quote_en"
                                :field="{
                                    label: 'Câu trích dẫn cam kết (EN)',
                                    name: 'home_about_commitment_quote_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.home_about_commitment_sub"
                                :field="{
                                    label: 'Mô tả phụ cam kết (VI)',
                                    name: 'home_about_commitment_sub',
                                }"
                            />
                            <Field
                                v-model="form.home_about_commitment_sub_en"
                                :field="{
                                    label: 'Mô tả phụ cam kết (EN)',
                                    name: 'home_about_commitment_sub_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.home_about_cta_text"
                                :field="{
                                    label: 'Nút CTA — Tiêu đề (VI)',
                                    name: 'home_about_cta_text',
                                }"
                            />
                            <Field
                                v-model="form.home_about_cta_text_en"
                                :field="{
                                    label: 'Nút CTA — Tiêu đề (EN)',
                                    name: 'home_about_cta_text_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.home_about_cta_link"
                                :field="{
                                    label: 'Nút CTA — Đường dẫn (VI)',
                                    name: 'home_about_cta_link',
                                    placeholder: 'VD: /gioi-thieu',
                                }"
                            />
                            <Field
                                v-model="form.home_about_cta_link_en"
                                :field="{
                                    label: 'Nút CTA — Đường dẫn (EN)',
                                    name: 'home_about_cta_link_en',
                                    placeholder: 'VD: /en/about-us',
                                }"
                            />
                        </div>
                    </div>
                </div>

                <!-- Trang chủ — Phần Liên hệ / Báo giá (Home Contact Section) -->
                <div class="card">
                    <div class="card-header font-bold text-slate-800">
                        Trang chủ — Phần Liên hệ &amp; Yêu cầu báo giá
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.home_contact_title"
                                :field="{
                                    label: 'Tiêu đề khối Liên hệ (VI)',
                                    name: 'home_contact_title',
                                }"
                            />
                            <Field
                                v-model="form.home_contact_title_en"
                                :field="{
                                    label: 'Tiêu đề khối Liên hệ (EN)',
                                    name: 'home_contact_title_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.home_contact_description"
                                :field="{
                                    type: 'textarea',
                                    rows: 3,
                                    label: 'Mô tả khối Liên hệ (VI)',
                                    name: 'home_contact_description',
                                }"
                            />
                            <Field
                                v-model="form.home_contact_description_en"
                                :field="{
                                    type: 'textarea',
                                    rows: 3,
                                    label: 'Mô tả khối Liên hệ (EN)',
                                    name: 'home_contact_description_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                    </div>
                </div>

                <!-- Trang Giới thiệu — Câu chuyện Thành Khang (About Story Section) -->
                <div class="card">
                    <div class="card-header font-bold text-slate-800">
                        Trang Giới thiệu — Câu chuyện Thành Khang (Section 2)
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.about_story_eyebrow"
                                :field="{
                                    label: 'Nhãn trên tiêu đề (VI)',
                                    name: 'about_story_eyebrow',
                                }"
                            />
                            <Field
                                v-model="form.about_story_eyebrow_en"
                                :field="{
                                    label: 'Nhãn trên tiêu đề (EN)',
                                    name: 'about_story_eyebrow_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.about_story_heading"
                                :field="{
                                    type: 'textarea',
                                    rows: 2,
                                    label: 'Tiêu đề câu chuyện (VI)',
                                    name: 'about_story_heading',
                                }"
                            />
                            <Field
                                v-model="form.about_story_heading_en"
                                :field="{
                                    type: 'textarea',
                                    rows: 2,
                                    label: 'Tiêu đề câu chuyện (EN)',
                                    name: 'about_story_heading_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.about_story_paragraph_1"
                                :field="{
                                    type: 'textarea',
                                    rows: 3,
                                    label: 'Đoạn mô tả 1 (VI)',
                                    name: 'about_story_paragraph_1',
                                }"
                            />
                            <Field
                                v-model="form.about_story_paragraph_1_en"
                                :field="{
                                    type: 'textarea',
                                    rows: 3,
                                    label: 'Đoạn mô tả 1 (EN)',
                                    name: 'about_story_paragraph_1_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.about_story_paragraph_2"
                                :field="{
                                    type: 'textarea',
                                    rows: 3,
                                    label: 'Đoạn mô tả 2 (VI)',
                                    name: 'about_story_paragraph_2',
                                }"
                            />
                            <Field
                                v-model="form.about_story_paragraph_2_en"
                                :field="{
                                    type: 'textarea',
                                    rows: 3,
                                    label: 'Đoạn mô tả 2 (EN)',
                                    name: 'about_story_paragraph_2_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.about_story_paragraph_3"
                                :field="{
                                    type: 'textarea',
                                    rows: 3,
                                    label: 'Đoạn mô tả 3 (VI)',
                                    name: 'about_story_paragraph_3',
                                }"
                            />
                            <Field
                                v-model="form.about_story_paragraph_3_en"
                                :field="{
                                    type: 'textarea',
                                    rows: 3,
                                    label: 'Đoạn mô tả 3 (EN)',
                                    name: 'about_story_paragraph_3_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.about_story_commitment"
                                :field="{
                                    label: 'Câu cam kết nhấn mạnh (VI)',
                                    name: 'about_story_commitment',
                                }"
                            />
                            <Field
                                v-model="form.about_story_commitment_en"
                                :field="{
                                    label: 'Câu cam kết nhấn mạnh (EN)',
                                    name: 'about_story_commitment_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                    </div>
                </div>

                <!-- Trang Giới thiệu — Đồng hành cùng khách hàng (About Companion Section) -->
                <div class="card">
                    <div class="card-header font-bold text-slate-800">
                        Trang Giới thiệu — Đồng hành cùng khách hàng (Section 5)
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.about_companion_eyebrow"
                                :field="{
                                    label: 'Nhãn trên tiêu đề (VI)',
                                    name: 'about_companion_eyebrow',
                                }"
                            />
                            <Field
                                v-model="form.about_companion_eyebrow_en"
                                :field="{
                                    label: 'Nhãn trên tiêu đề (EN)',
                                    name: 'about_companion_eyebrow_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <Field
                                v-model="form.about_companion_heading"
                                :field="{
                                    label: 'Tiêu đề khối Đồng hành (VI)',
                                    name: 'about_companion_heading',
                                }"
                            />
                            <Field
                                v-model="form.about_companion_heading_en"
                                :field="{
                                    label: 'Tiêu đề khối Đồng hành (EN)',
                                    name: 'about_companion_heading_en',
                                    placeholder: 'Để trống tự động lấy Tiếng Việt',
                                }"
                            />
                        </div>
                    </div>
                </div>
                <SeoFields
                    :modelValue="form"
                    @update:modelValue="form = $event"
                    :config="{
                        hiddenFields: ['seo_slug'],
                    }"
                />
                <Field class="hidden" disabled v-model="form.id" :field="{ default: 'general' }" />
            </template>
        </Form>
    </WrapSetting>
</template>
<script>
import WrapSetting from '@Core/Components/WrapSetting.vue'

function parseJsonMedia(val) {
    if (!val) return null
    if (typeof val === 'object') return val
    if (typeof val === 'string') {
        const trimmed = val.trim()
        if (trimmed.startsWith('{') || trimmed.startsWith('[')) {
            try {
                return JSON.parse(trimmed)
            } catch (e) {
                return val
            }
        }
        return trimmed || null
    }
    return val
}

function buildGeneralFormData(item) {
    const raw = { ...(item || {}) }
    delete raw.tax_code

    return {
        id: 'general',
        ...raw,
        general_logo: parseJsonMedia(item?.general_logo),
        general_tax_code: item?.general_tax_code || item?.tax_code || '',
        map_iframe: item?.map_iframe || item?.general_map_iframe || '',
    }
}

export default {
    components: { WrapSetting },
    props: ['item', 'schema'],
    data() {
        return {
            formData: buildGeneralFormData(this.item),
        }
    },
    watch: {
        item(newVal) {
            this.formData = buildGeneralFormData(newVal)
        },
    },
}
</script>
