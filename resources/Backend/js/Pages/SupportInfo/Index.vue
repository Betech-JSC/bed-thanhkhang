<template layout>
    <Head title="Thông tin hỗ trợ & Chính sách" />

    <div class="lg:p-6 space-y-6">
        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="alert-success">
            ✅ {{ $page.props.flash.success }}
        </div>

        <!-- Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">📞 Thông tin hỗ trợ & Chính sách</h1>
                <p class="page-sub">Cấu hình hotline, link nhóm và nội dung chính sách hỗ trợ khách hàng.</p>
            </div>
        </div>

        <form @submit.prevent="save">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Card: Hiển thị -->
                <div class="card col-span-full">
                    <div class="card-header">⚙️ Cài đặt hiển thị</div>
                    <div class="card-body">
                        <label class="toggle-row">
                            <span class="toggle-label">Hiển thị khối hỗ trợ trên trang</span>
                            <div
                                class="toggle-switch"
                                :class="{ active: form.is_visible }"
                                @click="form.is_visible = !form.is_visible"
                            >
                                <div class="toggle-knob"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Card: Hotline -->
                <div class="card">
                    <div class="card-header">📱 Hotline</div>
                    <div class="card-body space-y-4">
                        <div class="field">
                            <label class="field-label">Nhãn hiển thị</label>
                            <input
                                v-model="form.hotline_label"
                                type="text"
                                class="field-input"
                                placeholder="VD: Hotline hỗ trợ"
                            />
                        </div>
                        <div class="field">
                            <label class="field-label">Số điện thoại</label>
                            <input
                                v-model="form.hotline"
                                type="text"
                                class="field-input"
                                placeholder="VD: 0901 234 567"
                            />
                            <p class="field-hint">Người dùng có thể click để gọi trực tiếp.</p>
                        </div>
                    </div>
                </div>

                <!-- Card: Link nhóm -->
                <div class="card">
                    <div class="card-header">💬 Link nhóm hỗ trợ</div>
                    <div class="card-body space-y-4">
                        <div class="field">
                            <label class="field-label">Nhãn hiển thị</label>
                            <input
                                v-model="form.group_label"
                                type="text"
                                class="field-input"
                                placeholder="VD: Nhóm Zalo hỗ trợ"
                            />
                        </div>
                        <div class="field">
                            <label class="field-label">URL nhóm</label>
                            <input
                                v-model="form.group_link"
                                type="url"
                                class="field-input"
                                placeholder="https://zalo.me/g/..."
                            />
                            <p class="field-hint">Dán link nhóm Zalo, Facebook Group hoặc Telegram.</p>
                        </div>
                    </div>
                </div>

                <!-- Card: Chính sách / Hướng dẫn -->
                <div class="card col-span-full">
                    <div class="card-header">📋 Hướng dẫn & Chính sách</div>
                    <div class="card-body space-y-4">
                        <div class="field">
                            <label class="field-label">Tiêu đề</label>
                            <input
                                v-model="form.policy_title"
                                type="text"
                                class="field-input"
                                placeholder="VD: Chính sách hỗ trợ khách hàng"
                            />
                        </div>
                        <div class="field">
                            <label class="field-label">Nội dung hướng dẫn</label>
                            <textarea
                                v-model="form.policy_content"
                                class="field-textarea"
                                rows="8"
                                placeholder="Nhập nội dung chính sách, hướng dẫn liên hệ, lưu ý quan trọng..."
                            ></textarea>
                            <p class="field-hint">Hỗ trợ xuống dòng (Enter).</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Save Button -->
            <div class="save-row">
                <button type="submit" class="btn-save" :disabled="saving">
                    <span v-if="saving">⏳ Đang lưu...</span>
                    <span v-else>💾 Lưu thay đổi</span>
                </button>
            </div>
        </form>

        <!-- Preview -->
        <div class="card" v-if="form.is_visible">
            <div class="card-header">👁️ Xem trước (Preview)</div>
            <div class="card-body">
                <div class="preview-box">
                    <div v-if="form.policy_title || form.policy_content" class="preview-policy">
                        <h4 v-if="form.policy_title" class="preview-policy-title">{{ form.policy_title }}</h4>
                        <p v-if="form.policy_content" class="preview-policy-content">{{ form.policy_content }}</p>
                    </div>
                    <div class="preview-contacts">
                        <a v-if="form.hotline" :href="`tel:${form.hotline}`" class="preview-contact-item">
                            📱 {{ form.hotline_label }}: <strong>{{ form.hotline }}</strong>
                        </a>
                        <a v-if="form.group_link" :href="form.group_link" target="_blank" class="preview-contact-item">
                            💬 {{ form.group_label }} →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['item'],

    data() {
        return {
            saving: false,
            form: {
                hotline:        this.item?.hotline        ?? '',
                hotline_label:  this.item?.hotline_label  ?? 'Hotline hỗ trợ',
                group_link:     this.item?.group_link     ?? '',
                group_label:    this.item?.group_label    ?? 'Group hỗ trợ Zalo/Facebook',
                policy_title:   this.item?.policy_title   ?? 'Chính sách hỗ trợ khách hàng',
                policy_content: this.item?.policy_content ?? '',
                is_visible:     this.item?.is_visible     ?? true,
            },
        }
    },

    methods: {
        save() {
            this.saving = true
            const locale = this.getCurrentLocale()
            this.$inertia.post(
                this.route(locale + '.admin.support-info.store'),
                this.form,
                {
                    onFinish: () => { this.saving = false },
                }
            )
        },
    },
}
</script>

<style scoped>
.page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}
.page-title { font-size: 22px; font-weight: 700; color: #1e293b; margin: 0 0 4px; }
.page-sub   { font-size: 14px; color: #64748b; margin: 0; }

.alert-success {
    background: #dcfce7;
    border: 1px solid #86efac;
    color: #15803d;
    padding: 12px 16px;
    border-radius: 10px;
    font-weight: 600;
}

.card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden; }
.card-header { padding: 14px 20px; font-size: 14px; font-weight: 700; color: #1e293b; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
.card-body { padding: 20px; }

/* Toggle */
.toggle-row { display: flex; align-items: center; justify-content: space-between; cursor: pointer; }
.toggle-label { font-size: 14px; font-weight: 600; color: #374151; }
.toggle-switch {
    width: 48px; height: 26px;
    background: #d1d5db; border-radius: 13px;
    position: relative; transition: background 0.2s; cursor: pointer;
}
.toggle-switch.active { background: #2970FF; }
.toggle-knob {
    position: absolute; top: 3px; left: 3px;
    width: 20px; height: 20px;
    background: #fff; border-radius: 50%;
    transition: transform 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.toggle-switch.active .toggle-knob { transform: translateX(22px); }

/* Fields */
.field { display: flex; flex-direction: column; gap: 6px; }
.field-label { font-size: 13px; font-weight: 600; color: #374151; line-height: 1.4; }
.field-input {
    padding: 10px 14px; border: 1px solid #d1d5db;
    border-radius: 8px; font-size: 14px; color: #1e293b;
    outline: none; transition: border 0.2s;
    width: 100%;
}
.field-input:focus { border-color: #2970FF; box-shadow: 0 0 0 3px rgba(41,112,255,0.15); }
.field-textarea {
    padding: 10px 14px; border: 1px solid #d1d5db;
    border-radius: 8px; font-size: 14px; color: #1e293b;
    outline: none; transition: border 0.2s; width: 100%;
    resize: vertical; font-family: inherit; line-height: 1.6;
}
.field-textarea:focus { border-color: #2970FF; box-shadow: 0 0 0 3px rgba(41,112,255,0.15); }
.field-hint { font-size: 12px; color: #94a3b8; margin: 0; }

/* Save */
.save-row { display: flex; justify-content: flex-end; margin-top: 20px; }
.btn-save {
    padding: 12px 28px;
    background: linear-gradient(135deg, #2970FF, #155EEF);
    color: #fff; border: none; border-radius: 10px;
    font-size: 14px; font-weight: 700; cursor: pointer;
    transition: opacity 0.2s, transform 0.2s;
}
.btn-save:hover:not(:disabled) { opacity: 0.9; transform: translateY(-1px); }
.btn-save:disabled { opacity: 0.6; cursor: not-allowed; }

/* Preview */
.preview-box {
    border: 2px dashed #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    background: #fafafa;
}
.preview-policy { margin-bottom: 16px; }
.preview-policy-title { font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 8px; }
.preview-policy-content { font-size: 13px; color: #64748b; line-height: 1.7; margin: 0; white-space: pre-line; }
.preview-contacts { display: flex; flex-wrap: wrap; gap: 12px; }
.preview-contact-item {
    display: inline-flex; align-items: center; gap: 6px;
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 8px; padding: 8px 14px;
    font-size: 13px; color: #374151;
    text-decoration: none; transition: border-color 0.2s;
}
.preview-contact-item:hover { border-color: #2970FF; color: #155EEF; }
</style>
