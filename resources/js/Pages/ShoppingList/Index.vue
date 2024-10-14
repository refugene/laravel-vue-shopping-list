<script setup>
import { useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import { Inertia } from '@inertiajs/inertia';

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

const deleteItem = (id) => {
    if (confirm('Are you sure you want to delete this item?')) {
        Inertia.delete(route('shoppingList.destroy', id), {
            onSuccess: () => {
                console.log('Item deleted successfully!');
            },
        });
    }
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
                <InputLabel value="New Item" />
                <TextInput v-model="form.name" name="name" id="name" required maxlength="255" />
                <PrimaryButton :disabled="form.processing">
                    Add Item
                </PrimaryButton>
            </div>

            <!-- Validation Errors -->
            <InputError :message="form.errors.name" />
        </form>

        <p v-if="shoppingItems.length === 0">No items in the shopping list.</p>
        <ul v-else>
            <li
                v-for="item in shoppingItems"
                :key="item.id"
                class="flex justify-between items-center rounded-full px-3 py-2 my-1 border border-blue-500"
            >
                {{ item.name }}

                <!-- Delete Button -->
                <SecondaryButton @click="deleteItem(item.id)" color="red">
                    Delete
                </SecondaryButton>
            </li>
        </ul>
    </GuestLayout>
</template>
