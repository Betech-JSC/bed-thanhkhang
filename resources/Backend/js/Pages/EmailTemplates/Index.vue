<template layout>
    <Table
        :schema="schema"
        :columns="[
            'id',
            'type',
            'subject',
            'status',
            'created_at',
            'updated_at',
        ]"
    />
</template>
<script>
export default {
    props: ['schema'],
}
</script>
