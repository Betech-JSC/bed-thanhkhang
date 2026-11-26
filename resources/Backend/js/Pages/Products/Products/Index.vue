<template layout>
    <Table
        :schema="schema"
        :columns="['id', 'image', 'title', 'status', 'view_count', 'created_at', 'updated_at']"
        :config="{
            canExport: false,
            canImport: false,
        }"
    >
        <template #filter="{ filters }">
            <div class="flex space-x-2">
                <Field
                    class="w-[250px]"
                    v-model="status"
                    @change="filters['status'] = status"
                    :field="{
                        type: 'dropdown',
                        name: 'status',
                        label: false,
                        keyBy: 'id',
                        labelBy: 'label',
                        placeholder: 'Trạng thái',
                        options: [
                            { id: 'ALL', label: 'Xem tất cả' },
                            { id: 'ACTIVE', label: 'Hiển thị' },
                            { id: 'INACTIVE', label: 'Ẩn' },
                        ],
                    }"
                />
            </div>
        </template>
    </Table>
</template>
<script>
export default {
    props: ['schema'],
    data() {
        return {
            status: null,
        }
    },
}
</script>
