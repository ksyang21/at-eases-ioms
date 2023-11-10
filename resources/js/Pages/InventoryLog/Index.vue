<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {Head, Link} from "@inertiajs/vue3";
import moment from 'moment/moment.js';

const props = defineProps({
    product: Object,
    inventory_logs: Object
})
</script>

<template>

    <Head title="Inventory"/>

    <AuthenticatedLayout>
        <Breadcrumb></Breadcrumb>
        <div class="py-0">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
                        <div class="flex flex-col">
                            <p class="text-xl">{{ product.name }}</p>
                            <p class="text-sm text-gray-600">Total {{ inventory_logs.length }} records</p>
                        </div>
                        <Link :href="route('inventoryLogs.create', product.id)"
                              class="focus:outline-none text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 ml-auto">
                            New Record
                        </Link>
                    </div>
                    <div class="p-6">
                        <div class="relative overflow-x-auto sm:rounded-lg">
                            <table
                                class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border-2 border-gray-100">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Date
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Quantity
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Description
                                    </th>
                                </tr>
                                </thead>
                                <tbody v-if="inventory_logs.length > 0">
                                <tr class=" border-b hover:bg-gray-50"
                                    v-for="(log, index) in inventory_logs" :key="index"
                                    :class="log.stock_status === 'stock out' ? 'bg-gray-100' : 'bg-white'">
                                    <th scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <p v-if="log.stock_status === 'stock out'" class="text-blue-700">
                                            {{ log.stock_status.toUpperCase() }}</p>
                                        <p v-else class="text-green-700">{{ log.stock_status.toUpperCase() }}</p>
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ moment(log.created_at).format('YYYY-MM-DD HH:mm:ss') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <p v-if="log.stock_status === 'stock out'" class="text-red-700">
                                            -{{ log.quantity }}
                                        </p>
                                        <p v-else class="text-green-700">{{ log.quantity }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p v-if="log.description !== '-' || log.description !== null">
                                            {{ log.description }}
                                        </p>
                                        <p v-else>-</p>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center">No records found</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xl text-gray-700 mt-6">Current Stock: {{ product.stock_quantity }} </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
