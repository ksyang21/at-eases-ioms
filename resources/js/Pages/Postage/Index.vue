<script setup>

import {Head, Link} from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
	breadcrumbs: Object,
	postages: Object
})
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
						<div class="flex flex-col">
							<p class="text-xl">Postage Settings</p>
						</div>
						<Link :href="route('seller.create')"
							  class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 ml-auto flex items-center">
							<font-awesome-icon icon="plus-circle" class="mr-2"/>
							New Option
						</Link>
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
	</AuthenticatedLayout>
</template>

<style scoped>
.option-action-btn {
	font-size: 20px;
	cursor: pointer;
}
</style>
