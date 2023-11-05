<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {reactive, ref} from "vue";
import {Link} from "@inertiajs/vue3";

let listView = ref(false)

let search = ref('')

const props = [
    {
        id: 1,
        name: 'Item 1',
        price: '20.0',
        description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam',
        pv: 10
    },
    {
        id: 1,
        name: 'Item 1',
        price: '20.0',
        description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam',
        pv: 20
    },
]

let products = reactive(props)

function changeToListView() {
    listView.value = true
}

function changeToTileView() {
    listView.value = false
}

function searchProduct() {
    console.log(search.value)
}
</script>

<template>
    <Head title="Products"/>

    <AuthenticatedLayout>
        <!--                <template #header>-->
        <!--                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>-->
        <!--                </template>-->
        <Breadcrumb></Breadcrumb>
        <div class="py-0">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
                        <div class="flex items-center">
                            <p class="p-2 change-view-btn" :class="!listView ? 'active-view' : ''"
                               @click="changeToTileView()"
                            >
                                <font-awesome-icon icon="table-cells-large"></font-awesome-icon>
                            </p>
                            <p>|</p>
                            <p class="p-2 change-view-btn" :class="listView ? 'active-view' : ''"
                               @click="changeToListView()">
                                <font-awesome-icon icon="list"></font-awesome-icon>
                            </p>
                        </div>
                        <button type="button"
                                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 ml-auto flex items-center">
                            <font-awesome-icon icon="plus-circle" class="mr-2"/>
                            New Product
                        </button>
                    </div>
                    <div class="p-6">
                        <div id="search-bar" class="mb-4">
                            <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input type="search" id="search"
                                       class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 "
                                       placeholder="Search product name / description" @keyup="searchProduct"
                                       v-model="search">
                            </div>
                        </div>
                        <div v-show="!listView" class="md:flex md:items-center w-full">
                            <div
                                class="flex flex-col bg-gray-200 rounded-lg w-full md:w-1/2 lg:w-1/4 h-full mt-2 md:mt-0 product-card border-2 border-gray-100"
                                v-for="(product, index) in products" :key="index">
                                <div class="relative">
                                    <img src="../../../images/coke.jpeg" alt="coke" class="rounded w-full">
                                    <p class="pv text-2xl shadow-lg px-3 py-1 bg-red-500 text-white rounded-md">{{parseInt(product.pv)}} PV</p>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center mt-3">
                                        <p class="font-semibold text-xl">{{ product.name }}</p>
                                        <p class="text-gray-600 text-xl ml-auto">
                                            RM{{ parseFloat(product.price).toFixed(2) }}
                                        </p>
                                    </div>
                                    <p class="text-sm mt-2">{{ product.description }}</p>
                                    <div class="flex items-center mt-2">
                                        <div class="ml-auto">
                                            <font-awesome-icon icon="pen-to-square" class="ml-2 product-action-btn"/>
                                            <Link :href="route('package.index', product.id)">
                                                <font-awesome-icon icon="box-open" class="ml-2 product-action-btn"/>
                                            </Link>
                                            <font-awesome-icon icon="warehouse" class="ml-2 product-action-btn"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-show="listView">
                            <div class="relative overflow-x-auto sm:rounded-lg">
                                <table
                                    class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border-2 border-gray-100">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">
                                            Product name
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Price
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Action
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                        v-for="(product, index) in products" :key="index">
                                        <th scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            <div class="flex">
                                                <img src="../../../images/coke.jpeg" alt="coke" class="list-view-img">
                                                <div class="flex flex-col">
                                                    <p class="text-2xl font-semibold">{{ product.name }}</p>
                                                    <p>{{ product.description }}</p>
                                                </div>
                                            </div>
                                        </th>
                                        <td class="px-6 py-4">
                                            RM{{ parseFloat(product.price).toFixed(2) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <font-awesome-icon icon="pen-to-square" class="ml-2 product-action-btn"/>
                                            <font-awesome-icon icon="box-open" class="ml-2 product-action-btn"/>
                                            <font-awesome-icon icon="warehouse" class="ml-2 product-action-btn"/>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<style scoped>
.change-view-btn:hover {
    cursor: pointer;
    color: #a8a8a8;
}

.product-action-btn:hover {
    color: #d71d28;
    cursor: pointer;
}

.active-view {
    color: #d71d28;
    font-weight: bold;
}

.product-card + .product-card:not(:first-child) {
    margin-left: 20px;
}

.list-view-img {
    width: 50px;
    height: 50px;
    object-fit: contain;
    margin-right: 16px;
    margin-left: -8px;
}

.pv {
    position: absolute;
    top: 8px;
    right: 8px;
}
</style>
