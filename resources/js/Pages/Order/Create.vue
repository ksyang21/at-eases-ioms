<script setup>

import {Head, Link, router} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {inject, reactive, ref} from "vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const Swal = inject('$swal')

const props = defineProps({
    products: Object,
})

let cart = reactive({
    products: []
})

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
        cart.products.push({
            product_id: productID.value,
            quantity: quantity.value
        })

        let totalPriceForAddedProduct = 0;
        for (let item of selectedProduct.value.packages) {
            if (quantity.value >= item.details.quantity) {
                // Use the entire package
                totalPriceForAddedProduct += parseFloat(item.details.price) * item.details.quantity;
                quantity.value -= item.details.quantity;
            } else if (quantity.value > 0) {
                // Use part of the package
                totalPriceForAddedProduct += parseFloat(item.details.price) * quantity.value;
                quantity.value = 0;
            }

            if (quantity.value === 0) {
                break; // We've used up the desired quantity
            }
        }

        // Update the total price
        totalPrice.value += totalPriceForAddedProduct;
    }
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
                                <font-awesome-icon icon="cart-shopping" class="mr-2"></font-awesome-icon>
                                {{ cart.products.length }} items
                            </div>
                            <div class="flex items-center">
                                <font-awesome-icon icon="sack-dollar" class="mr-2"></font-awesome-icon>
                                RM {{ totalPrice.toFixed(2) }}
                            </div>
                        </div>
                    </div>
                    {{selectedProduct.packages}}
                    <form @submit.prevent="submitForm">
                        <div class="grid gap-6 md:grid-cols-2 border-b-2 border-gray-100">
                            <div class="p-6 mx-2">
                                <div id="select-products-section">
                                    <label for="products"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select a
                                        product</label>
                                    <select id="products" @change="getProductPackage"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                            v-model="productID">
                                        <option selected>Choose a product</option>
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
                                <div class="relative overflow-x-auto sm:rounded-lg mt-4">
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
                                            <td class="px-6 py-4">{{ item.package.name }}</td>
                                            <td class="px-6 py-4">{{ item.details.quantity }}</td>
                                            <td class="px-6 py-4">RM{{ item.details.price }}</td>
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
