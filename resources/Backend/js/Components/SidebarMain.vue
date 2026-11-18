<template>
    <span class="hidden">
        {{ $page.props.route.name }}
    </span>

    <!-- 1. BẢNG ĐIỀU KHIỂN -->
    <div class="sidebar-section-header">
        <span>📊 BẢNG ĐIỀU KHIỂN</span>
    </div>

    <Link
        v-if="can('admin.dashboard.index')"
        :href="route('admin.dashboard.index')"
        :class="{ active: isUrl('admin.dashboard.index') }"
        class="item"
    >
        <ph:chart-bar-light />
        <span>Bảng điều khiển</span>
    </Link>

    <Link
        :href="route('admin.user-guide.index')"
        :class="{ active: isUrl('admin.user-guide.*') }"
        class="item"
    >
        <ph:book-open-light />
        <span>Hướng dẫn sử dụng</span>
    </Link>

    <!-- 2. QUẢN LÝ SẢN PHẨM & BÁO GIÁ -->
    <div class="sidebar-section-header">
        <span>QUẢN LÝ SẢN PHẨM & BÁO GIÁ</span>
    </div>

    <!-- Yêu cầu báo giá kỹ thuật (RFQ) -->
    <Link
        v-if="can('admin.contacts.index')"
        :href="route('admin.contacts.index')"
        :class="{ active: isUrl('admin.contacts.*') }"
        class="item"
    >
        <material-symbols:connect-without-contact />
        <span>Yêu cầu báo giá kỹ thuật (RFQ)</span>
        <span class="badge badge-primary" v-if="newContact">{{ newContact }}</span>
    </Link>

    <!-- Quản lý Sản phẩm -->
    <Link
        v-if="can('admin.products.index')"
        :href="route('admin.products.index')"
        :class="{ active: isUrl('admin.products.*') }"
        class="item"
    >
        <material-symbols:inventory-2-outline />
        <span>Quản lý Sản phẩm</span>
    </Link>

    <!-- Quản lý Danh mục sản phẩm -->
    <Link
        v-if="can('admin.product-categories.index')"
        :href="route('admin.product-categories.index')"
        :class="{ active: isUrl('admin.product-categories.*') }"
        class="item"
    >
        <bx:category />
        <span>Danh mục Sản phẩm</span>
    </Link>

    <!-- Dịch vụ & Giải pháp Kỹ thuật -->
    <Link
        v-if="can('admin.services.index')"
        :href="route('admin.services.index')"
        :class="{ active: isUrl('admin.services.*') }"
        class="item"
    >
        <bx:category />
        <span>Dịch vụ &amp; Giải pháp Kỹ thuật</span>
    </Link>

    <!-- 3. NỘI DUNG & THƯƠNG HIỆU -->
    <div class="sidebar-section-header">
        <span>🎨 NỘI DUNG & THƯƠNG HIỆU</span>
    </div>

    <!-- Banner & Hero Trang chủ -->
    <Link
        v-if="can('admin.sliders.index')"
        :href="route('admin.sliders.index')"
        :class="{ active: isUrl('admin.sliders.*') && !route().params.filters?.position }"
        class="item"
    >
        <bi:sliders2 />
        <span>Banner &amp; Hero Trang chủ</span>
    </Link>

    <!-- Bài viết & Tin tức -->
    <Link
        v-if="can('admin.posts.index')"
        :href="route('admin.posts.index')"
        :class="{ active: isUrl('admin.posts.*') }"
        class="item"
    >
        <ph-newspaper-clipping-light />
        <span>Bài viết &amp; Tin tức</span>
    </Link>

    <!-- Danh mục bài viết -->
    <Link
        v-if="can('admin.post-categories.index')"
        :href="route('admin.post-categories.index')"
        :class="{ active: isUrl('admin.post-categories.*') }"
        class="item"
    >
        <bx:category />
        <span>Danh mục bài viết</span>
    </Link>

    <!-- Chính sách & Cam kết -->
    <Link
        v-if="can('admin.policies.index')"
        :href="route('admin.policies.index')"
        :class="{ active: isUrl('admin.policies.*') }"
        class="item"
    >
        <material-symbols:policy-outline-sharp />
        <span>Chính sách &amp; Cam kết</span>
    </Link>

    <!-- 4. HỆ THỐNG & CẤU HÌNH -->
    <div class="sidebar-section-header">
        <span>⚙️ HỆ THỐNG & CẤU HÌNH</span>
    </div>

    <!-- Khách hàng đăng ký -->
    <Link
        v-if="can('admin.users.index')"
        :href="route('admin.users.index')"
        :class="{ active: isUrl('admin.users.*') }"
        class="item"
    >
        <heroicons-outline:user-group />
        <span>Khách hàng đăng ký</span>
    </Link>

    <!-- Mẫu Email thông báo -->
    <Link
        v-if="can('admin.email-templates.index')"
        :href="route('admin.email-templates.index')"
        :class="{ active: isUrl('admin.email-templates.*') }"
        class="item"
    >
        <material-symbols:mail-outline />
        <span>Mẫu Email thông báo</span>
    </Link>

    <!-- Quản lý Tệp -->
    <Link
        v-if="can('admin.files.index')"
        :href="route('admin.files.index')"
        :class="{ active: isUrl('admin.files.*') }"
        class="item"
    >
        <ph:image />
        <span>Quản lý Tệp Media</span>
    </Link>

    <!-- Cài đặt chung & Hotline -->
    <Link
        v-if="can('admin.settings.index') || can('admin.settings.form')"
        :href="route('admin.settings.form', { id: 'general' })"
        :class="{ active: isUrl('admin.settings.*') }"
        class="item"
    >
        <heroicons-outline:cog-8-tooth />
        <span>Cài đặt chung &amp; Hotline</span>
    </Link>

    <!-- Quản trị viên -->
    <Link
        v-if="can('admin.admins.index')"
        :href="route('admin.admins.index')"
        :class="{ active: isUrl('admin.admins.*') }"
        class="item"
    >
        <heroicons-outline:user-circle />
        <span>Quản trị viên</span>
    </Link>

    <!-- Phân quyền -->
    <Link
        v-if="can('admin.roles.index')"
        :href="route('admin.roles.index')"
        :class="{ active: isUrl('admin.roles.*') }"
        class="item"
    >
        <heroicons-outline:user-group />
        <span>Phân quyền vai trò</span>
    </Link>

    <!-- Logs Hệ thống -->
    <Link
        v-if="can('admin.system-logs.index')"
        :href="route('admin.system-logs.index')"
        :class="{ active: isUrl('admin.system-logs.*') }"
        class="item"
    >
        <material-symbols:terminal-sharp />
        <span>Logs Hệ thống</span>
    </Link>
</template>

<script>
export default {
    computed: {
        newContact() {
            return this.$page.props.data?.new_contact_count || this.$page.props.new_contact_count
        },
    },
}
</script>

<style scoped>
.sidebar-section-header {
    @apply px-6 pt-5 pb-1.5 text-[11px] font-bold text-gray-400 uppercase tracking-wider select-none flex items-center;
}

.badge {
    @apply px-2 py-0.5 text-xs font-semibold rounded-full min-w-[20px] text-center inline-flex items-center justify-center;
}

.badge-primary {
    @apply text-white bg-primary;
}

.badge-success {
    @apply text-green-700 bg-green-200;
}

.badge-danger {
    @apply text-red-600 bg-red-200;
}
</style>
