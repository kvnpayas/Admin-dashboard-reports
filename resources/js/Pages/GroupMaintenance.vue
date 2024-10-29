<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import Pagination from '../Partial/Pagination.vue';

const props = defineProps({
  groups: Object,
  reports: Object,
});
const groups = ref(props.groups)
const reports = ref(props.reports)
const selectedGroup = ref({})

const modals = ref({});
const form = useForm({
  name: null,
  description: null,
});
const formEdit = useForm({
  id: null,
  name: null,
  description: null,
});

const showModal = (id) => {
  modals.value[id] = true;
};

const hideModal = (id) => {
  modals.value[id] = false;
  form.clearErrors()
  formEdit.clearErrors()
  formEdit.name = null
  formEdit.description = null
  selectedGroup.value = null
};

const createGroup = (modal) => {
  form.post('/group-maintenance', {
    onSuccess: (page) => {
      groups.value = page.props.groups;
      form.name = null
      form.description = null
      hideModal(modal)
    }
  })
}

const showEditModal = (id, group) => {
  modals.value[id] = true;
  formEdit.id = group.id
  formEdit.name = group.name
  formEdit.description = group.description
};

const editGroup = (modal) => {
  formEdit.patch('/group-maintenance/' + formEdit.id, {
    onSuccess: (page) => {
      groups.value = page.props.groups;
      formEdit.name = null
      formEdit.description = null
      hideModal(modal)
    }
  })
}

const viewReportModal = (id, group) => {
  modals.value[id] = true;
  selectedGroup.value = group
  reports.value = reports.value.map((report) => {
    const exists = group.reports.some((groupReport) => groupReport.pivot.report_id == report.id);
    return {
      ...report,
      check: exists,
    };
  })
};

const saveReports = (modal) => {
  router.post('/group-maintenance/' + selectedGroup.value.id, reports.value, {
    onSuccess: (page) => {
      groups.value = page.props.groups;
      hideModal(modal)
    }
  })
}

</script>
<template>
  <div class="shadow-inner py-5">
    <div class="max-w-7xl mx-auto my-5">
      <div class="bg-main p-4 rounded-t-md flex">
        <h3 class="text-2xl font-extrabold text-light uppercase">Group Maintenance</h3>
        <div class="ml-auto space-x-2">
          <button @click="showModal('addGroupModal')"
            class="bg-light mt-1.5 px-4 py-1 rounded-md uppercase font-extrabold text-primary hover:bg-gray-300 text-xs"><v-icon
              name="ri-group-fill" class="text-green-500 mr-2" scale="0.9"></v-icon>Create Group</button>
        </div>
      </div>

      <div class="shadow">
        <table class="table-auto w-full">
          <thead class="text-left uppercase text-xs bg-light text-accent font-bold ">
            <tr>
              <th class="px-2 py-2">Id</th>
              <th class="px-2 py-2">Name</th>
              <th class="px-2 py-2">Description</th>
              <th class="px-2 py-2">Registered Reports</th>
              <th class="px-2 py-2">Action</th>
            </tr>
          </thead>
          <tbody class="text-xs text-white bg-main bg-opacity-70">
            <tr v-for="group in groups" :key="group.id" class="border-b-2">
              <td class="px-2 py-2 font-black">{{ group.id }}</td>
              <td class="px-2 py-2">{{ group.name }}</td>
              <td class="px-2 py-2">{{ group.description }}</td>
              <td class="px-2 py-2">
                {{ group.reports.length }}
              </td>
              <td>
                <button @click="showEditModal('editGroupModal', group)">
                  <v-icon name="fa-user-edit" class="text-green-500"></v-icon>
                </button>
                <button @click="viewReportModal('viewReportModal', group)">
                  <v-icon name="co-playlist-add" class="text-yellow-500"></v-icon>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Add Group -->
  <div id="addGroupModal"
    class="modal overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-100% max-h-full"
    :class="{ 'modal-hide': !modals['addGroupModal'], 'modal-show': modals['addGroupModal'] }">
    <div class="relative p-4 w-full max-w-xl max-h-full modal-content"
      :class="{ 'modal-content-hide': !modals['addGroupModal'], 'modal-content-show': modals['addGroupModal'] }">
      <div class="relative  rounded-lg shadow-xl">
        <div class="flex items-center justify-between p-4 border-b-2 border-secondary rounded-t bg-primary">
          <h3 class="text-xl font-extrabold text-light">
            Create Group
          </h3>
          <button type="button" @click="hideModal('addGroupModal')"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-4 h-4 ms-auto inline-flex justify-center items-center">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <div class="p-5 bg-white flex flex-col space-y-5">
          <div>
            <div class="flex flex-col space-y-2">
              <label for="" class="text-sm font-semibold text-primary">Name</label>
              <input type="text" v-model="form.name"
                class="bg-neutral-100 w-full rounded shadow focus:outline-secondary p-1.5 text-xs border border-light">
              <small class="text-red-500">{{ form.errors.name }}</small>
            </div>
          </div>
          <div>
            <div class="flex flex-col space-y-2">
              <label for="" class="text-sm font-semibold text-primary">Description</label>
              <textarea v-model="form.description"
                class="bg-neutral-100 w-full rounded shadow focus:outline-secondary p-1.5 text-xs border border-light"></textarea>
              <small class="text-red-500">{{ form.errors.description }}</small>
            </div>
          </div>
        </div>
        <div class="p-5 bg-white border-t-2 rounded-b flex justify-center items-center space-x-5">
          <button class="px-5 py-1 rounded border  text-white font-bold shadow "
            :class="{ 'bg-light': form.processing, 'bg-green-500 hover:bg-green-600': !form.processing }"
            @click="createGroup('addGroupModal')" :disabled="form.processing">Create</button>
          <button class="px-5 py-1 rounded border bg-red-500 text-white font-bold shadow hover:bg-red-600"
            @click="hideModal('addGroupModal')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Group -->

  <!-- Edit Group -->
  <div id="editGroupModal"
    class="modal overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-100% max-h-full"
    :class="{ 'modal-hide': !modals['editGroupModal'], 'modal-show': modals['editGroupModal'] }">
    <div class="relative p-4 w-full max-w-xl max-h-full modal-content"
      :class="{ 'modal-content-hide': !modals['editGroupModal'], 'modal-content-show': modals['editGroupModal'] }">
      <div class="relative  rounded-lg shadow-xl">
        <div class="flex items-center justify-between p-4 border-b-2 border-secondary rounded-t bg-primary">
          <h3 class="text-xl font-extrabold text-light">
            Edit Group
          </h3>
          <button type="button" @click="hideModal('editGroupModal')"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-4 h-4 ms-auto inline-flex justify-center items-center">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <div class="p-5 bg-white flex flex-col space-y-5">
          <div>
            <div class="flex flex-col space-y-2">
              <label for="" class="text-sm font-semibold text-primary">Name</label>
              <input type="text" v-model="formEdit.name"
                class="bg-neutral-100 w-full rounded shadow focus:outline-secondary p-1.5 text-xs border border-light">
              <small class="text-red-500">{{ formEdit.errors.name }}</small>
            </div>
          </div>
          <div>
            <div class="flex flex-col space-y-2">
              <label for="" class="text-sm font-semibold text-primary">Description</label>
              <textarea v-model="formEdit.description"
                class="bg-neutral-100 w-full rounded shadow focus:outline-secondary p-1.5 text-xs border border-light"></textarea>
              <small class="text-red-500">{{ formEdit.errors.description }}</small>
            </div>
          </div>
        </div>
        <div class="p-5 bg-white border-t-2 rounded-b flex justify-center items-center space-x-5">
          <button class="px-5 py-1 rounded border  text-white font-bold shadow "
            :class="{ 'bg-light': formEdit.processing, 'bg-green-500 hover:bg-green-600': !formEdit.processing }"
            @click="editGroup('editGroupModal')" :disabled="formEdit.processing">Update</button>
          <button class="px-5 py-1 rounded border bg-red-500 text-white font-bold shadow hover:bg-red-600"
            @click="hideModal('editGroupModal')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Edit Group -->

  <!-- View Reports -->
  <div id="viewReportModal"
    class="modal overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-100% max-h-full"
    :class="{ 'modal-hide': !modals['viewReportModal'], 'modal-show': modals['viewReportModal'] }">
    <div class="relative p-4 w-full max-w-xl max-h-full modal-content"
      :class="{ 'modal-content-hide': !modals['viewReportModal'], 'modal-content-show': modals['viewReportModal'] }">
      <div class="relative  rounded-lg shadow-xl">
        <div class="flex items-center justify-between p-4 border-b-2 border-secondary rounded-t bg-primary">
          <h3 class="text-xl font-extrabold text-light">
            List of Reports
          </h3>
          <button type="button" @click="hideModal('viewReportModal')"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-4 h-4 ms-auto inline-flex justify-center items-center">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <div class="py-5 bg-white flex flex-col space-y-5">
          <table class="table-auto w-full">
            <thead class="text-left uppercase text-xs bg-light text-accent font-bold ">
              <tr>
                <th class="px-2 py-2">Id</th>
                <th class="px-2 py-2">Name</th>
                <th class="px-2 py-2">Component</th>
                <th class="px-2 py-2">Select</th>
              </tr>
            </thead>
            <tbody class="text-xs text-accent bg-white bg-opacity-70">
              <tr v-for="report in reports" :key="report.id" class="border-b-2">
                <td class="px-2 py-2 font-black">{{ report.id }}</td>
                <td class="px-2 py-2">{{ report.name }}</td>
                <td class="px-2 py-2">{{ report.component }}</td>
                <td>
                  <input v-model="report.check" type="checkbox" value="" >
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="p-5 bg-white border-t-2 rounded-b flex justify-center items-center space-x-5">
          <button class="px-5 py-1 rounded border  text-white font-bold shadow "
            :class="{ 'bg-light': formEdit.processing, 'bg-green-500 hover:bg-green-600': !formEdit.processing }"
            @click="saveReports('viewReportModal')" :disabled="formEdit.processing">Save</button>
          <button class="px-5 py-1 rounded border bg-red-500 text-white font-bold shadow hover:bg-red-600"
            @click="hideModal('viewReportModal')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End View Reports -->
</template>