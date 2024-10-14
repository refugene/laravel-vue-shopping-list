<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    color: {
        type: String,
        default: 'gray',
    },
});
</script>

<template>
    <button
        :type="type"
        :class="[
            'inline-flex items-center rounded-md px-4 py-2 text-xs font-semibold uppercase tracking-widest shadow-sm transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-25',
            color === 'red' ? 'bg-red-500 text-white hover:bg-red-600 border-transparent' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
        ]"

    >
        <slot />
    </button>
</template>
