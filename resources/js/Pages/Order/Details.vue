<script setup>

import {FwbDropdown, FwbListGroup, FwbListGroupItem} from "flowbite-vue";
import {Head, Link} from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import moment from "moment/moment.js";

const props = defineProps({
    order: Object,
    seller: Object,
    customer: Object,
    delivery_method: Object,
    breadcrumbs: Object
})

let totalProductQuantity = 0
for (let detail of props.order.details) {
    totalProductQuantity += detail.quantity
}
</script>

<template>
    <Head title="Order Details"/>

    <AuthenticatedLayout>
        <Breadcrumb :breadcrumbs="breadcrumbs"></Breadcrumb>
        <div class="py-0">
            <!--            <div class="max-w-2xl mx-auto mt-10">-->
            <!--                <div class="relative">-->
            <!--                    &lt;!&ndash; Timeline Dot (Use the absolute position to position it) &ndash;&gt;-->
            <!--                    <div class="absolute w-5 h-5 bg-blue-500 rounded-full border-2 border-white left-0 top-1/2 transform -translate-y-1/2"></div>-->

            <!--                    &lt;!&ndash; Timeline Content (Use the margin-left to create spacing) &ndash;&gt;-->
            <!--                    <div class="ml-8 mb-6">-->
            <!--                        <h3 class="text-xl font-semibold text-gray-800">Event 1</h3>-->
            <!--                        <p class="text-gray-600">Description of Event 1</p>-->
            <!--                    </div>-->
            <!--                </div>-->

            <!--                <div class="relative">-->
            <!--                    &lt;!&ndash; Timeline Dot (Use the absolute position to position it) &ndash;&gt;-->
            <!--                    <div class="absolute w-5 h-5 bg-blue-500 rounded-full border-2 border-white left-0 top-1/2 transform -translate-y-1/2"></div>-->

            <!--                    &lt;!&ndash; Timeline Content (Use the margin-left to create spacing) &ndash;&gt;-->
            <!--                    <div class="ml-8 mb-6">-->
            <!--                        <h3 class="text-xl font-semibold text-gray-800">Event 2</h3>-->
            <!--                        <p class="text-gray-600">Description of Event 2</p>-->
            <!--                    </div>-->
            <!--                </div>-->

            <!--                &lt;!&ndash; Add more timeline items as needed &ndash;&gt;-->
            <!--            </div>-->

            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="block md:flex md:items-center py-4 mx-6 border-b-2 border-gray-100">
                        <div class="flex flex-col">
                            <p class="text-3xl font-semibold">#{{ order.order_no }}</p>
                            <p class="text-sm text-gray-600">Ordered at :
                                {{ moment(order.created_at).format('YYYY-MM-DD HH:mm:ss') }}</p>
                        </div>
                        <div class="ml-auto flex items-center">
                            <div id="status">
                                <p v-if="order.status === 'completed'" class="text-green-700">
                                    • {{ order.status.toUpperCase() }}
                                </p>
                                <p v-if="order.status === 'pending'" class="text-blue-700">
                                    • {{ order.status.toUpperCase() }}
                                </p>
                                <p v-if="order.status === 'in transit'" class="text-yellow-400">
                                    • {{ order.status.toUpperCase() }}
                                </p>
                                <p v-if="order.status === 'return'" class="text-red-700">
                                    • {{ order.status.toUpperCase() }}
                                </p>
                                <p v-if="order.status === 'cancelled'" class="text-gray-700">
                                    • {{ order.status.toUpperCase() }}
                                </p>
                                <p v-if="order.status === 'approved'" class="text-green-600">
                                    •{{ order.status.toUpperCase() }}
                                </p>
                                <p v-if="order.status === 'rejected'" class="text-red-700">
                                    •{{ order.status.toUpperCase() }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 mx-2 block md:flex border-b-2 border-gray-100">
                        <div class="w-full md:w-1/2 mx-6">
                            <div id="product-section">
                                <p class="text-red-600">
                                    <font-awesome-icon icon="boxes" class="mr-2"/>
                                    Product
                                </p>
                                <div class="relative overflow-x-auto sm:rounded-lg mt-4">
                                    <table
                                        class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border-2 border-gray-100">
                                        <thead
                                            class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">
                                                Product
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Quantity
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Price
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="bg-white border-b"
                                            v-for="(detail, index) in order.details" :key="index">
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <p class="font-semibold">{{ detail.product.name }}</p>
                                            </th>
                                            <td class="px-6 py-4">
                                                {{ detail.quantity }}
                                            </td>
                                            <td class="px-6 py-4">
                                                RM{{ parseFloat(detail.price).toFixed(2) }}
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot>
                                        <tr class="bg-gray-100">
                                            <td class="px-6 py-3">Total</td>
                                            <td class="px-6 py-3">
                                                {{ totalProductQuantity }}
                                            </td>
                                            <td class="px-6 py-3">RM{{
                                                    parseFloat(order.total_price).toFixed(2)
                                                }}
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="w-full md:w-1/2 px-4 mt-6 md:mt-0">
                            <div id="customer-section">
                                <p class="text-red-600">
                                    <font-awesome-icon icon="location-dot" class="mr-2"/>
                                    Delivery Address
                                </p>
                                <div class="ml-[24px]">
                                    <p>{{ customer.name }}</p>
                                    <p class="text-gray-500">{{ customer.address }}</p>
                                </div>
                            </div>
                            <div id="seller-section" class="mt-6">
                                <p class="text-red-600">
                                    <font-awesome-icon icon="user" class="mr-2"/>
                                    Seller
                                </p>
                                <div class="ml-[24px]">
                                    <p>{{ seller.name }}</p>
                                </div>
                            </div>
                            <div id="logistic-section" class="mt-6">
                                <p class="text-red-600">
                                    <font-awesome-icon icon="truck-fast" class="mr-2"/>
                                    Logistic Information
                                </p>
                                <div class="ml-[24px]">
                                    <div v-if="order.status !== 'pending' && order.status !== 'rejected' && order.status !== 'approved'" class="flex items-center`">
                                        <p>{{ delivery_method.delivery_method }}</p>
                                        <p class="px-2 bg-blue-200 rounded-md text-sm ml-2">#{{ order.delivery_no }}</p>
                                    </div>
                                    <div v-else>
                                        <p v-if="order.status === 'pending'">Pending approval</p>
                                        <p v-if="order.status === 'rejected'" class="text-red-900">Order Rejected</p>
                                        <p v-if="order.status === 'approved'" class="text-blue-700">Pending Shipment</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
                        <Link :href="route('orders.index')"
                              class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                            <font-awesome-icon icon="angle-left" class="mr-1"/>
                            Back to Orders
                        </Link>
                        <fwb-dropdown text="Delivery Method" placement="top"
                                      class="mt-2 md:mt-0 ml-auto w-full md:w-auto">
                            <template #trigger>
                                <span
                                    class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2 flex items-center">
                                    Update Status
                                    <font-awesome-icon icon="angle-down" class="ml-2"/>
                                </span>
                            </template>
                            <fwb-list-group>
                                <fwb-list-group-item>Pending</fwb-list-group-item>
                                <fwb-list-group-item>In Transit</fwb-list-group-item>
                                <fwb-list-group-item>Completed</fwb-list-group-item>
                                <fwb-list-group-item>Cancelled</fwb-list-group-item>
                                <fwb-list-group-item>Return</fwb-list-group-item>
                            </fwb-list-group>
                        </fwb-dropdown>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
