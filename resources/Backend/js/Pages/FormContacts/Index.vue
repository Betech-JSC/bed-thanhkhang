<template layout>
    <Table
        :schema="schema"
        :columns="[
            'id',
            {
                field: 'name',
                label: 'Họ và tên',
                transform: (data) => {
                    const d = data.data || data.data_contact || {}
                    return d['Họ và tên'] || d['Họ tên'] || d.name || d.full_name || '-'
                },
            },
            {
                field: 'phone',
                label: 'Số điện thoại',
                transform: (data) => {
                    const d = data.data || data.data_contact || {}
                    return d['Số điện thoại'] || d['Số ĐT'] || d.phone || d.telephone || '-'
                },
            },
            {
                field: 'email',
                label: 'Email',
                transform: (data) => {
                    const d = data.data || data.data_contact || {}
                    return d['Email'] || d.email || data.data_contact?.email || '-'
                },
            },
            {
                field: 'service',
                label: 'Dịch vụ',
                transform: (data) => {
                    const d = data.data || data.data_contact || {}
                    const s = d['Dịch vụ quan tâm'] || d['Dịch vụ'] || d.service || d.service_title || d.Service
                    if (typeof s === 'object' && s !== null) {
                        return s.title || s.name || '-'
                    }
                    return s || '-'
                },
            },
            'status',
            'created_at',
        ]"
        :config="{
            canCreate: false,
        }"
    />
</template>
<script>
export default {
    props: ['schema'],
}
</script>
