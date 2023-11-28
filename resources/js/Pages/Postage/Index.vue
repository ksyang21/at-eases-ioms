<script setup>

import {Head, Link, router} from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {inject, reactive, ref} from "vue";

const props = defineProps({
	breadcrumbs: Object,
	postages: Object,
	errors: Object
})

const Swal = inject('$swal')

let filteredPostages = ref(props.postages)

const newPostage = reactive({
	area: '',
	postcode: '',
	state: '',
	delivery_fee: 0.00,
	status: 'active',
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

let search = ref('')

function searchPostage() {
	filteredPostages.value = props.postages.filter((postage) => {
		return postage.state.toLowerCase().includes(search.value.toLowerCase()) || postage.area.toLowerCase().includes(search.value.toLowerCase()) || postage.postcode.includes(search.value.toLowerCase())
	})
}

let showError = ref(false)

function createNewPostage() {
	showError.value = false
	if(!isDuplicatedPostage()) {
		if (newPostage.area !== '' && newPostage.delivery_fee >= 0 && newPostage.postcode !== '' && newPostage.state !== '') {
			router.post('/postage', newPostage)
		} else {
			showError.value = true
			Swal.fire('', 'Please fill in all required fields!', 'error')
		}
	} else {
		Swal.fire({
			html: `<b>${newPostage.area}</b> / <b>${newPostage.postcode}</b> already exists!`,
			icon: 'error'
		})
	}
}

function removePostage(postage) {
	Swal.fire({
		title: 'Delete this option?',
		text: 'You cannot undo this!',
		icon: 'warning',
		showCancelButton: true
	}).then((result) => {
		if(result.isConfirmed) {
			router.delete(`/postage/${postage.id}`)
		}
	})
}

function isDuplicatedPostage() {
	const duplicate = props.postages.filter((postage) => {
		const duplicatedArea = postage.area.toLowerCase() === newPostage.area.toLowerCase()
		const duplicatedPostcode = postage.postcode.toLowerCase() === newPostage.postcode.toLowerCase()

		return duplicatedArea || duplicatedPostcode
	})

	return duplicate.length
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
			<div class="py-6" v-if="errors.length > 0">
				<p class="text-xl text-red-600" v-for="(error, index) in errors" :key="index">
					{{error}}
				</p>
			</div>
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
						<div class="flex items-center">
							<div id="search-bar" class="w-full">
								<label for="search" class="text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
								<div class="relative">
									<div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
										<svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
											 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
											<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
												  stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
										</svg>
									</div>
									<input type="search" id="search" @input="searchPostage"
										   v-model="search"
										   class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 "
										   placeholder="Search Postcode / Area">
								</div>
							</div>
						</div>
						<div class="relative overflow-x-auto sm:rounded-lg mt-5">
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
								<tbody v-if="filteredPostages.length > 0">
								<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50"
									v-for="(option, index) in filteredPostages" :key="index">
									<th scope="row"
										class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
										<p class="font-semibold">{{ option.area }}, {{ option.state }}</p>
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
											<div
												class="absolute bottom-0 flex flex-col items-center hidden mb-6 group-hover:flex">
                                                <span
													class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg">Edit</span>
												<div class="w-3 h-3 -mt-2 rotate-45 bg-black"></div>
											</div>
										</div>
										<div class="relative flex flex-col items-center group ml-6">
											<font-awesome-icon icon="trash" @click="removePostage(option)"
															   class="option-action-btn text-red-600 hover:text-red-800"/>
											<div
												class="absolute bottom-0 flex flex-col items-center hidden mb-6 group-hover:flex">
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
		<div id="newPostageModal"
			 class="w-full md:w-1/3 hidden fixed inset-y-0 right-0 bg-white flex flex-col shadow-2xl">
			<div class="top-0 p-6 border-b-2 border-gray-100 flex items-center">
				<p class="text-2xl">Create New Option</p>
				<button class="text-red-600 ml-auto" @click="closeModal">
					<font-awesome-icon icon="times-circle"/>
				</button>
			</div>
			<div class="bg-white p-6 rounded-lg">
				<div id="area-section" class="mb-6">
					<label for="name"
						   class="block mb-2 text-sm font-medium text-gray-900">Area</label>
					<input id="name"
						   class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
						   placeholder="Area e.g. Bangsar, Sungai Besi..."
						   v-model="newPostage.area">
					<p class="text-red-600 text-sm" v-if="showError && newPostage.area === ''">Area name must be provided</p>

				</div>
				<div id="state-section" class="mb-6">
					<label for="state"
						   class="block mb-2 text-sm font-medium text-gray-900">State</label>
					<input id="state"
						   class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
						   placeholder="State e.g. Johor, Selangor..."
						   v-model="newPostage.state">
					<p class="text-red-600 text-sm" v-if="showError && newPostage.state === ''">State must be provided</p>

				</div>
				<div id="postcode-section" class="mb-6">
					<label for="postcode" class="block mb-2 text-sm font-medium text-gray-900">Postcode</label>
					<input id="postcode"
						   class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
						   placeholder="Postcode" required
						   v-model="newPostage.postcode">
					<p class="text-red-600 text-sm" v-if="showError && newPostage.postcode === ''">Postcode must be provided</p>
				</div>
				<div id="delivery-fee-section" class="mb-6">
					<label for="delivery-fee-amount" class="block mb-2 text-sm font-medium text-gray-900">Delivery Fee (RM)</label>
					<input type="number" id="delivery-fee-amount"
						   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
						   placeholder="Delivery fee to this area" required v-model="newPostage.delivery_fee"
						   step="0.01">
					<p class="text-red-600 text-sm" v-if="showError && newPostage.delivery_fee < 0.00">Delivery Fee must be provided</p>
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
					<button @click="createNewPostage"
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
