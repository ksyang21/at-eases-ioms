<script setup>
import {Head, Link, router} from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {inject, reactive} from "vue";

const Swal = inject('$swal')
const props = defineProps({
    product: Object,
})

let form = reactive({
    id: props.product.id,
    name: props.product.name,
    description: props.product.description,
    price: props.product.price,
    pv: props.product.pv
})

function removeProduct() {
    Swal.fire({
        title: `Remove ${props.product.name}?`,
        text: 'This action cannot be reverted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d71d28'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete('/delete-product')
        }
    })
}

function changeProductStatus() {
    let statusText = props.product.status === 'active' ? 'Deactivate' : 'Activate'
    Swal.fire({
        title: `${statusText} ${props.product.name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d71d28'
    }).then((result) => {
        if (result.isConfirmed) {
            router.put('/change-product-status')
        }
    })
}

function submitForm() {

}
</script>

<template>

    <Head title="Edit Product"/>

    <AuthenticatedLayout>
        <Breadcrumb></Breadcrumb>
        <div class="py-0">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
                        <p class="text-2xl">Edit Product</p>
                        <div class="ml-auto flex items-center">
                            <button type="button" v-if="props.product.status === 'active'" @click="changeProductStatus"
                                    class="text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 flex items-center">
                                <font-awesome-icon icon="plane-circle-xmark" class="mr-2"/>
                                Deactivate
                            </button>
                            <button type="button" v-else @click="changeProductStatus"
                                    class="text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 flex items-center">
                                <font-awesome-icon icon="plane-up" class="mr-2"/>
                                Activate
                            </button>
                            <button type="button" @click="removeProduct"
                                    class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 flex items-center">
                                <font-awesome-icon icon="trash" class="mr-2"/>
                                Remove
                            </button>
                        </div>
                    </div>
                    <div class="p-6 border-b-2 border-gray-100">
                        <div class="md:flex md:items-center">
                            <div class="w-full md:w-1/4 flex justify-center">
                                <div class="relative group">
                                    <img src="../../../images/coke.jpeg" alt="Image" class="w-64 h-64 object-cover rounded-md">
                                    <div class="absolute inset-0 flex flex-col items-center justify-center opacity-0 group-hover:opacity-90 transition duration-300 hover:bg-gray-300">
                                        <button class="bg-blue-500 text-white px-2 py-1 mb-2 rounded-md hover:bg-blue-600">Replace</button>
                                        <button class="bg-red-500 text-white px-2 py-1 rounded-md hover:bg-red-600">Remove</button>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col w-full md:w-3/4">
                                <div id="name-group">
                                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Product
                                        Name</label>
                                    <div class="relative mb-6">
                                        <div
                                            class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                            <font-awesome-icon icon="font"
                                                               class="w-4 h-4 text-gray-500 dark:text-gray-400"/>
                                        </div>
                                        <input type="text" id="name"
                                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
                                               placeholder="Product Name" v-model="form.name">
                                    </div>
                                </div>
                                <div id="description-group" class="mb-6">
                                    <label for="message"
                                           class="block mb-2 text-sm font-medium text-gray-900">Description</label>
                                    <textarea id="message" rows="4"
                                              class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                              placeholder="Product Description" v-model="form.description"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="grid gap-6 mb-6 md:grid-cols-2">
                            <div>
                                <label for="price" class="block mb-2 text-sm font-medium text-gray-900">Price
                                    (MYR)</label>
                                <input type="text" id="price"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                       placeholder="Product Original Price" required v-model="form.price">
                            </div>
                            <div>
                                <label for="pv" class="block mb-2 text-sm font-medium text-gray-900">PV</label>
                                <input type="text" id="pv"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                       placeholder="Point Value" required v-model="form.pv">
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
                        <Link :href="route('products.index')"
                              class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                            <font-awesome-icon icon="angle-left" class="mr-1"/>
                            Back to Product Listing
                        </Link>
                        <button type="button"
                                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-auto flex items-center"
                                @click="submitForm"
                        >
                            <font-awesome-icon icon="check-circle" class="mr-2"/>
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
</style>
