<script setup>

import {Head, Link, router} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {inject, reactive, ref} from "vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const Swal = inject('$swal')

const props = defineProps({
	breadcrumbs: Object,
	sellers: Object,
	errors: Object
})

let sellerOptions = []
for(let seller of props.sellers) {
	sellerOptions.push({
		label: seller.name,
		code: seller.id
	})
}

const form = reactive({
	name: '',
	seller: {label: 'Please select seller', code: 0},
	address: '',
	phone: '',
	email: ''
})

let showError = ref(false)
let invalidEmail = ref(false)

function validateForm() {
	showError.value = false
	invalidEmail.value = false
	if(form.name === '' || form.address === '' || form.phone === '' || form.email === '' || form.seller.code <= 0) {
		showError.value = true

		Swal.fire({
			text: 'Please fill in all fields!',
			icon: 'error'
		})

		return false
	}
	if(!validateEmail(form.email)) {
		showError.value = true
		invalidEmail.value = true

		Swal.fire({
			text: 'Invalid email address!',
			icon: 'error'
		})

		return false
	}
	return true
}

function validateEmail(email) {
	const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	return emailRegex.test(email);
}

function createCustomer() {
	if(validateForm()) {
		form.seller = form.seller.code // convert object to only seller ID
		router.post('/customer', form)
	}
}
</script>

<template>
	<Head title="Products"/>

	<AuthenticatedLayout>
		<Breadcrumb :breadcrumbs="breadcrumbs"></Breadcrumb>
		<div v-if="errors.length > 0" class="px-8 py-6">
			<p class="text-red-600 text-xl" v-for="(error, index) in errors" :key="index">{{error}}</p>
		</div>
		<div class="py-0">
			<div class="mx-auto sm:px-6 lg:px-8">
				<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
					<div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
						<div class="flex flex-col">
							<p class="text-xl">New Customer</p>
						</div>
					</div>
					<form @submit.prevent="createCustomer">
						<div class="p-6 border-b-2 border-gray-100">
							<div id="customer-name-group" class="mb-6">
								<label for="name" class="block mb-2 text-sm font-medium text-gray-900">Customer
									Name</label>
								<input type="text" id="name"
									   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
									   placeholder="Customer name" v-model="form.name">
								<p class="text-red-600 text-sm" v-if="showError && form.name === ''">Customer
									name is required</p>
							</div>
							<div id="seller-group" class="mb-6">
								<label for="seller" class="block mb-2 text-sm font-medium text-gray-900">Seller</label>
								<VueSelect :options="sellerOptions" v-model="form.seller"/>
								<p class="text-red-600 text-sm" v-if="showError && form.seller.code <= 0">Please select seller</p>
							</div>
							<div id="customer-address-group" class="mb-6">
								<label for="address"
									   class="block mb-2 text-sm font-medium text-gray-900">Address</label>
								<textarea id="address" rows="4"
										  class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
										  placeholder="Address"
										  v-model="form.address"></textarea>
								<p class="text-red-600 text-sm" v-if="showError && form.address === ''">Address is required</p>
							</div>
							<div class="grid gap-6 md:grid-cols-2">
								<div>
									<label for="phone" class="block mb-2 text-sm font-medium text-gray-900">Phone</label>
									<input type="text" id="phone"
										   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
										   placeholder="Customer phone number" v-model="form.phone">
									<p class="text-red-600 text-sm" v-if="showError && form.phone === ''">Customer phone number is required</p>
								</div>
								<div>
									<label for="email"
										   class="block mb-2 text-sm font-medium text-gray-900">Email</label>
									<input type="text" id="phone"
										   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
										   placeholder="Customer email" v-model="form.email">
									<p class="text-red-600 text-sm" v-if="showError && form.email === ''">Customer email is required</p>
									<p class="text-red-600 text-sm" v-if="showError && invalidEmail">Please provide valid email address</p>
								</div>
							</div>
						</div>
						<div class="py-6 mx-6 flex items-center">
							<Link :href="route('customers.index')"
								  class="font-medium text-blue-600 dark:text-blue-500 hover:underline hidden md:inline-flex items-center">
								<font-awesome-icon icon="angle-left" class="mr-1"/>
								Back to Customers Listing
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
