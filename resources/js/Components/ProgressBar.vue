<script setup>
const props = defineProps({
    progress: Number,
})
</script>

<template>
    <div class="relative">
        <div :style="{ left: progress + '%' }"
             class="marker absolute top-0 transform -translate-x-1/2 -translate-y-full bg-red-400 text-white px-2 py-1 rounded-md animate-pulse">
            {{ progress }}%
        </div>
        <div class="w-full h-4 mb-4 bg-gray-200 rounded-full">
            <div class="h-4 bg-red-400 rounded-full" :style="{width: progress + '%'}"></div>
        </div>
    </div>
</template>

<style scoped>
.marker {
    transform: translateY(-10px);
}
</style>
