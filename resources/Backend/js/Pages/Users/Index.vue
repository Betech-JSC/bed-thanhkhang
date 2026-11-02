<template layout>
    <Table :schema="schema" :columns="['id', 'email', 'name', 'created_at', 'updated_at']" />
</template>
<script>
export default {
    props: ['schema'],
}
</script>
