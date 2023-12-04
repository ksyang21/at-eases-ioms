<script setup>
import {Head, Link, router} from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {inject, reactive, ref} from "vue";

const Swal = inject('$swal')

const props = defineProps({
	errors: Object,
	breadcrumbs: Object
})

let form = reactive({
	name: '',
	description: '',
	document: null
})

let documentPreview = ref('')

let canSubmit = ref(true)
let showError = ref(false)

function selectDocument() {
	document.getElementById('document-input').click()
}

function removeDocument() {
	form.document = null
	documentPreview.value = ''
}

function preview(e) {
	const file = e.target.files[0]
	documentPreview.value = URL.createObjectURL(file)
	form.document = file
}

function validateForm() {
    return form.document !== null && form.name !== '' && form.description !== ''
}
function submitForm() {
    canSubmit.value = true
    if(validateForm()) {
        router.post('/document', form)
    } else {
        canSubmit.value = false
        Swal.fire({
            text: 'All fields are required!',
            icon: 'error'
        })
    }
}

</script>

<template>

	<Head title="Upload Document"/>

	<AuthenticatedLayout>
		<Breadcrumb :breadcrumbs="breadcrumbs"></Breadcrumb>
		<div class="py-0">
			<div class="mx-auto sm:px-6 lg:px-8">
				<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
					<div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
						<p class="text-2xl">Upload Document</p>
					</div>
					<form @submit.prevent="submitForm()">
						<div class="p-6 border-b-2 border-gray-100">
							<div class="md:flex md:items-center mb-0 md:mb-3">
								<div class="w-full flex flex-col items-center">
									<div
										class="w-full min-h-[500px] border-2 border-dashed border-gray-300 bg-gray-100 flex flex-col justify-center items-center cursor-pointer hover:bg-gray-200"
										@click="selectDocument" v-if="documentPreview === ''">
										<font-awesome-icon icon="file" class="text-5xl text-gray-400"/>
										<p class="mt-4 text-gray-400"><span
											class="text-blue-500 hover:underline">Click</span> to select document</p>
										<input type="file" id="document-input" class="hidden" @change="preview">
									</div>
									<div class="w-full min-h-[500px] flex flex-col justify-center items-center" v-if="documentPreview !== ''">
										<iframe :src="documentPreview" frameborder="0" class="w-full min-h-[500px]"></iframe>
										<button type="button" class="mt-3 text-white py-1 px-2.5 rounded bg-red-600 flex items-center" @click="removeDocument">
											<font-awesome-icon icon="times" class="mr-2" />
											Remove
										</button>
									</div>
								</div>
							</div>
							<div class="md:flex md:items-center mb-0">
								<div class="flex flex-col w-full mt-3">
									<div id="name-group" class="mb-6">
										<label for="name" class="block mb-2 text-sm font-medium text-gray-900">Document
											Name</label>
										<div class="relative">
											<div
												class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
												<font-awesome-icon icon="font"
																   class="w-4 h-4 text-gray-500 dark:text-gray-400"/>
											</div>
											<input type="text" id="name"
												   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
												   placeholder="Document Name" v-model="form.name">
										</div>
										<p class="text-red-600 text-sm" v-if="showError && form.name === ''">Document
											name is required</p>
									</div>
									<div id="description-group" class="mb-6">
										<label for="message"
											   class="block mb-2 text-sm font-medium text-gray-900">Description</label>
										<textarea id="message" rows="4"
												  class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
												  placeholder="Description"
												  v-model="form.description"></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="flex items-center py-4 mx-6 border-b-2 border-gray-100">
							<Link :href="route('documents.index')"
								  class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
								<font-awesome-icon icon="angle-left" class="mr-1"/>
								Back to Document Listing
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
