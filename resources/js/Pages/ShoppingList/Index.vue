<script setup>
import { useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

// Props coming from the controller (shopping items and flash messages)
const props = defineProps({
    shoppingItems: Array,
    flash: Object,
});

const form = useForm({
    name: '',
});

const submitForm = () => {
    form.post(route('shoppingList.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <GuestLayout>
        <h1>Shopping List</h1>

        <!-- Success Message -->
        <div v-if="flash && flash.success" class="bg-green-500 mt-2">
            {{ flash.success }}
        </div>

        <!-- Add a New Item -->
        <form @submit.prevent="submitForm">
            <div>
                <label for="name">New Item:</label>
                <input v-model="form.name" type="text" name="name" id="name" required maxlength="255">
                <button type="submit" :disabled="form.processing">Add Item</button>
            </div>

            <!-- Validation Errors -->
            <div v-if="form.errors.name" class="bg-red-500 mt-2">
                {{ form.errors.name }}
            </div>
        </form>

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
