<script setup>

import {Head, Link, router} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {reactive, ref} from "vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
    product: Object,
    inventory_logs: Object
})

let form = reactive({
    quantity: 0,
    stock_status: 'stock in',
})

let canSubmit = ref(true)
let showError = ref(false)

function submitForm() {
    canSubmit.value = true
    showError.value = false

    let allowedStatuses = ['stock in', 'stock out']
    if(form.quantity <= 0 || !allowedStatuses.includes(form.stock_status)) {
        showError.value = true
        canSubmit.value = false
    }

    if(canSubmit.value) {
        router.post(`/inventory-logs/${props.product.id}`, form)
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
                        <div class="flex flex-col">
                            <p class="text-xl">{{ product.name }}</p>
                        </div>
                    </div>
                    <form @submit.prevent="submitForm">
                        <div class="p-6 mx-2 border-b-2 border-gray-100">
                            <div id="log-option-section" class="flex item-center">
                                <div class="flex items-center mr-4">
                                    <input v-model="form.stock_status" value="stock in" id="default-radio-1"
                                           type="radio" name="default-radio"
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="default-radio-1"
                                           class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Stock
                                        In</label>
                                </div>
                                <div class="flex items-center">
                                    <input v-model="form.stock_status" value="stock out" id="default-radio-2"
                                           type="radio" name="default-radio"
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="default-radio-2"
                                           class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Stock
                                        Out</label>
                                </div>
                            </div>
                            <div id="quantity-section" class="mt-6">
                                <label for="quantity"
                                       class="block mb-2 text-sm font-medium text-gray-900">Quantity</label>
                                <input type="number" id="quantity"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                       placeholder="Point Value" required v-model="form.quantity">
                                <p class="text-red-600 text-sm" v-if="showError && form.quantity < 1">Quantity must
                                    not
                                    be less than 1</p>
                            </div>
                        </div>
                        <div class="p-6 flex items-center">
                            <Link :href="route('inventoryLogs.index', product.id)"
                                  class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                <font-awesome-icon icon="angle-left" class="mr-1"/>
                                Back to Inventory Logs
                            </Link>
                            <button
                                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-auto flex items-center">
                                <font-awesome-icon icon="check-circle" class="mr-2"/>
                                Save Record
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
