<script setup>

import {Head, Link, router} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {reactive, ref} from "vue";

const props = defineProps({
	breadcrumbs: Object,
	sellers: Object
})

const form = reactive({
	name: '',
	email: '',
})

let showError = ref(false)
let duplicateSeller = ref(false)

function checkDuplicateSeller() {
	for (let seller in props.sellers) {
		if (seller.email === form.email) {
			showError.value = true
			duplicateSeller.value = true
			return false
		}
	}
	return true
}

function validateForm() {
    if(form.name === '' || form.email === '') {
        showError.value = true
        return false
    }

    return checkDuplicateSeller();

}

function createSeller() {
    showError.value = false
    duplicateSeller.value = false
    if(validateForm()) {
        router.post('/seller', form)
    }
}
</script>

<template>

	<Head title="Seller Management"/>
	<AuthenticatedLayout>
		<template #header>
			<h2 class="font-semibold text-xl text-gray-800 leading-tight">All Sellers</h2>
		</template>
		<Breadcrumb :breadcrumbs="breadcrumbs"/>
		<div class="py-0">
			<div class="mx-auto sm:px-6 lg:px-8">
				<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
					<div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
						<p class="text-2xl">New Seller</p>
					</div>
					<form @submit.prevent="createSeller">
						<div class="p-6 border-b-2 border-gray-100">
							<div id="customer-name-group" class="mb-6">
								<label for="name" class="block mb-2 text-sm font-medium text-gray-900">Name</label>
								<input type="text" id="name"
									   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
									   placeholder="Seller name" v-model="form.name">
								<p class="text-red-600 text-sm" v-if="showError && form.name === ''">Seller name is required</p>
							</div>
							<div id="customer-email-group" class="mb-6">
								<label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
								<input type="email" id="email"
									   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
									   placeholder="Seller email" v-model="form.email">
								<p class="text-red-600 text-sm" v-if="showError && form.email === ''">Seller email is required</p>
							</div>
							<div id="customer-password-group" class="mb-6">
								<div class="mb-2">
									<label for="password"
										   class="block text-sm font-medium text-gray-900">Password</label>
									<p class="text-sm text-gray-400">Default password is '<span class="italic font-bold">VertexSeller</span>'. Seller can change the password afterwards.</p>
								</div>
								<input type="password" id="password" disabled
									   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 disabled:bg-gray-300"
									   placeholder="Seller password" value="VertexSeller">
							</div>
						</div>
						<div class="py-6 mx-6 flex items-center">
							<Link :href="route('sellers.index')"
								  class="font-medium text-blue-600 dark:text-blue-500 hover:underline hidden md:inline-flex items-center">
								<font-awesome-icon icon="angle-left" class="mr-1"/>
								Back to Sellers Listing
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
