<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {ref} from "vue";
import {Bar} from 'vue-chartjs'
import {Chart} from 'chart.js/auto'
import ProgressBar from "@/Components/ProgressBar.vue";

let todaySales = 28000
let pendingOrders = [1, 2, 3, 4, 5]

let showAnnouncement = ref(true)

let chartData = {
    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
    datasets: [
        {
            label: 'Goal',
            data: [1250, 1350, 1300, 1700, 1900, 2700, 2150],
            fill: false,
            borderColor: '#23B7E5',
            backgroundColor: '#23B7E5'
        },
        {
            label: 'Sales',
            data: [1200, 1300, 1277, 1633, 1898, 2740, 2263],
            backgroundColor: '#66BD78',
        },
    ],
}

function hideAnnouncement() {
    showAnnouncement.value = false
}
</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <Breadcrumb></Breadcrumb>
        <div class="py-4">
            <div class="bg-red-400 text-white px-6 py-4 rounded-md flex items-center text-xl mx-3"
                 v-if="showAnnouncement">
                <font-awesome-icon icon="bell"/>
                <p class="ml-3">This is an announcement.</p>
                <font-awesome-icon icon="times-circle" class="ml-auto cursor-pointer hover:text-gray-300"
                                   @click="hideAnnouncement"/>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl mt-5 mx-3">
                <div class="px-6 pt-6 pb-4">
                    <p class="text-sm">Ongoing Campaign</p>
                </div>
                <div class="flex items-center pb-6 mx-6 border-b-2 border-gray-100">
                    <div class="py-2.5 px-4 w-full">
                        <div class="md:flex md:items-center hidden">
                            <p class="mt-3 md:mt-0 font-bold text-3xl">Shocking Sales 11.11</p>
                            <div class="flex flex-col ml-auto">
                                <div class="text-3xl flex items-center mt-3 md:mt-0">
                                    <span>RM 28,000.00</span>
                                    <span class="ml-3">of</span>
                                    <span class="ml-3 text-red-400">RM 1,000,000.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="block md:hidden">
                            <p>Target</p>
                            <span class="text-xl text-red-400">RM 1,000,000.00</span>
                        </div>
                        <ProgressBar
                            :progress="10"
                            class="mt-6"
                        />
                    </div>
                </div>
            </div>
            <div class="flex items-center mt-5">
                <div class="bg-white h-[500px] rounded-2xl w-full md:w-2/3 mx-3">
                    <div class="flex items-center border-b-2 border-gray-100 py-3 px-4">
                        <p class="text-xl">Sales Overview</p>
                        <div class="ml-auto">
                            <span class="bg-blue-200 px-3 py-1 rounded cursor-pointer hover:bg-blue-400">Daily</span>
                            <span
                                class="bg-blue-700 px-3 py-1 rounded cursor-pointer hover:bg-blue-400 ml-2 text-white">Weekly</span>
                            <span
                                class="bg-blue-200 px-3 py-1 rounded cursor-pointer hover:bg-blue-400 ml-2">Monthly</span>
                            <span
                                class="bg-blue-200 px-3 py-1 rounded cursor-pointer hover:bg-blue-400 ml-2">Yearly</span>
                        </div>
                    </div>
                    <div class="py-4 px-16">
                        <Bar :data="chartData" class="max-h-[400px]"/>
                    </div>
                </div>
                <div class="bg-white h-[500px] rounded-2xl w-full md:w-1/3 mx-3">
                    <div class="flex items-center border-b-2 border-gray-100 py-3 px-4">
                        <p class="text-xl">Pending Orders</p>
                    </div>
                    <div class="pb-4 px-5">
                        <ul class="w-full divide-y divide-gray-200">
                            <li class="pb-3 sm:py-4">
                                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                    <div class="flex-shrink-0">
                                        <p>#OD1001</p>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                            Customer 2
                                        </p>
                                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                            49670 Celestino Trace Apt. 751 Hettingermouth, UT 00315-8845
                                        </p>
                                    </div>
                                    <div
                                        class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                        RM 1,500.00
                                    </div>
                                </div>
                            </li>
                            <li class="pb-3 sm:py-4">
                                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                    <div class="flex-shrink-0">
                                        <p>#OD1002</p>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                            Customer 3
                                        </p>
                                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                            52046 Halvorson Junctions Leschton, PA 96365-0822
                                        </p>
                                    </div>
                                    <div
                                        class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                        RM 0.30
                                    </div>
                                </div>
                            </li>
                        </ul>

                    </div>
                </div>
            </div>
            <div class="md:flex md:items-center h-full mt-5 mx-3">
                <div
                    class="py-4 px-6 rounded-2xl shadow-sm flex items-center border-2 border-gray-100 md:w-1/3 mt-3 md:mt-0 ml-0 bg-white">
                    <div class="flex flex-col ml-4 w-full">
                        <p class="text-gray-600 text-xl">Today Sales (RM)</p>
                        <p class="text-2xl">{{
                                todaySales.toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                })
                            }}</p>
                    </div>
                    <div class="p-4 bg-red-100 flex items-center justify-center rounded-xl">
                        <font-awesome-icon icon="money-bill-1" class="text-lg text-red-500"/>
                    </div>
                </div>
                <div
                    class="py-4 px-6 rounded-2xl shadow-sm flex items-center border-2 border-gray-100 md:w-1/3 mt-3 md:mt-0 ml-0 md:ml-3 bg-white">
                    <div class="flex flex-col ml-4 w-full">
                        <p class="text-gray-600 text-xl">Pending Orders</p>
                        <p class="text-2xl">{{ pendingOrders.length }}</p>
                    </div>
                    <div class="p-4 bg-yellow-100 flex items-center justify-center rounded-xl">
                        <font-awesome-icon icon="boxes" class="text-lg text-yellow-500"/>
                    </div>
                </div>
                <div
                    class="py-4 px-6 rounded-2xl shadow-sm flex items-center border-2 border-gray-100 md:w-1/3 mt-3 md:mt-0 ml-0 md:ml-3 bg-white">
                    <div class="flex flex-col ml-4 w-full">
                        <p class="text-gray-600 text-xl">Inventory</p>
                        <p class="text-2xl">Item 1</p>
                    </div>
                    <div class="p-4 bg-blue-100 flex items-center justify-center rounded-xl">
                        <font-awesome-icon icon="money-bill-1" class="text-lg text-blue-500"/>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
