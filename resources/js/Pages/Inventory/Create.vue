<script setup>
import {Head, Link, router} from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {computed, inject, reactive, ref} from "vue";

const Swal = inject('$swal')

const props = defineProps({
    errors: Object
})

let form = reactive({
    name: '',
    description: '',
    price: 0.00,
    pv: 0,
    imagePreview: '',
    status: 'active',
    image: null
})

let canSubmit = ref(true)
let showError = ref(false)

const isStatusActive = computed(() => {
    return form.status === 'active';
});

function updateStatus() {
    if(isStatusActive.value) {
        form.status = 'inactive'
    } else {
        form.status = 'active'
    }
}

function addImage() {
    document.getElementById('image-input').click()
}

function submitForm() {
    canSubmit.value = form.name !== '' && form.price >= 0.00 && form.pv >= 0
    if (canSubmit.value) {
        let warningText = ''
        if (form.price === 0.00) {
            warningText += 'Price is set to RM0.00<br>'
        }
        if (form.pv === 0) {
            warningText += 'PV is set to 0<br>'
        }

        if(warningText !== '') {
            Swal.fire({
                title: 'Proceed to add new product?',
                html: warningText,
                icon: 'warning',
                showCancelButton: true
            }).then((result) => {
                if(result.isConfirmed) {
                    router.post('/product', form)
                }
            })
        } else {
            router.post('/product', form)
        }
    } else {
        showError.value = true
    }
}

function previewImage(e) {
    const file = e.target.files[0]
    form.imagePreview = URL.createObjectURL(file)
    form.image = file
}

</script>

<template>

    <Head title="New Product"/>

    <AuthenticatedLayout>
        <Breadcrumb></Breadcrumb>
        {{errors}}
        <div class="py-0">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
                        <p class="text-2xl">New Product</p>
                    </div>
                    <form @submit.prevent="submitForm()">
                        <div class="p-6 border-b-2 border-gray-100">
                            <div class="md:flex md:items-center mb-0 md:mb-3">
                                <div class="w-full md:w-1/4 flex flex-col items-center">
                                    <label for="image-input">
                                        <img :src="form.imagePreview ? form.imagePreview : '/storage/image/no-image.jpg'" alt="Image"
                                             class="w-64 h-64 object-fill rounded-md">
                                    </label>
                                    <input type="file" id="image-input" class="hidden" @change="previewImage">
                                    <div class="flex items-center justify-center mt-2">
                                        <button
                                            type="button"
                                            @click="addImage"
                                            class="bg-blue-500 text-white px-2 py-1 mr-2 rounded-md hover:bg-blue-600">
                                            Add Image
                                        </button>
                                    </div>
                                </div>
                                <div class="flex flex-col w-full md:w-3/4 mt-3">
                                    <div id="status-group" class="flex items-center mb-6">
                                        <input
                                            type="checkbox" id="status-input"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                                            :checked="form.status === 'active'"
                                            @change="updateStatus"
                                        >
                                        <label for="status-input" class="ml-2 text-lg font-medium text-gray-900">Active Product</label>
                                    </div>
                                    <div id="name-group" class="mb-6">
                                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Product
                                            Name</label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                                <font-awesome-icon icon="font"
                                                                   class="w-4 h-4 text-gray-500 dark:text-gray-400"/>
                                            </div>
                                            <input type="text" id="name"
                                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
                                                   placeholder="Product Name" v-model="form.name">
                                        </div>
                                        <p class="text-red-600 text-sm" v-if="showError && form.name === ''">Product
                                            name is required</p>
                                    </div>
                                    <div id="description-group" class="mb-6">
                                        <label for="message"
                                               class="block mb-2 text-sm font-medium text-gray-900">Description</label>
                                        <textarea id="message" rows="4"
                                                  class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                                  placeholder="Product Description"
                                                  v-model="form.description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="grid gap-6 mb-6 md:grid-cols-2">
                                <div>
                                    <label for="price" class="block mb-2 text-sm font-medium text-gray-900">Price
                                        (MYR)</label>
                                    <input type="number" id="price"
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                           placeholder="Product Original Price" required v-model="form.price" step="0.01">
                                    <p class="text-red-600 text-sm" v-if="showError && form.price < 0.00">Price must be
                                        given</p>
                                </div>
                                <div>
                                    <label for="pv" class="block mb-2 text-sm font-medium text-gray-900">PV</label>
                                    <input type="number" id="pv"
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                           placeholder="Point Value" required v-model="form.pv">
                                    <p class="text-red-600 text-sm" v-if="showError && form.pv < 0">PV must be given</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
                            <Link :href="route('products.index')"
                                  class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                <font-awesome-icon icon="angle-left" class="mr-1"/>
                                Back to Product Listing
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
