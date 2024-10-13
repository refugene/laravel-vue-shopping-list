<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
</script>

<template>
    <GuestLayout>
        <h1>Shopping List</h1>
        <p v-if="shoppingItems.length === 0">No items in the shopping list.</p>
        <ul v-else>
            <li v-for="item in shoppingItems" :key="item.id">{{ item.name }}</li>
        </ul>
    </GuestLayout>
</template>

<script>
export default {
    props: {
        shoppingItems: Array,
    },
};
</script>
