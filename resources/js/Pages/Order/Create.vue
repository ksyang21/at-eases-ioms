<script setup>

import {Head, Link, router, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {inject, reactive, ref, watch} from "vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

// Check if current user is Seller / Admin
let currentUserRole = usePage().props.auth.role
const Swal = inject('$swal')

const props = defineProps({
    products: Object,
    customers: Object,
    sellers: Object,
    postages: Object,
    breadcrumbs: Object,
})

// Re-structure sellers and customers for VueSelect
let sellerOptions = []
for (let seller of props.sellers) {
    sellerOptions.push({
        label: seller.name,
        code: seller.id
    })
}

let customerOptions = []
for (let customer of props.customers) {
    customerOptions.push({
        label: customer.name,
        code: customer.id
    })
}

let cart = reactive({
    seller_id: {label: 'Please select seller', code: 0},
    customer_id: {label: 'Please select customer', code: 0},
    products: []
})

let customer = {}
let customerPostage = {}
function getCustomerDetails(customerID) {
    customer = props.customers.find((c) => {
        return c.id === customerID
    })
    customerPostage = props.postages.find((p) => {
        return p.postcode === customer.postcode
    })
}

let seller = {}

function getSellerDetails(sellerID) {
    seller = props.sellers.find((s) => {
        return s.id === sellerID
    })
}

if (currentUserRole !== 'admin') {
    let currentSellerID = usePage().props.auth.user.id
    seller = props.sellers.find((s) => {
        return s.id === currentSellerID
    })
    cart.seller_id = {
        label: seller.name,
        code: currentSellerID
    }
}

// Watch for changes, as VueSelect does not support @change
watch(() => cart.customer_id, (selectedCustomer) => {
    const customerID = selectedCustomer.code
    getCustomerDetails(customerID)
})

watch(() => cart.seller_id, (selectedSeller) => {
    const sellerID = selectedSeller.code
    getSellerDetails(sellerID)
})


let totalPrice = ref(0.00)

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
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        })
        /**
         * Check if current cart has this item. If exists sum up the total quantity
         */
        let itemIsFound = false
        for (let item of cart.products) {
            if (item.product_id === productID.value) {
                itemIsFound = true
                let totalQuantity = item.quantity + quantity.value

                if (totalQuantity > selectedProduct.value.stock_quantity) {
                    Swal.fire({
                        title: `Not enough stock!`,
                        text: `${selectedProduct.value.name} stock left : ${selectedProduct.value.stock_quantity}`,
                        icon: 'error'
                    })
                } else {
                    item.quantity += quantity.value
                    Toast.fire({
                        icon: "success",
                        title: "Item quantity updated!"
                    });
                }
                break;
            }
        }
        if (!itemIsFound) {
            if (quantity.value <= selectedProduct.value.stock_quantity) {
                cart.products.push({
                    product_id: productID.value,
                    name: selectedProduct.value.name,
                    quantity: quantity.value,
                    product: selectedProduct.value
                })
                Toast.fire({
                    icon: "success",
                    title: "Item added to cart!"
                });
            } else {
                Swal.fire({
                    title: `Not enough stock!`,
                    text: `${selectedProduct.value.name} stock left : ${selectedProduct.value.stock_quantity}`,
                    icon: 'error'
                })
            }
        }

        calculateTotalPrice()

    }
}

function calculateTotalPrice() {
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

function confirmOrder() {
    let errorText = ''
    if (cart.products.length < 1 || cart.customer_id.code <= 0 || cart.seller_id.code <= 0) {
        if (cart.products.length < 1) {
            errorText += 'Empty cart!<br>'
        }
        if (cart.customer_id.code <= 0) {
            errorText += 'Please select customer first!<br>'
        }
        if (cart.seller_id.code <= 0) {
            errorText += 'Please select seller first!<br>'
        }

        Swal.fire({
            html: errorText,
            icon: 'error'
        })
    } else {
        Swal.fire({
            title: 'Confirm Order?',
            html: `Total : RM ${totalPrice.value.toFixed(2)}`,
            icon: 'info',
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                cart.seller_id = cart.seller_id.code
                cart.customer_id = cart.customer_id.code
                router.post('/order', cart)
            }
        })
    }
}


let isCartModalOpen = ref(false)

function openModal() {
    document.getElementById('cartModal').classList.remove('hidden')
    isCartModalOpen.value = true
}

function closeModal() {
    document.getElementById('cartModal').classList.add('hidden')
    isCartModalOpen.value = false
}

function showImage() {
    return '/storage/'
}

function reduceQuantity(product) {
    let tmpQuantity = product.quantity - 1
    if (tmpQuantity > 0) {
        product.quantity -= 1
        product.total_price = calculateProductPrice(product.quantity, product.product)
        calculateTotalPrice()
    } else {
        removeProduct(product)
    }
}

function addQuantity(product) {
    product.quantity += 1
    product.total_price = calculateProductPrice(product.quantity, product.product)
    calculateTotalPrice()
}

function updateCart(cartProduct, quantity, product) {
    cartProduct.total_price = calculateProductPrice(quantity, product)
    calculateTotalPrice()
}

function removeProduct(product) {
    Swal.fire({
        text: `Remove ${product.name} ?`,
        icon: 'warning',
        showCancelButton: true,
    }).then((result) => {
        if (result.isConfirmed) {
            const index = cart.products.indexOf(product)
            if (index !== -1) {
                cart.products.splice(index, 1)
            }
            calculateTotalPrice()
        }
    })
}
</script>

<template>

    <Head title="Products"/>
    <AuthenticatedLayout>
        <Breadcrumb :breadcrumbs="breadcrumbs"></Breadcrumb>
        <div class="py-0">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
                        <p class="text-2xl">New Order</p>
                        <p class="text-2xl ml-auto hidden md:flex">
                            Total : RM {{ totalPrice.toFixed(2) }}
                        </p>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2 border-b-2 border-gray-100">
                        <div class="p-6 mx-2">
                            <div v-if="$page.props.auth.role === 'admin'" id="select-seller-section">
                                <label for="sellers"
                                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select
                                    Seller</label>
                                <VueSelect :options="sellerOptions" v-model="cart.seller_id"/>
                                <div v-if="cart.seller_id.code > 0" class="text-sm mt-3">
                                    <p class="text-red-600">
                                        <font-awesome-icon icon="people-roof" class="mr-2"/>
                                        <span class="text-gray-600"
                                              v-if="seller.group_details !== null">{{
                                                seller.group_details.group.name
                                            }}</span>
                                        <span class="text-gray-600"
                                              v-else>{{ seller.name }} does not have a group</span>
                                    </p>
                                </div>
                            </div>
                            <div id="select-customer-section" class="mt-6">
                                <label for="customers"
                                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select
                                    customer</label>
                                <VueSelect :options="customerOptions" v-model="cart.customer_id"
                                           @input="getCustomerDetails"/>
                                <div v-if="cart.customer_id.code > 0" class="text-sm mt-3">
                                    <p class="text-red-600">
                                        <font-awesome-icon icon="phone" class="mr-2"/>
                                        <span class="text-gray-600">{{ customer.phone }}</span>
                                    </p>
                                    <p class="text-red-600">
                                        <font-awesome-icon icon="location-dot" class="mr-2"/>
                                        <span class="text-gray-600">{{ customer.address }}</span>
                                    </p>
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
                                <div v-if="selectedProduct.id > 0" class="text-sm mt-3">
                                    <p class="text-red-600">
                                        <font-awesome-icon icon="dollar" class="mr-2"/>
                                        <span class="text-gray-600">RM {{
                                                parseFloat(selectedProduct.price).toFixed(2)
                                            }}</span>
                                    </p>
                                    <p class="text-red-600">
                                        <font-awesome-icon icon="box" class="mr-2"/>
                                        <span class="text-gray-600">{{ selectedProduct.stock_quantity }}</span>
                                    </p>
                                </div>
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
                                        Add Item
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
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-auto"
                                    @click="openModal">
                                Summary
                                <span class="ml-2 px-1.5 bg-white rounded-sm text-black">{{
                                        cart.products.length
                                    }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Cart Modal container-->
        <div v-if="isCartModalOpen" class="fixed inset-0 bg-gray-800 bg-opacity-50"></div>
        <div id="cartModal" class="w-full md:w-1/2 hidden fixed inset-y-0 right-0 bg-white flex flex-col shadow-2xl">
            <div class="top-0 p-6 border-b-2 border-gray-100 flex items-center">
                <p class="text-2xl">Cart</p>
                <button class="text-red-600 ml-auto" @click="closeModal">
                    <font-awesome-icon icon="times-circle"/>
                </button>
            </div>
            <div class="bg-white p-6 rounded-lg">
                <div v-if="cart.products.length === 0" class="py-6 text-2xl flex justify-center">
                    <img src="/storage/system/empty-cart.png" alt="Cart is Empty.">
                </div>
                <div v-else class="pb-6">
                    <div class="py-6 border-b-2 border-gray-100"
                         v-for="(product, index) in cart.products" :key="index">
                        <div class="flex items-center">
                            <img
                                :src="product.product.image !== null ? showImage() + product.product.image : showImage() + 'image/no-image.jpg'"
                                alt="product" class="max-h-[120px] max-w-[120px]">
                            <div class="flex flex-col ml-6">
                                <p class="text-xl font-semibold">{{ product.name }}</p>
                            </div>
                            <span class="input-wrapper mt-2 ml-auto">
                              <button @click="reduceQuantity(product)" v-if="product.quantity > 0">-</button>
                              <input type="number" v-model="product.quantity" class="border-2 border-gray-300"
                                     @change="updateCart(product, product.quantity, product.product)" min="0"
                                     :max="product.quantity"/>
                              <button @click="addQuantity(product)"
                                      v-if="product.quantity < product.product.stock_quantity">+</button>
                            </span>
                            <div class="ml-auto items-center hidden md:flex">
                                <p class="text-2xl">RM {{ product.total_price.toFixed(2) }}</p>
                                <p class="ml-4 text-red-600 hover:text-red-900 cursor-pointer"
                                   @click="removeProduct(product)">
                                    <font-awesome-icon icon="trash"/>
                                </p>
                            </div>
                        </div>
                        <div class="items-center flex md:hidden text-center mt-3">
                            <button class="bg-red-600 text-white hover:bg-red-900 cursor-pointer py-1 px-4 rounded-md "
                                    @click="removeProduct(product)">
                                <font-awesome-icon icon="trash"/>
                            </button>
                            <p class="text-2xl ml-auto">RM {{ product.total_price.toFixed(2) }}</p>
                        </div>
                    </div>
                    <div class="py-6 px-6 flex flex-col items-end border border-gray-200">
                        <p class="text-xl mb-2">Total Items : {{ cart.products.length }}</p>
                        <p class="text-xl mb-2">Sub-total : RM {{ totalPrice.toFixed(2) }}</p>
                        <p class="text-xl mb-2">Delivery Fee : RM {{ customerPostage.delivery_fee.toFixed(2) }}</p>
                        <hr class="w-full border border-gray-300">
                        <p class="text-2xl my-2 ">Total :
                            <span class="font-semibold"> RM {{ (totalPrice + parseFloat(customerPostage.delivery_fee)).toFixed(2) }}</span></p>
                    </div>
                    <div class="bottom-0 fixed py-6">
                        <button @click="confirmOrder"
                                v-if="cart.products.length > 0 && cart.customer_id.code > 0 && cart.seller_id.code > 0"
                                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-auto flex items-center">
                            <font-awesome-icon icon="check-circle" class="mr-2"/>
                            Create Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.input-wrapper {
    width: 100px;
    height: 30px;
    display: flex;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
    padding: 10px;
    text-align: center;
}

.input-wrapper * {
//border: none; width: 50px; flex: 1;
}

.input-wrapper button {
    cursor: pointer;
}

.input-wrapper button:hover {
    background-color: #e1e1e1;
}

.input-wrapper button:first-child {
    color: red;
}

.input-wrapper button:last-child {
    color: green;
}
</style>
