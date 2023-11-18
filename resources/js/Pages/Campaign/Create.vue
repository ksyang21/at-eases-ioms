<script setup>

import {Head, Link, router} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {inject, reactive, ref} from "vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
	breadcrumbs: Object,
    errors: Object
})

const Swal = inject('$swal')

const form = reactive({
	name: '',
	date: [],
	targetAmount: 0.00,
})


const formatDate = ([date1, date2]) => {
	const startDay = date1.getDate()
	const startMonth = date1.getMonth() + 1
	const startYear = date1.getFullYear()
	if(!date2) {
		return `${startDay}/${startMonth}/${startYear}`
	}
	const endDay = date2.getDate()
	const endMonth = date2.getMonth() + 1
	const endYear = date2.getFullYear()

	return `${startDay}/${startMonth}/${startYear} - ${endDay}/${endMonth}/${endYear}`
}

let showError = ref(false)

function validateCampaign() {
	showError.value = false
	if(form.name === '' || !form.date[0] || !form.date[1] || form.targetAmount === 0.00) {
		showError.value = true
		return false
	}
	return true
}
function createCampaign() {
	if(validateCampaign()) {
		let formData = new FormData()
		formData.append('name', form.name)
		formData.append('target_amount', form.targetAmount)
		formData.append('period_start', form.date[0].toUTCString())
		formData.append('period_end', form.date[1].toUTCString())
		router.post('/campaign', formData)
	} else {
		Swal.fire({
			text: 'Missing required data!',
			icon: 'error'
		})
	}
}
</script>

<template>

	<Head title="New Campaign"/>

	<AuthenticatedLayout>
		<Breadcrumb :breadcrumbs="breadcrumbs"></Breadcrumb>
        <div v-if="errors.length > 0" class="px-8 py-6">
            <p class="text-red-600 text-xl" v-for="(error, index) in errors" :key="index">{{error}}</p>
        </div>
		<div class="py-0">
			<div class="mx-auto sm:px-6 lg:px-8">
				<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
					<div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
						<p class="text-2xl">New Campaign</p>
					</div>
					<form @submit.prevent="createCampaign">
						<div class="p-6 border-b-2 border-gray-100">
							<div id="name-group" class="mb-6">
								<label for="name" class="block mb-2 text-sm font-medium text-gray-900">Campaign
									Name</label>
								<div class="relative">
									<div
										class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
										<font-awesome-icon icon="font"
														   class="w-4 h-4 text-gray-500 dark:text-gray-400"/>
									</div>
									<input type="text" id="name"
										   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
										   placeholder="Campaign Name" v-model="form.name">
								</div>
								<p class="text-red-600 text-sm" v-if="showError && form.name === ''">Campaign
									name is required</p>
							</div>
							<div id="campaign-date-section" class="mb-6">
								<label for="campaign-period" class="block mb-2 text-sm font-medium text-gray-900">Campaign Period</label>
								<VueDatePicker
									id="campaign-period"
									range
									v-model="form.date"
									:format="formatDate"
									:enable-time-picker="false"
									:clearable="false"
									:teleport="true"
                                    placeholder="Start Date - End Date"
								/>
								<p class="text-red-600 text-sm" v-if="showError && (!form.date[0] || !form.date[1])">Please select campaign start date and end date.</p>
							</div>
							<div id="target-amount-section">
								<label for="target-amount" class="block mb-2 text-sm font-medium text-gray-900">Target
									Amount (MYR)</label>
								<input type="number" id="target-amount"
									   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
									   placeholder="Campaign Target Amount" required v-model="form.targetAmount"
									   step="0.01">
								<p class="text-red-600 text-sm" v-if="showError && form.targetAmount <= 0.00">Target
									Amount must be provided</p>
							</div>
						</div>
						<div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
							<Link :href="route('campaigns.index')"
								  class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
								<font-awesome-icon icon="angle-left" class="mr-1"/>
								Back to Campaigns
							</Link>
							<button
								class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-auto flex items-center">
								<font-awesome-icon icon="check-circle" class="mr-2"/>
								Create
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
