<script setup>

import {Head, Link, router} from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {inject, reactive, ref} from "vue";
import {FwbDropdown, FwbListGroup, FwbListGroupItem} from "flowbite-vue";
import moment from "moment/moment.js";

const props = defineProps({
    orders: Object,
    delivery_methods: Object,
    breadcrumbs: Object,
    today_orders: Object,
    today_order_amount: Number,
    earliest_order_date: String,
    latest_order_date: String,
})

const Swal = inject('$swal')

let orders = ref(props.orders)

let status = ref('all')
let search = ref('')
let deliveryMethod = ref('all')

let startDate = ref(new Date(props.earliest_order_date))
let endDate = ref(new Date(props.latest_order_date))
let date = ref([startDate.value, endDate.value])

const formatDate = ([date1, date2]) => {
    const startDay = date1.getDate()
    const startMonth = date1.getMonth() + 1
    const startYear = date1.getFullYear()
    if (!date2) {
        return `${startDay}/${startMonth}/${startYear}`
    }
    const endDay = date2.getDate()
    const endMonth = date2.getMonth() + 1
    const endYear = date2.getFullYear()

    return `${startDay}/${startMonth}/${startYear} - ${endDay}/${endMonth}/${endYear}`
}


let totalOrderSales = ref(0.00)

function calculateTotalOrderSales() {
    totalOrderSales.value = 0.00
    for (let order of orders.value) {
        totalOrderSales.value += parseFloat(order.total_price)
    }
}

calculateTotalOrderSales()


function filterOrders() {
    let filterStartDate = date.value[0].setHours(0, 0, 0, 0)
    let filterEndDate = date.value[1].setHours(23, 59, 59, 59)
    orders.value = props.orders.filter((order) => {
        const orderNoFilter = search.value !== '' ? order.order_no.toLowerCase().includes(search.value) : true
        const deliveryFilter = deliveryMethod.value !== 'all' ? order.delivery_method_id === deliveryMethod.value.id : true
        const statusFilter = status.value !== 'all' ? order.status === status.value : true
        const dateFilter = moment(filterStartDate).unix() !== moment(startDate.value).unix() || moment(filterEndDate).unix() !== moment(endDate.value).unix() ? moment(order.created_at).unix() >= moment(filterStartDate).unix() && moment(order.created_at).unix() <= moment(filterEndDate).unix() : true
        return orderNoFilter && deliveryFilter && statusFilter && dateFilter
    })
}

function changeStatus(selectedStatus) {
    selectedStatus = selectedStatus.toLowerCase()
    status.value = selectedStatus
    filterOrders()
    // if (selectedStatus !== 'all') {
    //     orders.value = props.orders.filter((order) => {
    //         return order.status.toLowerCase() === selectedStatus
    //     })
    // } else {
    //     orders.value = props.orders
    // }
}

function filterByDeliveryMethod(methodID) {
    if (methodID > 0) {
        deliveryMethod.value = props.delivery_methods.find((option) => {
            return option.id === methodID
        })
        filterOrders()
    } else {
        deliveryMethod.value = 'all'
        filterOrders()
    }
    calculateTotalOrderSales()
}

function approveOrder(order) {
    Swal.fire({
        text: `Approve Order ${order.order_no}?`,
        icon: 'info',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(`/approve-order/${order.id}`)
        }
    })
}

function cancelOrder(order) {
    Swal.fire({
        text: `Cancel Order ${order.order_no}?`,
        icon: 'info',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(`/cancel-order/${order.id}`)
        }
    })
}

function inTransitOrder(order) {
    Swal.fire({
        text: `Update Order ${order.order_no} to 'In Transit'?`,
        icon: 'info',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(`/in-transit-order/${order.id}`)
        }
    })
}

function returnOrder(order) {
    Swal.fire({
        text: `Return Order ${order.order_no}?`,
        icon: 'info',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(`/return-order/${order.id}`)
        }
    })
}

function completeOrder(order) {
    Swal.fire({
        text: `Complete Order ${order.order_no}?`,
        icon: 'info',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(`/complete-order/${order.id}`)
        }
    })
}

function rejectOrder(order) {
    Swal.fire({
        text: `Reject Order ${order.order_no}?`,
        icon: 'info',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(`/reject-order/${order.id}`)
        }
    })
}

function changeDate() {
    filterOrders()
}

/**
 * Bulk selection
 */
let multiselectMode = ref(false)
let multiselectStatus = ref('')
let multiselected = reactive({
    orders: []
})
function toggleMultiselect() {
    multiselectMode.value = !multiselectMode.value
}

function selectRow(order) {
    multiselected.orders.push(order)
    multiselectStatus.value = order.status
}

function confirmMultiselect() {
    console.log(multiselected)
}
</script>

<template>

    <Head title="Orders"/>

    <AuthenticatedLayout>
        <Breadcrumb :breadcrumbs="breadcrumbs"></Breadcrumb>
        <div class="py-0">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="block md:flex md:items-center py-4 mx-6 border-b-2 border-gray-100">
                        <div class="flex items-center" id="status-bar">
                            <p class="status-item" :class="status === 'all' ? 'active-status' : ''"
                               @click="changeStatus('all')">All</p>
                            <p class="status-item" :class="status === 'pending' ? 'active-status' : ''"
                               @click="changeStatus('pending')">Pending</p>
                            <p class="status-item" :class="status === 'approved' ? 'active-status' : ''"
                               @click="changeStatus('approved')">Approved</p>
                            <p class="status-item" :class="status === 'in transit' ? 'active-status' : ''"
                               @click="changeStatus('in transit')">In Transit</p>
                            <p class="status-item" :class="status === 'completed' ? 'active-status' : ''"
                               @click="changeStatus('completed')">Completed</p>
                            <p class="status-item" :class="status === 'return' ? 'active-status' : ''"
                               @click="changeStatus('return')">Return</p>
                            <p class="status-item" :class="status === 'cancelled' ? 'active-status' : ''"
                               @click="changeStatus('cancelled')">Cancelled</p>
                            <p class="status-item" :class="status === 'rejected' ? 'active-status' : ''"
                               @click="changeStatus('rejected')">Rejected</p>
                        </div>
                    </div>
                    <div class="pt-3 pb-6 px-6 min-h-[500px]">
                        <div class="md:flex md:items-center mt-3">
                            <div id="search-bar">
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
                                           placeholder="Search Order ID" @keyup="filterOrders"
                                           v-model="search">
                                </div>
                            </div>
                            <VueDatePicker
                                class="md:ml-auto mt-2 md:mt-0 min-w-[350px]"
                                range
                                v-model="date"
                                :min-date="startDate"
                                :max-date="endDate"
                                :format="formatDate"
                                :enable-time-picker="false"
                                :clearable="false"
                                @update:model-value="changeDate"
                            />
                            <fwb-dropdown text="Delivery Method" placement="bottom"
                                          class="mt-2 md:mt-0 ml-0 md:ml-2 w-full md:w-auto">
                                <template #trigger>
                                <span
                                    class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2 flex items-center">
                                    Delivery Method
                                    <font-awesome-icon icon="angle-down" class="ml-2"/>
                                </span>
                                </template>
                                <fwb-list-group>
                                    <fwb-list-group-item @click="filterByDeliveryMethod(0)">All</fwb-list-group-item>
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
                            <Link :href="route('order.create')"
                                  class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2 w-full md:w-auto mt-2 md:mt-0 md:ml-2 md:flex md:items-center w-full">
                                <font-awesome-icon icon="plus-circle" class="mr-2"/>
                                New Order
                            </Link>
                        </div>
                        <div class="py-6 block md:flex md:items-center">
                            <div
                                class="py-4 px-6 rounded-lg shadow-sm flex items-center border-2 border-gray-100 md:w-1/4">
                                <div class="p-4 bg-red-100 flex items-center justify-center rounded-xl">
                                    <font-awesome-icon icon="boxes" class="text-lg text-red-500"/>
                                </div>
                                <div class="flex flex-col ml-4 items-end w-full">
                                    <p class="text-gray-600 text-xl">Today Orders</p>
                                    <p class="text-2xl">{{ today_orders.length }}</p>
                                </div>
                            </div>
                            <div
                                class="py-4 px-6 rounded-lg shadow-sm flex items-center border-2 border-gray-100 md:w-1/4 mt-3 md:mt-0 ml-0 md:ml-3">
                                <div class="p-4 bg-red-100 flex items-center justify-center rounded-xl">
                                    <font-awesome-icon icon="money-bill-1" class="text-lg text-red-500"/>
                                </div>
                                <div class="flex flex-col ml-4 items-end w-full">
                                    <p class="text-gray-600 text-xl">Today Sales (RM)</p>
                                    <p class="text-2xl">{{
                                            today_order_amount.toLocaleString(undefined, {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            })
                                        }}</p>
                                </div>
                            </div>
                            <div
                                class="py-4 px-6 rounded-lg shadow-sm flex items-center border-2 border-gray-100 md:w-1/4 mt-3 md:mt-0 ml-0 md:ml-3">
                                <div class="p-4 bg-yellow-100 flex items-center justify-center rounded-xl">
                                    <font-awesome-icon icon="circle-dollar-to-slot" class="text-lg text-yellow-500"/>
                                </div>
                                <div class="flex flex-col ml-4 items-end w-full">
                                    <p class="text-gray-600 text-xl">Total Orders</p>
                                    <p class="text-2xl">{{ orders.length }}</p>
                                </div>
                            </div>
                            <div
                                class="py-4 px-6 rounded-lg shadow-sm flex items-center border-2 border-gray-100 md:w-1/4 mt-3 md:mt-0 ml-0 md:ml-3">
                                <div class="p-4 bg-blue-100 flex items-center justify-center rounded-xl">
                                    <font-awesome-icon icon="sack-dollar" class="text-lg text-blue-500"
                                                       v-if="deliveryMethod === 'all'"/>
                                    <font-awesome-icon icon="truck-fast" class="text-lg text-blue-500" v-else/>
                                </div>
                                <div class="flex flex-col ml-4 items-end w-full">
                                    <p class="text-gray-600 text-xl" v-if="deliveryMethod === 'all'">Total Sales
                                        (RM)</p>
                                    <p class="text-gray-600 text-xl" v-else>Sales By Delivery (RM)</p>
                                    <p class="text-2xl">{{
                                            totalOrderSales.toLocaleString(undefined, {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            })
                                        }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-if="deliveryMethod !== 'all'">
                            Selected Delivery : <span class="text-gray-400">{{ deliveryMethod.delivery_method }}</span>
                        </div>
                        <div class="md:flex md:items-center">
                            <button type="button" @click="toggleMultiselect"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                Bulk Select
                            </button>
                            <button @click="confirmMultiselect">Confirm</button>
                        </div>
                        <div class="relative overflow-x-auto sm:rounded-lg mt-4">
                            <table
                                class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border-2 border-gray-100">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th></th>
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
                                    v-for="(order, index) in orders" :key="index" @click="selectRow(order)">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" :key="index" :value="order.id" v-show="multiselectMode"
                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2">
                                    </td>
                                    <th scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <p class="font-semibold"># {{ order.order_no }}</p>
                                        <p class="font-thin text-sm text-gray-600">
                                            {{ moment(order.created_at).format('YYYY-MM-DD HH:mm:ss') }}</p>
                                    </th>
                                    <td scope="row" class="px-6 py-4 font-semibold text-xs">
                                        <p v-if="order.status === 'approved'" class="text-green-600">
                                            •{{ order.status.toUpperCase() }}
                                        </p>
                                        <p v-if="order.status === 'completed'" class="text-green-700">
                                            •{{ order.status.toUpperCase() }}
                                        </p>
                                        <p v-if="order.status === 'pending'" class="text-blue-700">
                                            •{{ order.status.toUpperCase() }}
                                        </p>
                                        <p v-if="order.status === 'in transit'" class="text-yellow-400">
                                            •{{ order.status.toUpperCase() }}
                                        </p>
                                        <p v-if="order.status === 'return'" class="text-red-700">
                                            •{{ order.status.toUpperCase() }}
                                        </p>
                                        <p v-if="order.status === 'cancelled'" class="text-gray-700">
                                            •{{ order.status.toUpperCase() }}
                                        </p>
                                        <p v-if="order.status === 'rejected'" class="text-red-700">
                                            •{{ order.status.toUpperCase() }}
                                        </p>
                                    </td>
                                    <td scope="row" class="px-6 py-4">
                                        {{ order.seller.name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <p>{{ order.customer.name }}</p>
                                        <p>{{ order.customer.address }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p>{{
                                                order.delivery_method !== null ? order.delivery_method.delivery_method : '-'
                                            }}</p>
                                        <span class="px-2 bg-blue-200 rounded-md text-sm text-black"
                                              v-if="order.delivery_method !== null">#{{ order.delivery_no }}</span>
                                    </td>
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
                                                Details
                                            </Link>
                                            <font-awesome-icon icon="check-circle" v-if="order.status === 'in transit'"
                                                               @click="completeOrder(order)"
                                                               class="ml-3 text-green-500 hover:text-green-900 order-action-btn"
                                                               title="Complete"/>
                                            <font-awesome-icon icon="truck" v-if="order.status === 'approved'"
                                                               @click="inTransitOrder(order)"
                                                               class="ml-3 text-blue-500 hover:text-blue-900 order-action-btn"
                                                               title="In Transit"/>
                                            <font-awesome-icon icon="rotate-left"
                                                               v-if="order.status === 'completed' || order.status === 'in transit'"
                                                               @click="returnOrder(order)"
                                                               class="ml-3 text-gray-600 hover:text-gray-900 order-action-btn"
                                                               title="Return"/>
                                            <font-awesome-icon icon="check-circle" v-if="order.status === 'pending'"
                                                               @click="approveOrder(order)"
                                                               class="ml-3 text-green-600 hover:text-green-900 order-action-btn"
                                                               title="Approve"/>
                                            <font-awesome-icon icon="times" @click="cancelOrder(order)"
                                                               v-if="order.status !== 'pending' && order.status !== 'rejected' && order.status !== 'completed' && order.status !== 'cancelled' && order.status !== 'return'"
                                                               class="ml-3 text-red-600 hover:text-red-900 order-action-btn"
                                                               title="Cancel"/>
                                            <font-awesome-icon icon="ban" v-if="order.status === 'pending'"
                                                               @click="rejectOrder(order)"
                                                               class="ml-3 text-red-500 hover:text-red-900 order-action-btn"
                                                               title="Rejected"/>
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
