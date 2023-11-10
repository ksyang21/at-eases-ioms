<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {reactive, ref} from "vue";
import {Link} from "@inertiajs/vue3";

let listView = ref(false)

let search = ref('')

const props = defineProps({
    products: Object
})

let products = ref(props.products)

function changeToListView() {
    listView.value = true
}

function changeToTileView() {
    listView.value = false
}

function searchProduct() {
    if(search.value !== '') {
        products.value = props.products.filter((product) => {
            return product.name.toLowerCase().includes(search.value) || product.description.toLowerCase().includes(search.value)
        })
    } else {
        products.value = props.products
    }
}

function showImage() {
    return '/storage/'
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
                        <Link :href="route('product.create')"
                              class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 ml-auto flex items-center">
                            <font-awesome-icon icon="plus-circle" class="mr-2"/>
                            New Product
                        </Link>
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
                            <div class="grid gap-3 mb-6 md:grid-cols-4" v-if="products.length > 0">
                                <div
                                    class="flex flex-col rounded-lg w-full h-full mt-2 md:mt-0 product-card border-2 border-gray-100"
                                    :class="product.status === 'inactive' ? 'bg-gray-300' : ''"
                                    v-for="(product, index) in products" :key="index">
                                    <div class="flex items-center py-2 px-4 border-b-2 border-gray-100">
                                        <p>Stock : <span class="text-gray-600">{{ product.stock_quantity }}</span></p>
                                        <div class="ml-auto">
                                            <Link :href="route('product.edit', product.id)">
                                                <font-awesome-icon icon="pen-to-square"
                                                                   class="ml-2 product-action-btn text-blue-700"/>
                                            </Link>
                                            <Link :href="route('package.index', product.id)">
                                                <font-awesome-icon icon="box-open"
                                                                   class="ml-2 product-action-btn text-orange-400"/>
                                            </Link>
                                            <Link :href="route('inventoryLogs.index', product.id)">
                                                <font-awesome-icon icon="warehouse" class="ml-2 product-action-btn"/>
                                            </Link>
                                        </div>
                                    </div>
                                    <div class="relative">
                                        <img
                                            :src="product.image ? showImage() + product.image : '/storage/image/no-image.jpg'"
                                            :alt="product.name"
                                            class="rounded w-full tile-view-img border-b-2 border-gray-100"
                                            :class="product.status === 'inactive' ? 'inactive-product' : ''"
                                        >
                                        <p class="pv text-2xl shadow-lg px-3 py-1 bg-red-500 text-white rounded-md"
                                           v-if="product.status === 'active'">
                                            {{ parseInt(product.pv) }} PV</p>
                                    </div>
                                    <div class="p-4">
                                        <div class="flex items-center mt-3">
                                            <p class="font-semibold text-xl" v-if="product.status === 'active'">
                                                <font-awesome-icon icon="check-circle" class="text-green-700"/>
                                                {{ product.name }}
                                            </p>
                                            <div class="flex flex-col items-start" v-else>
                                                <span class="px-2.5 bg-gray-400 rounded-md text-sm mr-2">Inactive</span>
                                                <p class="font-semibold text-xl">
                                                    {{ product.name }}
                                                </p></div>
                                            <p class="text-gray-600 text-xl ml-auto">
                                                RM{{ parseFloat(product.price).toFixed(2) }}
                                            </p>
                                        </div>
                                        <p class="text-sm mt-2" v-if="product.description">{{ product.description }}</p>
                                        <p class="text-sm mt-2 text-gray-600" v-else>No Description</p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="w-full text-center py-6">
                                <p class="text-3xl">No result found</p>
                            </div>
                        </div>
                        <div v-show="listView">
                            <div v-if="products.length > 0" class="relative overflow-x-auto sm:rounded-lg">
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
                                            Stock
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Action
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class=" border-b hover:bg-gray-50"
                                        :class="product.status === 'inactive' ? 'bg-gray-300' : 'bg-white'"
                                        v-for="(product, index) in products" :key="index">
                                        <th scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            <div class="flex">
                                                <img
                                                    :src="product.image ? showImage() + product.image : '/storage/image/no-image.jpg'"
                                                    :alt="product.name"
                                                    class="list-view-img"
                                                    :class="product.status === 'inactive' ? 'inactive-product' : ''">
                                                <div class="flex flex-col">
                                                    <p class="text-2xl font-semibold" v-if="product.status==='active'">
                                                        {{ product.name }}</p>
                                                    <div class="flex flex-col items-start" v-else>
                                                        <span
                                                            class="px-2.5 bg-gray-400 rounded-md mr-2 font-normal text-sm ">Inactive</span>
                                                        <p class="text-2xl font-semibold">
                                                            {{ product.name }}
                                                        </p>
                                                    </div>
                                                    <p>{{ product.description }}</p>
                                                </div>
                                            </div>
                                        </th>
                                        <td class="px-6 py-4">
                                            RM{{ parseFloat(product.price).toFixed(2) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ product.stock_quantity }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <Link :href="route('product.edit', product.id)">
                                                <font-awesome-icon icon="pen-to-square"
                                                                   class="ml-2 product-action-btn text-blue-700"/>
                                            </Link>
                                            <Link :href="route('package.index', product.id)">
                                                <font-awesome-icon icon="box-open"
                                                                   class="ml-2 product-action-btn text-orange-400"/>
                                            </Link>
                                            <Link :href="route('inventoryLogs.index', product.id)">
                                                <font-awesome-icon icon="warehouse" class="ml-2 product-action-btn"/>
                                            </Link>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else class="w-full text-center py-6">
                                <p class="text-3xl">No result found</p>
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

.tile-view-img {
    height: 300px;
    width: 100%;
    object-fit: fill;
}

.inactive-product {
    filter: grayscale(100%);
}
</style>
