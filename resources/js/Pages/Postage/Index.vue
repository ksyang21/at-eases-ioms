<script setup>

import {Head, Link} from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {reactive, ref} from "vue";

const props = defineProps({
	breadcrumbs: Object,
	postages: Object
})

const newPostage = reactive({
    area: '',
    postcode: '',
    delivery_fee: 0.00
})

let isModalOpen = ref(false)
function openNewPostage() {
    document.getElementById('newPostageModal').classList.remove('hidden')
    isModalOpen.value = true
}

function closeModal() {
    document.getElementById('newPostageModal').classList.add('hidden')
    isModalOpen.value = false
}
</script>

<template>
	<Head title="Postage Management"/>
	<AuthenticatedLayout>
		<template #header>
			<h2 class="font-semibold text-xl text-gray-800 leading-tight">Postage Management</h2>
		</template>
		<Breadcrumb :breadcrumbs="breadcrumbs"/>
		<div class="py-0">
			<div class="mx-auto sm:px-6 lg:px-8">
				<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
					<div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
						<div class="flex flex-col">
							<p class="text-2xl font-bold">Postage Settings</p>
						</div>
                        <button @click="openNewPostage"
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
										Area
									</th>
									<th scope="col" class="px-6 py-3">
										Postcode
									</th>
									<th scope="col" class="px-6 py-3">
										Delivery Fee (RM)
									</th>
									<th scope="col" class="px-6 py-3">
										Action
									</th>
								</tr>
								</thead>
								<tbody v-if="postages.length > 0">
								<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50"
									v-for="(option, index) in postages" :key="index">
									<th scope="row"
										class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
										<p class="font-semibold">{{ option.area }}</p>
									</th>
									<td class="px-6 py-4">
										<p class="font-semibold">{{ option.postcode }}</p>
									</td>
									<td class="px-6 py-4">
										<p class="font-semibold">{{ option.delivery_fee.toFixed(2) }}</p>
									</td>
									<td class="px-6 py-4 flex items-center">
										<div class="relative flex flex-col items-center group">
											<font-awesome-icon icon="pen-to-square"
															   class="option-action-btn hover:text-gray-800"/>
											<div class="absolute bottom-0 flex flex-col items-center hidden mb-6 group-hover:flex">
                                                <span
													class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg">Edit</span>
												<div class="w-3 h-3 -mt-2 rotate-45 bg-black"></div>
											</div>
										</div>
										<div class="relative flex flex-col items-center group ml-6">
											<font-awesome-icon icon="trash"
															   class="option-action-btn text-red-600 hover:text-red-800"/>
											<div class="absolute bottom-0 flex flex-col items-center hidden mb-6 group-hover:flex">
                                                <span
													class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg">Remove</span>
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
        <div id="newPostageModal" class="w-full md:w-1/3 hidden fixed inset-y-0 right-0 bg-white flex flex-col shadow-2xl">
            <div class="top-0 p-6 border-b-2 border-gray-100 flex items-center">
                <p class="text-2xl">Create New Option</p>
                <button class="text-red-600 ml-auto" @click="closeModal">
                    <font-awesome-icon icon="times-circle"/>
                </button>
            </div>
            <div class="bg-white p-6 rounded-lg">
                <div id="delivery-method-section" class="mb-6">
                    <label for="name"
                           class="block mb-2 text-sm font-medium text-gray-900">Area</label>
                    <input id="name"
                           class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Area e.g. Bangsar, Sungai Besi..."
                           v-model="newPostage.area">
                </div>
                <div id="status-section" class="flex item-center">
                    <div class="flex items-center mr-4">
                        <input v-model="newPostage.status" value="active" id="active-radio-1"
                               type="radio" name="default-radio"
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="default-radio-1"
                               class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Active</label>
                    </div>
                    <div class="flex items-center">
                        <input v-model="newPostage.status" value="inactive" id="default-radio-2"
                               type="radio" name="default-radio"
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="default-radio-2"
                               class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Inactive</label>
                    </div>
                </div>
                <div class="bottom-0 fixed py-6">
                    <button class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-auto flex items-center">
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
