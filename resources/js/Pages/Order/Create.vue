<script setup>

import {Head, Link} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {inject, reactive, ref} from "vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const Swal = inject('$swal')

const props = defineProps({
    products: Object,
    customers: Object
})

let cart = reactive({
    customer_id: 0,
    products: []
})

let customer = {}

function getCustomerDetails() {
    customer = props.customers.find((c) => {
        return c.id === cart.customer_id
    })
}

let totalPrice = ref(0.00)

let canSubmit = ref(true)
let showError = ref(false)

function submitForm() {
    canSubmit.value = true
    showError.value = false

    // let allowedStatuses = ['stock in', 'stock out']
    // if (form.quantity <= 0 || !allowedStatuses.includes(form.stock_status)) {
    //     showError.value = true
    //     canSubmit.value = false
    // }
    //
    // if (canSubmit.value) {
    //     router.post(`/inventory-logs/${props.product.id}`, form)
    // }
}

let productID = ref(0)
let quantity = ref(0)
let selectedProduct = ref({})

function getProductPackage() {
    selectedProduct.value = props.products.find((product) => {
        return product.id === productID.value
    })
}

function addToCart() {
    let errorText = ''
    if (productID.value <= 0 || quantity.value <= 0) {
        if (productID.value <= 0) {
            errorText += 'Please select product first!'
        } else {
            errorText += 'Quantity is required!'
        }

        Swal.fire({
            text: errorText,
            icon: 'error'
        })
    } else {
        /**
         * Check if current cart has this item. If exists sum up the total quantity
         */
        let itemIsFound = false
        for (let item of cart.products) {
            if (item.product_id === productID.value) {
                itemIsFound = true
                item.quantity += quantity.value
                break;
            }
        }
        if (!itemIsFound) {
            cart.products.push({
                product_id: productID.value,
                name: selectedProduct.value.name,
                quantity: quantity.value,
            })
        }

        // Calculate total price for each product, and get overall total price
        totalPrice.value = 0.00
        for (let item of cart.products) {
            let product = props.products.find((p) => {
                return p.id === item.product_id
            })
            item.total_price = calculateProductPrice(item.quantity, product)

            totalPrice.value += item.total_price
        }
    }
}

function calculateProductPrice(quantity, product) {
    let sortedPackages = [...product.packages];

    let remainingQuantity = quantity;
    let price = 0;

    // Sort packages in descending order of quantity
    sortedPackages.sort((a, b) => b.quantity - a.quantity)

    for (const pack of sortedPackages) {
        const numberOfPackages = Math.floor(remainingQuantity / pack.quantity)

        if (numberOfPackages > 0) {
            price += numberOfPackages * parseFloat(pack.price)
            remainingQuantity -= numberOfPackages * pack.quantity
        }

        if (remainingQuantity === 0) {
            break
        }
    }

    // If there is remaining quantity, use the original price of the item
    if (remainingQuantity > 0) {
        price += remainingQuantity * parseFloat(product.price)
    }

    return price
}
</script>

<template>

    <Head title="Products"/>
    <AuthenticatedLayout>
        <Breadcrumb></Breadcrumb>
        <div class="py-0">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
                        <p class="text-2xl">Create New Order</p>
                        <div class="flex flex-col ml-auto">
                            <div class="flex items-center">
                                <font-awesome-icon icon="cart-shopping" class="mr-2 text-red-700"></font-awesome-icon>
                                {{ cart.products.length }} items
                            </div>
                            <div class="flex items-center">
                                <font-awesome-icon icon="sack-dollar" class="mr-2 text-blue-700"></font-awesome-icon>
                                RM {{ totalPrice.toFixed(2) }}
                            </div>
                        </div>
                    </div>
                    <form @submit.prevent="submitForm">
                        <div class="grid gap-6 md:grid-cols-2 border-b-2 border-gray-100">
                            <div class="p-6 mx-2">
                                <div id="select-customer-section">
                                    <label for="customers"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select
                                        customer</label>
                                    <select id="customers" @change="getCustomerDetails"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                            v-model="cart.customer_id">
                                        <option :value="customer.id" v-for="(customer, index) in customers"
                                                :key="index">
                                            {{ customer.name }}
                                        </option>
                                    </select>
                                    <div v-if="cart.customer_id > 0" class="text-sm">
                                        <p class="text-red-600">
                                            <font-awesome-icon icon="location-dot" class="mr-2"/>
                                            <span class="text-gray-600">{{ customer.address }}</span>
                                        </p>
                                        <p class="text-red-600">
                                            <font-awesome-icon icon="location-dot" class="mr-2"/>
                                            Customer Details
                                        </p>
                                        <div class="ml-[24px]">
                                            <p>{{ customer.phone }}</p>
                                            <p class="text-gray-500"></p>
                                        </div>
                                    </div>
                                </div>
                                <div id="select-products-section" class="mt-6">
                                    <label for="products"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select a
                                        product</label>
                                    <select id="products" @change="getProductPackage"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                            v-model="productID">
                                        <option :value="product.id" v-for="(product, index) in products"
                                                :key="index">
                                            {{ product.name }}
                                        </option>
                                    </select>
                                </div>
                                <div id="quantity-section" class="mt-6">
                                    <label for="quantity"
                                           class="block mb-2 text-sm font-medium text-gray-900">Quantity</label>
                                    <input type="number" id="quantity"
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                           placeholder="Quantity" required v-model="quantity">
                                </div>
                            </div>
                            <div class="p-6 mx-2">
                                <div class="relative overflow-x-auto sm:rounded-lg">
                                    <table class="w-full text-sm text-left text-gray-500 border-2 border-gray-200">
                                        <thead
                                            class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">
                                                Package
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Quantity
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Price
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody v-if="productID > 0">
                                        <tr v-for="(item, index) in selectedProduct.packages" :key="index"
                                            v-if="selectedProduct.packages.length > 0">
                                            <td class="px-6 py-4">{{ item.name }}</td>
                                            <td class="px-6 py-4 text-center">{{ item.quantity }}</td>
                                            <td class="px-6 py-4">RM {{ item.price }}</td>
                                        </tr>
                                        <tr v-else class="text-center">
                                            <td class="px-6 py-4" colspan="3">No package available</td>
                                        </tr>
                                        </tbody>
                                        <tbody v-else>
                                        <tr>
                                            <td colspan="43" class="px-6 py-4 text-center">Please select a product</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div class="mt-6 text-center flex" v-if="productID > 0">
                                        <button class="w-full bg-blue-500 text-white rounded-md py-1"
                                                @click="addToCart" type="button">
                                            <font-awesome-icon icon="plus-circle" class="mr-2"/>
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center">
                                <Link :href="route('orders.index')"
                                      class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                    <font-awesome-icon icon="angle-left" class="mr-1"/>
                                    Back to Order Listing
                                </Link>
                                <button
                                    class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-auto flex items-center">
                                    <font-awesome-icon icon="check-circle" class="mr-2"/>
                                    Create Order
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
