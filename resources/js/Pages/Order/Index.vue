<script setup>

import {Head, Link} from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {reactive, ref} from "vue";
import {FwbDropdown, FwbListGroup, FwbListGroupItem} from "flowbite-vue";

const props = defineProps({
    orders: Object,
    delivery_methods: Object
})


let orders = ref(props.orders)
let status = ref('all')
let search = ref('')

function changeStatus(selectedStatus) {
    selectedStatus = selectedStatus.toLowerCase()
    status.value = selectedStatus
    if (selectedStatus !== 'all') {
        orders = props.orders.filter((order) => {
            return order.status.toLowerCase() === selectedStatus
        })
    } else {
        orders = props.orders
    }
}

function searchOrder() {
    if (search.value !== '') {
        orders = props.orders.filter((order) => {
            return order.id.toString().includes(search.value)
        })
    } else {
        if (status.value !== 'all') {
            changeStatus(status.value)
        } else {
            orders = props.orders
        }
    }
}

function filterByDeliveryMethod(methodID) {
    orders = props.orders.filter((order) => {
        return parseInt(order.delivery_method_id) === parseInt(methodID)
    })
}
</script>

<template>

    <Head title="Orders"/>

    <AuthenticatedLayout>
        <Breadcrumb></Breadcrumb>
        <div class="py-0">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="block md:flex md:items-center py-4 mx-6 border-b-2 border-gray-100">
                        <div class="flex items-center" id="status-bar">
                            <p class="status-item" :class="status === 'all' ? 'active-status' : ''"
                               @click="changeStatus('all')">All</p>
                            <p class="status-item" :class="status === 'completed' ? 'active-status' : ''"
                               @click="changeStatus('completed')">Completed</p>
                            <p class="status-item" :class="status === 'pending' ? 'active-status' : ''"
                               @click="changeStatus('pending')">Pending</p>
                            <p class="status-item" :class="status === 'in transit' ? 'active-status' : ''"
                               @click="changeStatus('in transit')">In Transit</p>
                            <p class="status-item" :class="status === 'return' ? 'active-status' : ''"
                               @click="changeStatus('return')">Return</p>
                            <p class="status-item" :class="status === 'cancelled' ? 'active-status' : ''"
                               @click="changeStatus('cancelled')">Cancelled</p>
                        </div>
                    </div>
                    <div class="pt-3 pb-6 px-6 min-h-[500px]">
                        <div class="md:flex items-center mb-3">
                            <p class="text-2xl">{{ orders.length }} orders</p>
                            <VueDatePicker class="md:ml-auto mt-2 md:mt-0" range></VueDatePicker>
                            <fwb-dropdown text="Delivery Method" placement="bottom"
                                          class="mt-2 md:mt-0 ml-2 w-full md:w-auto">
                                <template #trigger>
                                <span
                                    class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2 flex items-center">
                                    Delivery Method
                                    <font-awesome-icon icon="angle-down" class="ml-2"/>
                                </span>
                                </template>
                                <fwb-list-group>
                                    <fwb-list-group-item v-for="(method, index) in delivery_methods" :key="index"
                                                         @click="filterByDeliveryMethod(method.id)">
                                        {{ method.delivery_method }}
                                    </fwb-list-group-item>
                                </fwb-list-group>
                            </fwb-dropdown>
                            <button type="button"
                                    class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2 w-full md:w-auto mt-2 md:mt-0 md:ml-2">
                                Export
                            </button>
                            <button type="button"
                                    class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2 w-full md:w-auto mt-2 md:mt-0 md:ml-2 md:flex md:items-center">
                                <font-awesome-icon icon="plus-circle" class="mr-2"/>
                                New Order
                            </button>
                        </div>
                        <div class="md:flex md:items-center">
                            <div id="search-bar" class="w-full">
                                <label for="search" class="text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                        </svg>
                                    </div>
                                    <input type="search" id="search"
                                           class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 "
                                           placeholder="Search Order ID" @keyup="searchOrder"
                                           v-model="search">
                                </div>
                            </div>
                        </div>
                        <div class="relative overflow-x-auto sm:rounded-lg mt-4">
                            <table
                                class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border-2 border-gray-100">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Order No.
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Seller
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Customer
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Delivery Method
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Total
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Action
                                    </th>
                                </tr>
                                </thead>
                                <tbody v-if="orders.length > 0">
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                    v-for="(order, index) in orders" :key="index">
                                    <th scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <p class="font-semibold"># {{ order.order_no }}</p>
                                        <p class="px-2 bg-blue-200 rounded-md text-sm">{{order.delivery_no}}</p>
                                    </th>
                                    <td scope="row" class="px-6 py-4 font-semibold">
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
                                    </td>
                                    <td scope="row" class="px-6 py-4">
                                        {{ order.seller.name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <p>{{ order.customer.name }}</p>
                                        <p>{{ order.customer.address }}</p>
                                    </td>
                                    <td class="px-6 py-4">{{ order.delivery_method.delivery_method }}</td>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold">RM{{ parseFloat(order.total_price).toFixed(2) }}</p>
                                        <p>
                                            Total {{ parseInt(order.details.length) }}
                                            item{{ parseInt(order.details.length) > 1 ? 's' : '' }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <div class="flex items-center">
                                            <Link :href="route('order.show', order.id)"
                                                class="focus:outline-none text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-1">
                                                View Details
                                            </Link>
                                            <font-awesome-icon icon="check-circle"
                                                               class="ml-3 text-green-500 hover:text-green-900 order-action-btn"
                                                               title="Complete"/>
                                            <font-awesome-icon icon="truck"
                                                               class="ml-3 text-blue-500 hover:text-blue-900 order-action-btn"
                                                               title="In Transit"/>
                                            <font-awesome-icon icon="rotate-left"
                                                               class="ml-3 text-gray-600 hover:text-gray-900 order-action-btn"
                                                               title="Return"/>
                                            <font-awesome-icon icon="times"
                                                               class="ml-3 text-red-600 hover:text-red-900 order-action-btn"
                                                               title="Cancel"/>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <th scope="row" colspan="6"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">
                                        No orders found
                                    </th>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.status-item {
    padding-left: 16px;
    padding-right: 16px;
}

.status-item.active-status {
    color: red;
}

.status-item:hover {
    color: #ef4444;;
    cursor: pointer;
}

.order-action-btn {
    font-size: 24px;
}

.order-action-btn:hover {
    cursor: pointer;
}

.dp__main {
    width: 300px;
}

@media only screen and (max-width: 600px) {
    .dp__main {
        width: 100%;
    }
}
</style>
