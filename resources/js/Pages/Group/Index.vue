<script setup>
import {Head, Link} from '@inertiajs/vue3';
import Breadcrumb from "@/Components/Breadcrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {reactive, ref} from "vue";

const props = defineProps({
    breadcrumbs: Object,
    groups: Object,
    categorized_groups: Object
})

let filteredGroups = ref(props.groups)
let search = ref('')

function searchGroup() {
    const searchValue = search.value.toLowerCase()
    filteredGroups.value = props.groups.filter((group) => {
        const sellerSearch = searchValue ?
            group.members.filter((member) => {
                return member.seller.name.toLowerCase().includes(searchValue)
            }) : true
        const groupSearch = searchValue ? group.name.toLowerCase().includes(searchValue) : true

        return sellerSearch.length || groupSearch
    })
}

let isModalOpen = ref(false)
let modalGroupDetails = reactive({
    id: 0,
    name: '',
    total_pv: 0,
    total_order: 0,
    members: [],
    leader: {},
    staffs: [],
})

let groupPvRecords = ref({})
let groupDetailsLoading = ref(true)

async function showGroupDetail(group) {
    document.getElementById('groupDetailModal').classList.remove('hidden')
    modalGroupDetails.id = group.id
    modalGroupDetails.name = group.name
    modalGroupDetails.total_pv = group.total_pv
    modalGroupDetails.members = group.members
    modalGroupDetails.leader = group.members.find((member) => {
        return member.is_leader === 'yes'
    })
    modalGroupDetails.staffs = group.members.filter((member) => {
        return member.is_leader === 'no'
    })
    isModalOpen.value = true

    groupDetailsLoading.value = true
    await axios.get(`/api/group-sales-records/${modalGroupDetails.id}`)
        .then((result) => {
            groupDetailsLoading.value = false
            groupPvRecords.value = result.data

            modalGroupDetails.total_order = groupPvRecords.value.length
        })
}

function closeModal() {
    document.getElementById('groupDetailModal').classList.add('hidden')
    isModalOpen.value = false
}
</script>

<template>
    <Head title="Group Management"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">All Groups</h2>
        </template>
        <Breadcrumb :breadcrumbs="breadcrumbs"/>
        <div class="py-0">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
                        <div class="flex flex-col">
                            <p class="text-2xl font-bold">Group Management</p>
                            <p class="text-sm text-gray-600">Total <b>{{ groups.length }}</b> groups</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div id="search-bar" class="mb-4">
                            <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input type="search" id="search"
                                       class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 "
                                       placeholder="Search group / seller name" @keyup="searchGroup"
                                       v-model="search">
                            </div>
                        </div>
                        <div class="grid gap-3 mb-6 md:grid-cols-3" v-if="filteredGroups.length > 0">
                            <div @click="showGroupDetail(group)"
                                 :class="{
                                     'border-l-8 border-l-yellow-300': index === 0,
                                     'border-l-8 border-l-gray-200': index === 1,
                                     'border-l-8 border-l-orange-600': index === 2,
                                     'border-l-[1px]': index > 2
                                 }"
                                 class="flex flex-col w-full h-full mt-4 md:mt-0 border-[1px] border-gray-300 group-card"
                                 v-for="(group, index) in filteredGroups" :key="index">
                                <div class="flex items-center py-4 px-6 border-b-2 border-gray-200">
                                    <div class="flex flex-col">
                                        <div class="relative group">
                                            <p class="text-xl font-bold overflow-hidden whitespace-nowrap overflow-ellipsis max-w-[100px] xl:max-w-[300px]">
                                                {{ group.name }}</p>
                                            <div
                                                class="absolute bottom-0 flex flex-col items-center hidden mb-6 group-hover:flex">
                                                <span
                                                    class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg">{{
                                                        group.name
                                                    }}</span>
                                                <div class="w-3 h-3 -mt-2 rotate-45 bg-black"></div>
                                            </div>
                                        </div>
                                        <div v-if="group.members.length > 0">
                                            <div v-for="(member, index) in group.members" :key="index">
                                                <p class="text-gray-600 text-sm" v-if="member.is_leader === 'yes'">
                                                    {{ member.seller.name }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="ml-auto px-4 rounded-lg"
                                          :class="group.status === 'active' ? 'bg-green-300 text-green-700' : 'bg-red-300 text-red-700'">{{
                                            group.status.toUpperCase()
                                        }}</span>
                                </div>
                                <div class="p-6">
                                    <div class="flex items-center">
                                        <div class="flex flex-col">
                                            <p class="text-sm text-gray-400">Total PV</p>
                                            <p class="text-gray-700 font-semibold">{{ group.total_pv }}</p>
                                        </div>
                                        <div class="flex flex-col ml-32">
                                            <p class="text-sm text-gray-400">Members</p>
                                            <p class="text-gray-700 font-semibold">{{ group.members.length }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="w-full text-center py-6">
                            <p class="text-3xl">No group found</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="isModalOpen" class="fixed inset-0 bg-gray-800 bg-opacity-50"></div>
        <div id="groupDetailModal"
             class="w-full md:w-1/2 hidden overflow-y-scroll fixed inset-y-0 right-0 bg-white flex flex-col shadow-2xl">
            <div class="sticky top-0 bg-white p-6 border-b-2 border-gray-100 flex items-center">
                <p class="text-2xl">Group Details</p>
                <button class="text-red-600 ml-auto" @click="closeModal">
                    <font-awesome-icon icon="times-circle"/>
                </button>
            </div>
            <div role="status" v-if="groupDetailsLoading" class="flex items-center h-full justify-center">
                <svg aria-hidden="true" class="inline w-64 h-64 text-gray-200 animate-spin fill-red-600"
                     viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="currentColor"/>
                    <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentFill"/>
                </svg>
                <span class="sr-only">Loading...</span>
            </div>
            <div class="bg-white pb-6 px-6 rounded-lg" v-else>
                <div id="group-details-name-section">
                    <div class="flex items-center p-6">
                        <div class="border-r-2 border-gray-300 w-1/2 h-full p-16">
                            <p class="text-5xl font-bold">{{ modalGroupDetails.name }}</p>
                        </div>
                        <div class="flex flex-col p-16">
                            <div>
                                <p class="text-sm text-gray-400">Total PV</p>
                                <p class="text-xl">{{ modalGroupDetails.total_pv }}</p>
                            </div>
                            <div class="mt-6">
                                <p class="text-sm text-gray-400">Total Orders</p>
                                <p class="text-xl">{{ modalGroupDetails.total_order }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden border-2 rounded-lg">
                        <div class="flex items-center py-4 px-4 border-b-2 border-gray-100">
                            <div class="flex flex-col">
                                <p class="font-bold">Members <span class="py-1 px-2 bg-gray-200 ml-1 rounded-md"><b>{{
                                        modalGroupDetails.members.length
                                    }}</b></span></p>
                            </div>
                        </div>
                        <div id="modal-members-list">
                            <ul class="w-full text-sm font-medium text-gray-900 bg-white">
                                <li class="w-full px-4 py-2 border-b border-gray-200">
                                    {{
                                        Object.entries(modalGroupDetails.leader).length > 0 ? modalGroupDetails.leader.seller.name : ''
                                    }}
                                    <span class="py-1 px-2 bg-red-500 ml-1 rounded-md text-white">Leader</span>
                                </li>
                                <li class="w-full px-4 py-2 border-b border-gray-200"
                                    v-for="(member, index) in modalGroupDetails.staffs">
                                    {{ member.seller.name }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden border-2 rounded-lg mt-6" id="pv-records-section">
                    <div class="flex items-center py-4 px-4 border-b-2 border-gray-100">
                        <div class="flex flex-col">
                            <p class="font-bold">Sales Records</p>
                        </div>
                    </div>
                    <div v-if="groupPvRecords.length > 0">
                        <ul class="w-full text-sm font-medium text-gray-900 bg-white">
                            <li v-for="(record, index) in groupPvRecords" class="flex items-center px-6 py-2" :key="index">
                                <div>
                                    <p class="text-lg">#{{ record.order_no }}</p>
                                    <p class="text-lg text-gray-500">{{ record.seller.name }}</p>
                                </div>
                                <span class="ml-auto text-lg">RM {{ record.total_price.toFixed(2) }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="text-center p-16 mt-6" v-else>
                        <p class="text-2xl font-semibold">No sales record found</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.action-btn {
    font-size: 20px;
    cursor: pointer;
}

.group-card:hover {
    background-color: #f7f7f7;
    cursor: pointer;
}
</style>
