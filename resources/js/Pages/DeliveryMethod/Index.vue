<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {Head, router} from "@inertiajs/vue3";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {computed, inject, reactive, ref} from "vue";

const Swal = inject('$swal')

const props = defineProps({
    delivery_methods: Object,
    breadcrumbs: Object,
})

const newDeliveryMethod = reactive({
    name: '',
    status: 'active'
})

const editedDeliveryMethod = reactive({
    id: 0,
    name: '',
    status: 'active'
})

const isStatusActive = computed(() => {
    return newDeliveryMethod.status === 'active';
});

let isModalOpen = ref(false)

function openNewDeliveryModal() {
    document.getElementById('newDeliveryMethodModal').classList.remove('hidden')
    isModalOpen.value = true
}

function closeModal() {
    document.getElementById('newDeliveryMethodModal').classList.add('hidden')
    document.getElementById('editDeliveryModal').classList.add('hidden')
    isModalOpen.value = false
}

function deactivateDeliveryMethod(deliveryMethod) {
    Swal.fire({
        text: `Deactivate ${deliveryMethod.delivery_method}?`,
        icon: 'info',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(`/deactivate-delivery/${deliveryMethod.id}`)
        }
    })
}

function activateDeliveryMethod(deliveryMethod) {
    Swal.fire({
        text: `Activate ${deliveryMethod.delivery_method}?`,
        icon: 'info',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(`/activate-delivery/${deliveryMethod.id}`)
        }
    })
}


function saveNewDeliveryMethod() {
    let option = props.delivery_methods.find((method) => {
        return method.delivery_method === newDeliveryMethod.name
    })

    if(!option) {
        Swal.fire({
            title: 'Create new delivery option?',
            text: newDeliveryMethod.name,
            icon: 'info',
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                router.post('/delivery', newDeliveryMethod)
            }
        })
    } else {
        Swal.fire({
            html: `<b>${newDeliveryMethod.name}</b> already exists!`,
            icon: 'error'
        })
    }
}

function editDeliveryMethod(deliveryMethod) {
    editedDeliveryMethod.id = deliveryMethod.id
    editedDeliveryMethod.name = deliveryMethod.delivery_method
    editedDeliveryMethod.status = deliveryMethod.status
    document.getElementById('editDeliveryModal').classList.remove('hidden')
    isModalOpen.value = true
}

function saveEditDeliveryMethod() {
    Swal.fire({
        text: 'Save changes?',
        icon: 'info',
        showCancelButton: true,
    }).then((result)=> {
        if(result.isConfirmed) {
            router.put(`/delivery/${editedDeliveryMethod.id}`, editedDeliveryMethod)
        }
    })
}
</script>

<template>

    <Head title="Delivery Method"/>

    <AuthenticatedLayout>
        <Breadcrumb :breadcrumbs="breadcrumbs"></Breadcrumb>
        <div class="py-0">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
                        <p class="text-xl">Delivery Method Settings</p>
                        <button @click="openNewDeliveryModal"
                                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 ml-auto flex items-center">
                            <font-awesome-icon icon="plus-circle" class="mr-2"/>
                            New Option
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="relative overflow-x-auto sm:rounded-lg">
                            <table
                                class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border-2 border-gray-100">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Delivery Method
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Action
                                    </th>
                                </tr>
                                </thead>
                                <tbody v-if="delivery_methods.length > 0">
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50"
                                    v-for="(option, index) in delivery_methods" :key="index">
                                    <th scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <p class="font-semibold">{{ option.delivery_method }}</p>
                                    </th>
                                    <td class="px-6 py-4">
                                        <span class="py-1 px-4 bg-green-300 text-green-700 rounded-lg"
                                              v-if="option.status === 'active'">{{ option.status.toUpperCase() }}</span>
                                        <span class="py-1 px-4 bg-red-300 text-red-700 rounded-lg"
                                              v-else>{{ option.status.toUpperCase() }}</span>
                                    </td>
                                    <td class="px-6 py-4 flex items-center">
                                        <div class="relative flex flex-col items-center group">
                                            <font-awesome-icon icon="pen-to-square" @click="editDeliveryMethod(option)"
                                                               class="option-action-btn hover:text-gray-800"/>
                                            <div class="absolute bottom-0 flex flex-col items-center hidden mb-6 group-hover:flex">
                                                <span
                                                    class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg">Edit</span>
                                                <div class="w-3 h-3 -mt-2 rotate-45 bg-black"></div>
                                            </div>
                                        </div>
                                        <div class="relative flex flex-col items-center group ml-6"
                                             @click="deactivateDeliveryMethod(option)" v-if="option.status ==='active'">
                                            <font-awesome-icon icon="ban" class="text-red-600 option-action-btn hover:text-red-800"/>
                                            <div
                                                class="absolute bottom-0 flex flex-col items-center hidden mb-6 group-hover:flex">
                                                <span
                                                    class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg">Deactivate</span>
                                                <div class="w-3 h-3 -mt-2 rotate-45 bg-black"></div>
                                            </div>
                                        </div>
                                        <div class="relative flex flex-col items-center group ml-6"
                                             @click="activateDeliveryMethod(option)" v-if="option.status ==='inactive'">
                                            <font-awesome-icon icon="check-circle" class="text-blue-600 option-action-btn hover:text-blue-800"/>
                                            <div class="absolute bottom-0 flex flex-col items-center hidden mb-6 group-hover:flex">
                                                <span class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg">Activate</span>
                                                <div class="w-3 h-3 -mt-2 rotate-45 bg-black"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center">No option available</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="isModalOpen" class="fixed inset-0 bg-gray-800 bg-opacity-50"></div>
        <div id="newDeliveryMethodModal" class="w-full md:w-1/3 hidden fixed inset-y-0 right-0 bg-white flex flex-col shadow-2xl">
            <div class="top-0 p-6 border-b-2 border-gray-100 flex items-center">
                <p class="text-2xl">Create New Option</p>
                <button class="text-red-600 ml-auto" @click="closeModal">
                    <font-awesome-icon icon="times-circle"/>
                </button>
            </div>
            <div class="bg-white p-6 rounded-lg">
                <div id="delivery-method-section" class="mb-6">
                    <label for="name"
                           class="block mb-2 text-sm font-medium text-gray-900">Name</label>
                    <input id="name"
                           class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Delivery Method Name"
                           v-model="newDeliveryMethod.name">
                </div>
                <div id="status-section" class="flex item-center">
                    <div class="flex items-center mr-4">
                        <input v-model="newDeliveryMethod.status" value="active" id="default-radio-1"
                               type="radio" name="default-radio"
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="default-radio-1"
                               class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Active</label>
                    </div>
                    <div class="flex items-center">
                        <input v-model="newDeliveryMethod.status" value="inactive" id="default-radio-2"
                               type="radio" name="default-radio"
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="default-radio-2"
                               class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Inactive</label>
                    </div>
                </div>
                <div class="bottom-0 fixed py-6">
                    <button @click="saveNewDeliveryMethod"
                        class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-auto flex items-center">
                        <font-awesome-icon icon="check-circle" class="mr-2"/>
                        Save
                    </button>
                </div>
            </div>
        </div>
        <div id="editDeliveryModal" class="w-full md:w-1/3 hidden fixed inset-y-0 right-0 bg-white flex flex-col shadow-2xl">
            <div class="top-0 p-6 border-b-2 border-gray-100 flex items-center">
                <p class="text-2xl">Edit Option</p>
                <button class="text-red-600 ml-auto" @click="closeModal">
                    <font-awesome-icon icon="times-circle"/>
                </button>
            </div>
            <div class="bg-white p-6 rounded-lg">
                <div id="delivery-method-section" class="mb-6">
                    <label for="name"
                           class="block mb-2 text-sm font-medium text-gray-900">Name</label>
                    <input id="name"
                           class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Delivery Method Name"
                           v-model="editedDeliveryMethod.name">
                </div>
                <div id="status-section" class="flex item-center">
                    <div class="flex items-center mr-4">
                        <input v-model="editedDeliveryMethod.status" value="active" id="edit-radio-active"
                               type="radio" name="default-radio"
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="edit-radio-active"
                               class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Active</label>
                    </div>
                    <div class="flex items-center">
                        <input v-model="editedDeliveryMethod.status" value="inactive" id="edit-radio-inactive"
                               type="radio" name="default-radio"
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="edit-radio-inactive"
                               class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Inactive</label>
                    </div>
                </div>
                <div class="bottom-0 fixed py-6">
                    <button @click="saveEditDeliveryMethod" v-if="editedDeliveryMethod.name !== ''"
                            class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-auto flex items-center">
                        <font-awesome-icon icon="check-circle" class="mr-2"/>
                        Save
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.option-action-btn {
    font-size: 20px;
    cursor: pointer;
}
</style>
