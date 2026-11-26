<template layout>
    <Table
        :schema="schema"
        :columns="['id', 'title', 'download', 'position', 'status', 'created_at', 'updated_at']"
    />
</template>
<script>
export default {
    props: ['schema'],
}
</script>
