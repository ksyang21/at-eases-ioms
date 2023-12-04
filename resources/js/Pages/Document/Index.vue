<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {Head, Link} from "@inertiajs/vue3";
import ProgressBar from "@/Components/ProgressBar.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {ref} from "vue";
import moment from "moment/moment.js";

const props = defineProps({
    breadcrumbs: Object,
    documents: Object,
})

let search = ref('')

function searchCampaign() {
}
</script>

<template>
    <Head title="Documents"/>

    <AuthenticatedLayout>
        <Breadcrumb :breadcrumbs="breadcrumbs"></Breadcrumb>
        <div class="py-0">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-3">
                    <div class="p-6">
                        <div class="md:flex md:items-center mb-4">
                            <p class="text-xl mb-3 md:mb-0">
                                {{ documents.length }} Document record{{ documents.length > 1 ? 's' : '' }}
                            </p>
                            <Link :href="route('campaign.create')"
                                  class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2 w-full md:w-auto mt-3 md:mt-0 md:ml-auto md:flex md:items-center w-full">
                                <font-awesome-icon icon="plus-circle" class="mr-2"/>
                                Upload Document
                            </Link>
                        </div>
                        <div id="search-bar">
                            <label for="search"
                                   class="text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input type="search" id="search"
                                       class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 "
                                       placeholder="Search Document" @keyup="searchCampaign"
                                       v-model="search">
                            </div>
                        </div>
                        <div class="relative overflow-x-auto sm:rounded-lg mt-4">
                            <table
                                    class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border-2 border-gray-100">
                                <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        No.
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Document Name
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Uploaded By
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Last Update Date
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Action
                                    </th>
                                </tr>
                                </thead>
                                <tbody v-if="documents.length > 0">
                                <tr v-for="(document, index) in documents" :key="index">
                                    <th class="px-6 py-4">
                                        {{ document.name }}
                                    </th>
                                    <td class="px-6 py-4">{{ document.uploader.name }}</td>
                                    <td class="px-6 py-4">{{ moment(document.created_at).format('YYYY/MM/DD') }}</td>
                                    <td class="px-6 py-4"></td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <td class="py-4 px-6 text-center" colspan="5">
                                        <p class="font-bold">No document found</p>
                                    </td>
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
