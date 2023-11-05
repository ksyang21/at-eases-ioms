<script setup>
import {Link} from "@inertiajs/vue3";

const items = [
    {
        label: 'Home',
        link: 'dashboard'
    },
    {
        label: 'Products',
        link: 'products'
    }
]
</script>

<template>
    <nav class="breadcrumb mx-auto sm:px-6 lg:px-8 py-8">
        <p v-for="(item, index) in items">
          <span :key="index" class="breadcrumb-item">
              <template v-if="index > 0"> > </template>
              <Link :href="route(item.link)">{{ item.label }}</Link>
          </span>
        </p>
    </nav>
</template>
<style scoped>
.breadcrumb {
    background: #f5f5f5;
    border-radius: 5px;
    display: flex;
    align-items: center;
}

.breadcrumb-item {
    font-size: 30px;
    color: #333;
    margin-left: 5px;
    margin-right: 5px;
}

.breadcrumb-item:not(:first-child)::before {
    content: '>';
    margin: 0 10px;
    color: #777;
}
</style>
