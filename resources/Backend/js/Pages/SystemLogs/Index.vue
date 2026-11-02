<template layout>
    <Head title="Logs Hệ thống" />

    <div class="space-y-6 p-6 mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">🖥️ Logs Hệ thống</h1>
                <p class="text-slate-500 text-sm">Xem và quản lý các file log của hệ thống Laravel.</p>
            </div>
        </div>

        <!-- Alert messages -->
        <div v-if="flashSuccess" class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium">
            ✅ {{ flashSuccess }}
        </div>
        <div v-if="flashError" class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium">
            ❌ {{ flashError }}
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Left Side: List of Log Files -->
            <div class="lg:col-span-1 bg-white border border-slate-200/60 rounded-xl shadow-sm overflow-hidden flex flex-col h-[700px]">
                <div class="p-4 border-b border-slate-100 font-bold text-slate-800 flex justify-between items-center bg-slate-50">
                    <span>Danh sách file log</span>
                    <span class="text-xs bg-slate-200 px-2 py-0.5 rounded text-slate-600 font-normal">{{ files.length }} files</span>
                </div>
                <div class="divide-y divide-slate-100 overflow-y-auto flex-1">
                    <div 
                        v-for="file in files" 
                        :key="file.name"
                        class="p-4 hover:bg-slate-50 transition cursor-pointer flex flex-col gap-2"
                        :class="{ 'bg-blue-50/50 border-l-4 border-blue-500': selectedFile === file.name }"
                        @click="selectFile(file.name)"
                    >
                        <div class="flex justify-between items-start gap-2">
                            <span class="font-medium text-slate-800 break-all text-sm" :class="{ 'text-blue-600 font-bold': selectedFile === file.name }">
                                {{ file.name }}
                            </span>
                            <button 
                                @click.stop="confirmDelete(file.name)"
                                class="text-slate-400 hover:text-red-600 p-1 rounded hover:bg-red-50 transition text-xs"
                                title="Xóa file"
                            >
                                🗑️
                            </button>
                        </div>
                        <div class="flex justify-between text-xs text-slate-400">
                            <span>{{ file.size }}</span>
                            <span>{{ file.modified }}</span>
                        </div>
                    </div>
                    <div v-if="files.length === 0" class="p-8 text-center text-slate-400 text-sm">
                        Không có file log nào.
                    </div>
                </div>
            </div>

            <!-- Right Side: Log Content Viewer -->
            <div class="lg:col-span-3 bg-white border border-slate-200/60 rounded-xl shadow-sm flex flex-col h-[700px]">
                <div v-if="selectedFile" class="flex-1 flex flex-col overflow-hidden">
                    <!-- Viewer Header -->
                    <div class="p-4 border-b border-slate-100 bg-slate-50 flex flex-wrap justify-between items-center gap-3">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-800 text-sm break-all">📄 {{ selectedFile }}</span>
                        </div>
                        <div class="flex gap-2">
                            <button 
                                @click="confirmClear(selectedFile)"
                                class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-lg text-xs font-semibold transition"
                            >
                                🧹 Xóa trắng nội dung
                            </button>
                            <button 
                                @click="confirmDelete(selectedFile)"
                                class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-lg text-xs font-semibold transition"
                            >
                                🗑️ Xóa file log
                            </button>
                        </div>
                    </div>

                    <!-- Warning header about length -->
                    <div class="px-4 py-2 bg-amber-50 border-b border-amber-100 text-amber-800 text-xs flex justify-between items-center">
                        <span>⚠️ Đang hiển thị tối đa 1000 dòng cuối cùng của log để tránh quá tải trình duyệt.</span>
                        <button @click="refreshSelected" class="text-amber-900 underline hover:no-underline font-semibold">Tải lại</button>
                    </div>

                    <!-- Viewer Content -->
                    <div class="flex-1 bg-slate-900 text-slate-100 p-4 font-mono text-xs overflow-auto select-text whitespace-pre">
                        <template v-if="logContent && logContent.trim()">{{ logContent }}</template>
                        <template v-else><span class="text-slate-500 italic">[File log rỗng]</span></template>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="flex-1 flex flex-col justify-center items-center p-12 text-slate-400 gap-3">
                    <span class="text-5xl">📄</span>
                    <span class="text-sm font-semibold">Vui lòng chọn một file log từ danh sách bên trái để xem nội dung.</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        files: Array,
        selectedFile: String,
        logContent: String,
        flash: Object,
    },
    computed: {
        flashSuccess() {
            return this.flash?.success || this.$page.props.flash?.success
        },
        flashError() {
            return this.flash?.error || this.$page.props.flash?.error
        }
    },
    methods: {
        selectFile(fileName) {
            this.$inertia.get(this.route('admin.system-logs.index'), { file: fileName }, {
                preserveState: true,
                preserveScroll: true,
            })
        },
        refreshSelected() {
            if (this.selectedFile) {
                this.selectFile(this.selectedFile)
            }
        },
        confirmClear(fileName) {
            if (confirm(`Bạn có chắc chắn muốn xóa sạch nội dung file log "${fileName}" không?`)) {
                this.$inertia.post(this.route('admin.system-logs.clear', { file: fileName }), {}, {
                    onSuccess: () => {
                        this.refreshSelected()
                    }
                })
            }
        },
        confirmDelete(fileName) {
            if (confirm(`Bạn có chắc chắn muốn xóa HOÀN TOÀN file log "${fileName}" khỏi ổ đĩa không?`)) {
                this.$inertia.delete(this.route('admin.system-logs.destroy', { file: fileName }))
            }
        }
    }
}
</script>
