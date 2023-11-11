<script setup>
import {Link} from "@inertiajs/vue3";

const props = defineProps({
    breadcrumbs: Object
})

console.log(props.breadcrumbs)
const items = [
    {
        label: 'Home',
        link: 'dashboard'
    },
    {
        label: 'Products',
        link: 'products.index'
    }
]
</script>

<template>
    <nav class="breadcrumb mx-auto sm:px-6 lg:px-8 py-8 flex text-[15px] md:text-[25px]">
        <p>
          <span class="breadcrumb-item">
              <Link :href="route('dashboard')">Home</Link>
          </span>
        </p>
        <p v-for="(item, index) in breadcrumbs">
          <span :key="index" class="breadcrumb-item">
              <span class="mr-3">/</span>
              <Link v-if="item.params" :href="route(item.link, item.params)">{{ item.label }}</Link>
              <Link v-else :href="route(item.link)">{{ item.label }}</Link>
          </span>
        </p>
    </nav>
</template>
<style scoped>
.breadcrumb {
    background: #f5f5f5;
    border-radius: 5px;
    align-items: center;
}

.breadcrumb-item {
    color: #333;
    margin-left: 5px;
    margin-right: 5px;
}

.breadcrumb-item:hover {
    color: #d71d28;
}
</style>
