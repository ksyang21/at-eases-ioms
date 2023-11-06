<script setup>

import {Head, Link} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {inject, reactive, ref} from "vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const Swal = inject('$swal')
const props = defineProps({
    product: Object,
    packages: Object
})


let form = reactive({
    product_id: props.product.id,
    name: '',
    quantity: 0,
    price: 0.00
})

let showError = ref(false)
function submitForm() {
    if(form.name === '' || form.quantity < 1 || form.price < 0.00) {
        showError.value = true
    }
    if(checkPackageExists()) {
        showError.value = true
    }
}

function checkPackageExists() {
    let isPackageExist = false
    let existingPackage = {}

    for(let item of props.packages) {
        if(item.package.name === form.name || item.details.quantity === form.quantity) {
            isPackageExist = true
            existingPackage = item
        }
    }

    if(isPackageExist) {
        console.log(existingPackage)
        Swal.fire({
            title: 'Package exists!',
            html: `Name : ${existingPackage.package.name}<br>Quantity: ${existingPackage.details.quantity}<br>Price: RM${existingPackage.details.price}`,
            icon: 'warning',
        })
    }

    return isPackageExist
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
                        <div class="flex flex-col">
                            <p class="text-xl">{{ product.name }}</p>
                        </div>
                    </div>
                    <form @submit.prevent="submitForm">
                        <div class="p-6 border-b-2 border-gray-100">
                            <div id="package-name-group" class="mb-6">
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Package
                                    Name</label>
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <font-awesome-icon icon="font"
                                                           class="w-4 h-4 text-gray-500 dark:text-gray-400"/>
                                    </div>
                                    <input type="text" id="name"
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
                                           placeholder="Give this package a unique name!" v-model="form.name">
                                </div>
                                <p class="text-red-600 text-sm" v-if="showError && form.name === ''">Package
                                    name is required</p>
                            </div>
                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label for="price" class="block mb-2 text-sm font-medium text-gray-900">Price (MYR)</label>
                                    <input type="number" id="price"
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                           placeholder="Product Original Price" required v-model="form.price" step="0.01">
                                    <p class="text-red-600 text-sm" v-if="showError && form.price <= 0.00">Price must not be less than 0.01</p>
                                </div>
                                <div>
                                    <label for="quantity" class="block mb-2 text-sm font-medium text-gray-900">Quantity</label>
                                    <input type="number" id="quantity"
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                           placeholder="Point Value" required v-model="form.quantity">
                                    <p class="text-red-600 text-sm" v-if="showError && form.quantity < 1">Quantity must not be less than 1</p>
                                </div>
                            </div>
                        </div>
                        <div class="py-6 mx-6 flex items-center">
                            <Link :href="route('package.index', product.id)"
                                  class="font-medium text-blue-600 dark:text-blue-500 hover:underline hidden md:inline-flex items-center">
                                <font-awesome-icon icon="angle-left" class="mr-1"/>
                                Back to {{ product.name }} Package Listing
                            </Link>
                            <Link :href="route('package.index', product.id)"
                                  class="font-medium text-blue-600 dark:text-blue-500 hover:underline inline md:hidden">
                                <font-awesome-icon icon="angle-left" class="mr-1"/>
                                Back
                            </Link>
                            <button
                                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-auto flex items-center">
                                <font-awesome-icon icon="check-circle" class="mr-2"/>
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
