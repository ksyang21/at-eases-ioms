<script setup>
import {Head, Link} from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
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
                        <button type="button"
                                class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 ml-auto flex items-center">
                            <font-awesome-icon icon="trash" class="mr-2"/>
                            Remove
                        </button>
                    </div>
                    <div class="p-6 border-b-2 border-gray-100">
                        <div id="name-group">
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Product Name</label>
                            <div class="relative mb-6">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                    <font-awesome-icon icon="font" class="w-4 h-4 text-gray-500 dark:text-gray-400"/>
                                </div>
                                <input type="text" id="name"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
                                       placeholder="Product Name">
                            </div>
                        </div>
                        <div id="description-group" class="mb-6">
                            <label for="message" class="block mb-2 text-sm font-medium text-gray-900">Description</label>
                            <textarea id="message" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Product Description"></textarea>
                        </div>
                        <div class="grid gap-6 mb-6 md:grid-cols-2">
                            <div>
                                <label for="price" class="block mb-2 text-sm font-medium text-gray-900">Price</label>
                                <input type="text" id="price" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Product Original Price" required>
                            </div>
                            <div>
                                <label for="pv" class="block mb-2 text-sm font-medium text-gray-900">PV</label>
                                <input type="text" id="pv" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Point Value" required>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
                        <Link :href="route('product.index')" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                            <font-awesome-icon icon="angle-left" class="mr-1"/>
                            Back to Product Listing
                        </Link>
                        <button type="button"
                                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-auto flex items-center">
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
