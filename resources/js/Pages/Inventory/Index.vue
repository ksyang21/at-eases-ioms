<script setup>

import {Head} from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
</script>

<template>

    <Head title="Orders"/>

    <AuthenticatedLayout>
        <Breadcrumb></Breadcrumb>
        <div class="py-0">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="block md:flex md:items-center py-4 mx-6 border-b-2 border-gray-100">
                        <p class="text-2xl">Inventory Management</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
