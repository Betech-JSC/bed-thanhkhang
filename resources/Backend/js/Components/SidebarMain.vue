<template>
    <span class="hidden">
        {{ $page.props.route.name }}
    </span>
    <Link v-if="can('admin.posts.index')" :href="route('admin.posts.index')" :class="{ active: isUrl('admin.posts.*') }"
        class="item">
    <ph-newspaper-clipping-light />
    <span>{{ tt('models.table_list.posts') }}</span>
    </Link>
    <Link v-if="can('admin.contacts.index')" :href="route('admin.contacts.index')"
        :class="{ active: isUrl('admin.contacts.*') }" class="item">
    <material-symbols:connect-without-contact />
    <span>{{ tt('models.table_list.contacts') }}</span>
    <span class="badge badge-danger" v-if="newContact">{{ newContact }}</span>
    </Link>
    <Link v-if="can('admin.post-categories.index')" :href="route('admin.post-categories.index')"
        :class="{ active: isUrl('admin.post-categories.*') }" class="item">
    <bx:category />
    <span>{{ tt('models.table_list.post-categories') }}</span>
    </Link>
    <Link v-if="can('admin.project-categories.index')" :href="route('admin.project-categories.index')"
        :class="{ active: isUrl('admin.project-categories.*') }" class="item">
    <bx:category />
    <span>{{ tt('models.table_list.project-categories') }}</span>
    </Link>

    <Link v-if="can('admin.services.index')" :href="route('admin.services.index')"
        :class="{ active: isUrl('admin.services.*') }" class="item">
    <bx:category />
    <span>{{ tt('models.table_list.services') }}</span>
    </Link>
    <Link v-if="can('admin.members.index')" :href="route('admin.members.index')"
        :class="{ active: isUrl('admin.members.*') }" class="item">
    <bx:category />
    <span>{{ tt('models.table_list.members') }}</span>
    </Link>
    <Link v-if="can('admin.users.index')" :href="route('admin.users.index')" :class="{ active: isUrl('admin.users.*') }"
        class="item">
    <bx:category />
    <span>{{ tt('models.table_list.users') }}</span>
    </Link>
    <Link v-if="can('admin.support-info.index')" :href="route('admin.support-info.index')" :class="{ active: isUrl('admin.support-info.*') }"
        class="item">
    <material-symbols:support-agent />
    <span>Hỗ trợ &amp; Chính sách</span>
    </Link>
    <Link v-if="can('admin.feedback.index')" :href="route('admin.feedback.index')"
        :class="{ active: isUrl('admin.feedback.*') }" class="item">
    <bx:category />
    <span>{{ tt('models.table_list.feedback') }}</span>
    </Link>
    <Link v-if="can('admin.learning-environments.index')" :href="route('admin.learning-environments.index')"
        :class="{ active: isUrl('admin.learning-environments.*') }" class="item">
    <material-symbols:connect-without-contact />
    <span> Hình ảnh Môi trường học </span>
    </Link>
    <Link v-if="can('admin.prizes.index')" :href="route('admin.prizes.index')"
        :class="{ active: isUrl('admin.prizes.*') }" class="item">
    <material-symbols:connect-without-contact />
    <span> Giải thưởng </span>
    </Link>
    <Link v-if="can('admin.products.index')" :href="route('admin.products.index')"
        :class="{ active: isUrl('admin.products.*') }" class="item">
    <material-symbols:connect-without-contact />
    <span>{{ tt('models.table_list.products') }}</span>
    </Link>
    <Link v-if="can('admin.projects.index')" :href="route('admin.projects.index')"
        :class="{ active: isUrl('admin.projects.*') }" class="item">
    <material-symbols:connect-without-contact />
    <span>{{ tt('models.table_list.projects') }}</span>
    </Link>
    <Link v-if="can('admin.faqs.index')" :href="route('admin.faqs.index')" :class="{ active: isUrl('admin.faqs.*') }"
        class="item">
    <material-symbols:connect-without-contact />
    <span>{{ tt('models.table_list.faqs') }}</span>
    </Link>
    <Link v-if="can('admin.sliders.index')" :href="route('admin.sliders.index')"
        :class="{ active: isUrl('admin.sliders.*') && !route().params.filters?.position }" class="item">
    <bi:sliders2 />
    <span>{{ tt('models.table_list.sliders') }}</span>
    </Link>
    <Link v-if="can('admin.email-templates.index')" :href="route('admin.email-templates.index')"
        :class="{ active: isUrl('admin.email-templates.*') }" class="item">
    <material-symbols:mail-outline />
    <span> Mẫu Email </span>
    </Link>
    <Link v-if="can('admin.policies.index')" :href="route('admin.policies.index')"
        :class="{ active: isUrl('admin.policies.*') }" class="item">
    <material-symbols:policy-outline-sharp />
    <span>{{ tt('models.table_list.policies') }}</span>
    </Link>
    <Link v-if="can('admin.system-logs.index')" :href="route('admin.system-logs.index')" :class="{ active: isUrl('admin.system-logs.*') }"
        class="item">
    <material-symbols:terminal-sharp />
    <span>Logs Hệ thống</span>
    </Link>
</template>

<script>
export default {
    computed: {
        newContact() {
            return this.$page.props.data.new_contact_count
        },
    },
}
</script>
<style scoped>
.badge {
    @apply px-2 py-1 text-xs font-medium rounded;
}

.badge-success {
    @apply text-green-700 bg-green-200;
}

.badge-danger {
    @apply text-red-600 bg-red-200;
}
</style>
