<script setup>

import { useForm } from '@inertiajs/vue3';
import { computed, ref } from "vue";
import Draggable from 'vuedraggable';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";

// Props coming from the controller (shopping items and flash messages)
const props = defineProps({
    shoppingItems: Array,
    flash: Object,
});

// Use ref for reactivity
const shoppingItems = ref([...props.shoppingItems]);

// Compute the total price in pounds
const total = computed(() => {
    return (shoppingItems.value.reduce((sum, item) => sum + item.price, 0) / 100).toFixed(2);
});

// Check if the total exceeds £100
const totalExceedsLimit = computed(() => {
    return total.value > 100;
});

const form = useForm({
    name: '',
    priceInPounds: '', // For user input in pounds
    price: 0, // Price in pence, used for the request
});

const updateForm = useForm({});

const deleteForm = useForm({});

const addItem = () => {
    // Convert price from pounds to pence
    form.price = Math.round(form.priceInPounds * 100);

    form.post(route('shoppingList.store'), {
        preserveScroll: true,
        onSuccess: (page) => {
            form.reset();
            // Add the new item
            if (page.props.newItem) {
                shoppingItems.value.push(page.props.newItem);
            }
        },
    });
};

// Toggle the bought status of a shopping list item
const toggleBought = (id) => {
    const item = shoppingItems.value.find(item => item.id === id);
    if (item) {
        updateForm.patch(route('shoppingList.toggleBought', id), {
            data: { is_bought: !item.is_bought },
            preserveScroll: true,
            onSuccess: () => {
                item.is_bought = !item.is_bought;
            },
            onError: (errors) => {
                console.error("Failed to update the item status:", errors);
            }
        });
    }
};

// Reorder shopping items
const updateSortOrder = (event) => {
    const newOrder = shoppingItems.value;

    axios.patch(route('shoppingList.updateSortOrder'), {
        orderedItems: newOrder.map(item => item.id),
    })
        .then(response => {
            console.log('Order updated successfully:', response.data);
        })
        .catch(error => {
            console.error('Failed to update the order:', error.response.data);
        });
};

// Delete shopping list item
const deleteItem = (id) => {
    if (confirm('Are you sure you want to delete this item?')) {
        deleteForm.delete(route('shoppingList.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                // Remove the item from the list
                shoppingItems.value = shoppingItems.value.filter(item => item.id !== id);
            },
            onError: (errors) => {
                console.error('Failed to delete the item', errors);
            },
        });
    }
};

</script>

<template>
    <GuestLayout>
        <h1 class="text-4xl font-bold text-center my-4">Shopping List</h1>

        <!-- Success Message -->
        <div v-if="flash && flash.success" class="bg-green-500 text-white font-bold px-4 py-3 mx-1 rounded">
            {{ flash.success }}
        </div>

        <!-- Add a New Item -->
        <form @submit.prevent="addItem" class="mt-2">
            <div class="flex flex-col">
                <h2 class="text-xl font-bold">New Item</h2>
                <InputLabel value="Name:" />
                <TextInput v-model="form.name" name="name" id="name" required maxlength="255" class="mb-2" />

                <!-- Price Input in Pounds -->
                <InputLabel value="Price (£):" />
                <TextInput v-model="form.priceInPounds" :modelValue="form.priceInPounds.toString()" name="price" id="price" required type="number" min="0" step="0.01" class="mb-2" />

                <PrimaryButton :disabled="form.processing" class="my-2 w-1/4 justify-center">
                    Add
                </PrimaryButton>
            </div>

            <!-- Validation Errors -->
            <InputError :message="form.errors.name" />
            <InputError :message="form.errors.price" />
        </form>

        <p v-if="shoppingItems.length === 0">No items in the shopping list.</p>
        <draggable v-model="shoppingItems" item-key="id" tag="ul" @end="updateSortOrder" class="space-y-2">
            <template #item="{ element }">
                <li
                    :key="element.id"
                    class="flex justify-between items-center rounded-full px-3 py-2 my-2 border border-blue-500"
                >
                    <div class="flex-grow break-words min-w-0">
                        {{ element.name }} - £{{ (element.price / 100).toFixed(2) }}
                    </div>

                    <div class="flex space-x-2">
                        <!-- Toggle Bought Button -->
                        <SecondaryButton @click="toggleBought(element.id)" :color="element.is_bought ? 'yellow' : 'green'">
                            {{ element.is_bought ? 'Undo' : 'Bought' }}
                        </SecondaryButton>

                        <!-- Delete Button -->
                        <SecondaryButton @click="deleteItem(element.id)" color="red">
                            Delete
                        </SecondaryButton>
                    </div>
                </li>
            </template>
        </draggable>
        <!-- Alert if Total Exceeds £100 -->
        <div v-if="totalExceedsLimit" class="bg-red-500 text-white font-bold px-4 py-3 mx-1 my-3 rounded">
            The total exceeds £100!
        </div>
        <div class="my-10">
            <h2 class="text-3xl">Total: £{{total}}</h2>
        </div>
    </GuestLayout>
</template>
