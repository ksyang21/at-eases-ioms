<script setup>

import {Head, Link} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import moment from 'moment/moment.js';

const props = defineProps({
	breadcrumbs: Object,
	customers: Object
})
</script>

<template>
	<Head title="Customers"/>
	<AuthenticatedLayout>
		<Breadcrumb :breadcrumbs="breadcrumbs"></Breadcrumb>
		<div class="py-0">
			<div class="mx-auto sm:px-6 lg:px-8">
				<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
					<div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
						<div class="flex flex-col">
							<p class="text-xl">Customers</p>
							<p class="text-sm text-gray-600">Total <b>{{customers.length}}</b> customers</p>
						</div>
						<Link :href="route('customer.create')"
							  class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 ml-auto flex items-center">
							<font-awesome-icon icon="plus-circle" class="mr-2"/>
							New Customer
						</Link>
					</div>
					<div class="p-6">
						<div class="relative overflow-x-auto sm:rounded-lg mt-4">
							<table
								class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border-2 border-gray-100">
								<thead
									class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
								<tr>
									<th scope="col" class="px-6 py-3">
										Name
									</th>
									<th scope="col" class="px-6 py-3">
										Phone
									</th>
									<th scope="col" class="px-6 py-3">
										Email
									</th>
									<th scope="col" class="px-6 py-3">
										Join Date
									</th>
									<th scope="col" class="px-6 py-3">
										Action
									</th>
								</tr>
								</thead>
								<tbody v-if="customers.length > 0">
								<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
									v-for="(customer, index) in customers" :key="index">
									<th scope="row"
										class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
										<p class="font-semibold">{{ customer.name }}</p>
										<p class="text-gray-600 text-sm">{{ customer.address }}</p>
									</th>
									<td class="px-6 py-4">
										{{ customer.phone }}
									</td>
									<td class="px-6 py-4">
										{{customer.email}}
									</td>
									<td class="px-6 py-4">
										{{moment(customer.created_at).format('YYYY-MM-DD')}}
									</td>
									<td class="px-6 py-4">
										<font-awesome-icon icon="pen-to-square" class="ml-2"/>
										<font-awesome-icon icon="trash" class="ml-2 text-red-600"/>
									</td>
								</tr>
								</tbody>
								<tbody v-else>
								<tr>
									<td colspan="4" class="px-6 py-4 text-center">No package available</td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</AuthenticatedLayout>

</template>

<style scoped>

</style>
