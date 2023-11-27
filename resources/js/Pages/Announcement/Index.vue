<script setup>

import {Head, Link, router} from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {inject, reactive, ref} from "vue";

const props = defineProps({
	breadcrumbs: Object,
	announcements: Object,
	activeAnnouncements: Object,
	categories: Object
})
const Swal = inject('$swal')

let filteredAnnouncements = ref(props.announcements)
let filteredCategories = ref(props.categories)
let announcementSearch = ref('')
let categorySearch = ref('')

function searchAnnouncement() {
	filteredAnnouncements.value = props.announcements.filter((announcement) => {
		const announcementText = announcement.announcement.toLowerCase().includes(announcementSearch.value.toLowerCase())
		const categoryText = announcement.category.category.toLowerCase().includes(announcementSearch.value.toLowerCase())
		// return announcement.announcement.toLowerCase().includes(announcementSearch.value.toLowerCase())
		return announcementText || categoryText
	})
}

function searchCategory() {
	filteredCategories.value = props.categories.filter((category) => {
		return category.category.toLowerCase().includes(categorySearch.value.toLowerCase())
	})
}

let viewMode = ref('announcements')

function changeViewMode(mode) {
	viewMode.value = mode
}

let isModalOpen = ref(false)

function openNewAnnouncement() {
	document.getElementById('newAnnouncementModal').classList.remove('hidden')
	isModalOpen.value = true
}

function closeModal() {
	document.getElementById('newAnnouncementModal').classList.add('hidden')
	isModalOpen.value = false
}

function activateAnnouncement(announcement) {
	Swal.fire({
		title: 'Activate this announcement?',
		text: 'It will be shown to all sellers.',
		icon: 'info',
		showCancelButton: true
	}).then((result) => {
		if(result.isConfirmed) {
			router.put(`/activate-announcement/${announcement.id}`)
		}
	})
}

function deactivateAnnouncement(announcement) {
	Swal.fire({
		title: 'Deactivate this announcement?',
		text: 'It will not be shown.',
		icon: 'info',
		showCancelButton: true
	}).then((result) => {
		if(result.isConfirmed) {
			router.put(`/deactivate-announcement/${announcement.id}`)
		}
	})
}

function removeAnnouncement(announcement) {
	Swal.fire({
		title: 'Remove this announcement?',
		text: 'You cannot undo this!',
		icon: 'warning',
		showCancelButton: true
	}).then((result) => {
		if(result.isConfirmed) {
			router.delete(`/announcement/${announcement.id}`);
		}
	})
}
</script>

<template>
	<Head title="Announcement Management"/>
	<AuthenticatedLayout>
		<template #header>
			<h2 class="font-semibold text-xl text-gray-800 leading-tight">Announcement Management</h2>
		</template>
		<Breadcrumb :breadcrumbs="breadcrumbs"/>
		<div class="py-0">
			<div class="mx-auto sm:px-6 lg:px-8">
				<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
					<div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
						<div class="flex flex-col">
							<p class="text-2xl font-bold">Announcement Settings</p>
							<div class="block md:flex md:items-center mt-6">
								<div class="flex items-center" id="status-bar">
									<p class="status-item" :class="viewMode === 'announcements' ? 'active-status' : ''"
									   @click="changeViewMode('announcements')">Announcements</p>
									<p class="status-item" :class="viewMode === 'categories' ? 'active-status' : ''"
									   @click="changeViewMode('categories')">Categories</p>
								</div>
							</div>
						</div>
					</div>
					<div class="p-6" v-if="viewMode === 'announcements'">
						<div class="flex items-center">
							<div id="announcements-search-bar" class="w-[500px]">
								<label for="announcements-search" class="text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
								<div class="relative">
									<div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
										<svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
											 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
											<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
												  stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
										</svg>
									</div>
									<input type="search" id="announcements-search" @input="searchAnnouncement" v-model="announcementSearch"
										   class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 "
										   placeholder="Search Announcement / Category">
								</div>
							</div>
							<button @click="openNewAnnouncement"
									class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm p-2 mr-2 flex items-center ml-auto">
								<font-awesome-icon icon="plus-circle" class="mr-2"/>
								New Announcement
							</button>
						</div>
						<div class="relative overflow-x-auto sm:rounded-lg mt-3">
							<table
								   class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border-2 border-gray-100">
								<thead
									class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
								<tr>
									<th scope="col" class="px-6 py-3">
										Announcement
									</th>
									<th scope="col" class="px-6 py-3">
										Category
									</th>
									<th scope="col" class="px-6 py-3">
										Action
									</th>
								</tr>
								</thead>
								<tbody v-if="filteredAnnouncements.length > 0">
								<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50"
									v-for="(announcement, index) in filteredAnnouncements" :key="index">
									<td class="px-6 py-4">
										<span class="mr-6">{{ announcement.announcement }}</span>
										<span class="py-0.5 px-2 bg-green-300 text-green-700 rounded-lg"
											  v-if="announcement.status === 'active'">{{
												announcement.status.toUpperCase()
											}}</span>
										<span class="py-0.5 px-2 bg-red-300 text-red-700 rounded-lg"
											  v-else>{{ announcement.status.toUpperCase() }}</span>
									</td>
									<td class="px-6 py-4">
										<span class="font-bold mr-6">{{ announcement.category.category }}</span>
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
										<div class="relative flex flex-col items-center group ml-6" v-if="announcement.status === 'active'">
											<font-awesome-icon icon="ban" @click="deactivateAnnouncement(announcement)"
															   class="option-action-btn text-orange-400 hover:text-orange-600"/>
											<div
												class="absolute bottom-0 flex flex-col items-center hidden mb-6 group-hover:flex">
                                                <span
													class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg">Deactivate</span>
												<div class="w-3 h-3 -mt-2 rotate-45 bg-black"></div>
											</div>
										</div>
										<div class="relative flex flex-col items-center group ml-6" v-if="announcement.status === 'inactive'">
											<font-awesome-icon icon="check-circle" @click="activateAnnouncement(announcement)"
															   class="option-action-btn text-blue-600 hover:text-blue-800"/>
											<div
												class="absolute bottom-0 flex flex-col items-center hidden mb-6 group-hover:flex">
                                                <span
													class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg">Activate</span>
												<div class="w-3 h-3 -mt-2 rotate-45 bg-black"></div>
											</div>
										</div>
										<div class="relative flex flex-col items-center group ml-6">
											<font-awesome-icon icon="trash" @click="removeAnnouncement(announcement)"
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
									<td colspan="4" class="px-6 py-4 text-center">No announcement found</td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="p-6" v-if="viewMode === 'categories'">
						<div class="flex items-center">
							<div id="categories-search-bar" class="w-[500px]">
								<label for="categories-search" class="text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
								<div class="relative">
									<div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
										<svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
											 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
											<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
												  stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
										</svg>
									</div>
									<input type="search" id="categories-search" @input="searchCategory" v-model="categorySearch"
										   class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 "
										   placeholder="Search Category">
								</div>
							</div>
							<button @click="openNewAnnouncement"
									class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm p-2 mr-2 flex items-center ml-auto">
								<font-awesome-icon icon="plus-circle" class="mr-2"/>
								New Category
							</button>
						</div>
						<div class="relative overflow-x-auto sm:rounded-lg mt-3">
							<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border-2 border-gray-100">
								<thead
									class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
								<tr>
									<th scope="col" class="px-6 py-3">
										Category
									</th>
									<th scope="col" class="px-6 py-3">
										Action
									</th>
								</tr>
								</thead>
								<tbody v-if="filteredCategories.length > 0">
								<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50"
									v-for="(category, index) in filteredCategories" :key="index">
									<th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
										<span class="font-bold mr-6">{{ category.category }}</span>
									</th>
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
											<font-awesome-icon icon="trash"
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
									<td colspan="4" class="px-6 py-4 text-center">No category found</td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div v-if="isModalOpen" class="fixed inset-0 bg-gray-800 bg-opacity-50"></div>
		<div id="newAnnouncementModal"
			 class="w-full md:w-1/3 hidden fixed inset-y-0 right-0 bg-white flex flex-col shadow-2xl">
			<div class="top-0 p-6 border-b-2 border-gray-100 flex items-center">
				<p class="text-2xl">Make New Announcement</p>
				<button class="text-red-600 ml-auto" @click="closeModal">
					<font-awesome-icon icon="times-circle"/>
				</button>
			</div>
			<div class="bg-white p-6 rounded-lg">
			</div>
		</div>
	</AuthenticatedLayout>
</template>

<style scoped>
.option-action-btn {
	font-size: 20px;
	cursor: pointer;
}

.status-item {
	margin-left: 16px;
	margin-right: 16px;
}

.status-item.active-status {
	color: #d71d28;
}

.status-item:hover {
	color: #ef4444;;
	cursor: pointer;
}
</style>
