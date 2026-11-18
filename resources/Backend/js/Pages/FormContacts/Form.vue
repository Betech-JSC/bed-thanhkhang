<template layout>
    <Form v-model="formData">
        <template #default="{ form }">
            <div class="card">
                <div class="card-header font-bold text-slate-800 flex items-center gap-2">
                    <i class="pi pi-user text-blue-600"></i>
                    <span>Thông tin Yêu cầu Báo giá (RFQ)</span>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Họ và tên</span>
                            <span class="text-base font-semibold text-slate-800">{{ customerName }}</span>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Số điện thoại</span>
                            <a
                                v-if="customerPhone !== '-'"
                                :href="`tel:${customerPhone}`"
                                class="text-base font-semibold text-blue-600 hover:underline"
                            >
                                {{ customerPhone }}
                            </a>
                            <span v-else class="text-base font-semibold text-slate-800">-</span>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Email</span>
                            <a
                                v-if="customerEmail !== '-'"
                                :href="`mailto:${customerEmail}`"
                                class="text-base font-semibold text-blue-600 hover:underline"
                            >
                                {{ customerEmail }}
                            </a>
                            <span v-else class="text-base font-semibold text-slate-800">-</span>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Dịch vụ quan tâm</span>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800"
                            >
                                {{ customerService }}
                            </span>
                        </div>
                    </div>

                    <div v-if="customerNote && customerNote !== '-'" class="p-4 rounded-xl bg-slate-50 border border-slate-200 mb-4">
                        <span class="text-xs uppercase font-bold text-slate-400 block mb-1">Nội dung cần hỗ trợ / Yêu cầu báo giá</span>
                        <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ customerNote }}</p>
                    </div>

                    <!-- Extra custom attributes if present -->
                    <div v-if="extraFields.length" class="mt-4 pt-4 border-t border-slate-100 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Thông tin bổ sung</h4>
                        <div v-for="item in extraFields" :key="item.key">
                            <Field
                                :disabled="true"
                                :modelValue="typeof item.value === 'object' ? JSON.stringify(item.value) : item.value"
                                :field="{ label: item.key }"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- File CAD & Tài liệu kỹ thuật -->
            <div class="card mt-6" v-if="cadAndDocumentFiles.length > 0">
                <div class="card-header flex items-center justify-between">
                    <span class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="pi pi-box text-blue-600"></i>
                        Bản vẽ CAD & Tài liệu kỹ thuật ({{ cadAndDocumentFiles.length }})
                    </span>
                    <span class="text-xs text-slate-400 font-medium">Bản vẽ kỹ thuật &amp; Tài liệu thông số</span>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div
                            v-for="(file, index) in cadAndDocumentFiles"
                            :key="index"
                            class="p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white hover:border-blue-400 transition-all shadow-sm flex items-center justify-between gap-4"
                        >
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div
                                    class="w-12 h-12 rounded-lg bg-blue-100 text-blue-700 font-black text-xs flex items-center justify-center shrink-0 uppercase tracking-wider"
                                >
                                    {{ getFileExt(file.path) }}
                                </div>
                                <div class="overflow-hidden">
                                    <div
                                        class="text-sm font-semibold text-slate-800 truncate"
                                        :title="getFileName(file.path)"
                                    >
                                        {{ getFileName(file.path) }}
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        Bản vẽ kỹ thuật / Tài liệu CAD
                                    </div>
                                </div>
                            </div>
                            <a
                                :href="staticUrl(file.path)"
                                target="_blank"
                                download
                                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-all shadow-sm cursor-pointer"
                            >
                                <i class="pi pi-download"></i>
                                <span>Tải file CAD</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hình ảnh đính kèm thông thường -->
            <div class="card mt-6" v-if="previewImages.length > 0">
                <div class="card-header font-bold text-slate-800 flex items-center gap-2">
                    <i class="pi pi-image text-slate-600"></i>
                    Hình ảnh đính kèm ({{ previewImages.length }})
                </div>
                <div class="card-body">
                    <div class="flex flex-wrap gap-4">
                        <div
                            v-for="(file, index) in previewImages"
                            :key="index"
                            class="w-[200px] aspect-square rounded-xl border border-slate-200 overflow-hidden relative group bg-white shadow-sm"
                        >
                            <img
                                v-if="file.path"
                                class="object-contain w-full h-full p-2"
                                :src="staticUrl(file.path)"
                                loading="lazy"
                            />
                            <a
                                :href="staticUrl(file.path)"
                                target="_blank"
                                class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white transition-opacity font-semibold text-xs gap-1.5"
                            >
                                <i class="pi pi-external-link"></i> Xem ảnh gốc
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #aside="{ form }">
            <div class="card">
                <div class="card-body">
                    <Field
                        v-model="form.status"
                        :field="{
                            type: 'radio_list',
                            name: 'status',
                            label: 'Trạng thái xử lý',
                            options: schema.columns.status.list,
                        }"
                    />
                    <Field
                        v-model="form.estimated_price"
                        :field="{
                            type: 'number',
                            name: 'estimated_price',
                            label: 'Giá trị báo giá tạm tính (VNĐ)',
                            placeholder: 'Nhập giá báo khách...',
                        }"
                    />
                    <Field
                        v-model="form.internal_notes"
                        :field="{
                            type: 'textarea',
                            name: 'internal_notes',
                            label: 'Ghi chú nội bộ (Internal Notes)',
                            placeholder: 'Ghi chú thông số kỹ thuật, bước xích, vật liệu, tiến độ giao hàng...',
                            rows: 4,
                        }"
                    />
                    <Field
                        v-model="form.formatted_created_at"
                        :disabled="true"
                        :field="{
                            type: 'text',
                            name: 'formatted_created_at',
                            label: 'Ngày gửi',
                        }"
                    />
                </div>
            </div>
        </template>
    </Form>
</template>

<script>
export default {
    props: ['item', 'schema'],
    data() {
        return {
            formData: {
                ...this.item,
                estimated_price: this.item.estimated_price ?? this.item.data?.estimated_price ?? null,
                internal_notes: this.item.internal_notes ?? this.item.data?.internal_notes ?? '',
            },
        }
    },
    watch: {
        item() {
            this.formData = {
                ...this.item,
                estimated_price: this.item.estimated_price ?? this.item.data?.estimated_price ?? null,
                internal_notes: this.item.internal_notes ?? this.item.data?.internal_notes ?? '',
            }
        },
    },
    computed: {
        rawContactData() {
            const d = this.formData.data_contact || this.formData.data || {}
            return typeof d === 'object' && d !== null ? d : {}
        },
        customerName() {
            const d = this.rawContactData
            return d['Họ và tên'] || d['Họ tên'] || d.name || d.full_name || '-'
        },
        customerPhone() {
            const d = this.rawContactData
            return d['Số điện thoại'] || d['Số ĐT'] || d.phone || d.telephone || '-'
        },
        customerEmail() {
            const d = this.rawContactData
            return d['Email'] || d.email || this.formData.data_contact?.email || '-'
        },
        customerService() {
            const d = this.rawContactData
            const s = d['Dịch vụ quan tâm'] || d['Dịch vụ'] || d.service || d.service_title || d.Service
            if (typeof s === 'object' && s !== null) {
                return s.title || s.name || '-'
            }
            return s || 'Tư vấn chung'
        },
        customerNote() {
            const d = this.rawContactData
            return (
                d['Nội dung cần hỗ trợ'] ||
                d['Lời nhắn'] ||
                d.note ||
                d.message ||
                d.customer_note ||
                d.content ||
                '-'
            )
        },
        extraFields() {
            const d = this.rawContactData
            const knownKeys = [
                'Họ và tên',
                'Họ tên',
                'name',
                'full_name',
                'Số điện thoại',
                'Số ĐT',
                'phone',
                'telephone',
                'Email',
                'email',
                'Dịch vụ quan tâm',
                'Dịch vụ',
                'service',
                'service_title',
                'Service',
                'Nội dung cần hỗ trợ',
                'Lời nhắn',
                'note',
                'message',
                'customer_note',
                'content',
                'File đính kèm',
                'file',
                'files',
            ]
            return Object.keys(d)
                .filter((k) => !knownKeys.includes(k))
                .map((k) => ({ key: k, value: d[k] }))
        },
        allFiles() {
            const list = []
            if (Array.isArray(this.formData.images)) {
                this.formData.images.forEach((img) => {
                    if (typeof img === 'string') list.push({ path: img })
                    else if (img && img.path) list.push(img)
                })
            }
            const data = this.rawContactData
            const extraFiles = data['File đính kèm'] || data['files'] || data['file'] || []
            if (Array.isArray(extraFiles)) {
                extraFiles.forEach((f) => {
                    const p = typeof f === 'string' ? f : f?.path
                    if (p && !list.some((x) => x.path === p)) {
                        list.push({ path: p })
                    }
                })
            } else if (typeof extraFiles === 'string' && extraFiles) {
                if (!list.some((x) => x.path === extraFiles)) {
                    list.push({ path: extraFiles })
                }
            }
            return list
        },
        previewImages() {
            return this.allFiles.filter((f) => this.isImageFile(f.path))
        },
        cadAndDocumentFiles() {
            return this.allFiles.filter((f) => !this.isImageFile(f.path))
        },
    },
    methods: {
        getFileName(path) {
            if (!path) return 'Tệp đính kèm'
            return path.split('/').pop().split('\\').pop()
        },
        getFileExt(path) {
            if (!path) return 'FILE'
            const clean = path.split('?')[0]
            const parts = clean.split('.')
            return parts.length > 1 ? parts.pop().toUpperCase() : 'FILE'
        },
        isImageFile(path) {
            if (!path) return false
            const clean = path.split('?')[0]
            return /\.(jpe?g|png|gif|webp|svg|avif)$/i.test(clean)
        },
    },
}
</script>
