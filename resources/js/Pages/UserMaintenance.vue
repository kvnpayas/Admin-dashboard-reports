<script setup>
import { ref } from 'vue';
import Pagination from '../Partial/Pagination.vue';
import Warning from '../Partial/UserMaintenance/UserWarningModal.vue';
import Remove from '../Partial/UserMaintenance/RemoveModal.vue';
import { emit, on } from '../eventBus';
import { router, useForm } from '@inertiajs/vue3';

const props = defineProps({
  users: Object,
  groups: Object,
});

const formEdit = useForm({
  status: null,
  group_id: null,
});

const users = ref(props.users);
const groups = props.groups;

const icssUsers = ref({});
const searchQuery = ref('');
const selectedUser = ref({});
const selectedUserStatus = ref('');
const selectedGroupUser = ref('');

const fetchIcssUsers = async (page = 1, searchQuery = null) => {
  // await new Promise(resolve => setTimeout(resolve, 1000));
  const response = await axios('/user-maintenance/icss-users', {
    params: {
      page: page,
      search: searchQuery,
    }
  })
  icssUsers.value = response.data;
};

const updateUsersList = (newUser, action) => {
  if (action == 'create') {
    users.value.push(newUser);
  }
  if (action == 'remove') {
    const index = users.value.findIndex(user => user.id === newUser.id);
    if (index !== -1) {
      users.value.splice(index, 1);
    }
  }
};

const searchFunction = async () => {
  await new Promise(resolve => setTimeout(resolve, 1000));
  fetchIcssUsers(1, searchQuery.value);
};

const modals = ref({});

const showModal = (id) => {
  if (id === 'addUserModal') {
    fetchIcssUsers();
  }
  modals.value[id] = true;
};

const hideModal = (id) => {
  modals.value[id] = false;
  searchQuery.value = null;
  // selectedUser.value = null
};

function handleClickModal(user, modal) {
  emit(modal, user);
}

on('hideUserLists', () => {
  hideModal('addUserModal');
});

const showSettingModal = (user, modal) => {
  selectedUser.value = user
  selectedUserStatus.value = Boolean(Number(selectedUser.value.status))
  selectedGroupUser.value = selectedUser.value.group_id
  modals.value[modal] = true;
};

const updateUser = (modal) => {
  formEdit.status = selectedUserStatus.value
  formEdit.group_id = selectedGroupUser.value
  console.log(selectedGroupUser.value)
  formEdit.patch('/user-maintenance/' + selectedUser.value.id, {
    onSuccess: (page) => {
      users.value = page.props.users;

      hideModal(modal)
    }
  })
};


</script>

<template>
  <div class="shadow-inner py-5">
    <div class="max-w-7xl mx-auto my-5">
      <div class="bg-main p-4 rounded-t-md flex">
        <h3 class="text-2xl font-extrabold text-light uppercase">User Maintenance</h3>
        <div class="ml-auto space-x-2">
          <!-- <button @click="showModal('createUserModal')"
            class="bg-light mt-1.5 px-4 py-1 rounded-md uppercase font-extrabold text-primary hover:bg-gray-300 text-xs"><v-icon
              name="ri-user-add-fill" class="text-green-500 mr-2" scale="0.9"></v-icon>Create user</button> -->
          <button @click="showModal('addUserModal')"
            class="bg-light mt-1.5 px-4 py-1 rounded-md uppercase font-extrabold text-primary hover:bg-gray-300 text-xs"><v-icon
              name="ri-user-add-fill" class="text-green-500 mr-2" scale="0.9"></v-icon>Add user</button>
        </div>
      </div>

      <div class="shadow">
        <table class="table-auto w-full">
          <thead class="text-left uppercase text-xs bg-light text-accent font-bold ">
            <tr>
              <th class="px-2 py-2">Name</th>
              <th class="px-2 py-2">Username</th>
              <th class="px-2 py-2">Email</th>
              <th class="px-2 py-2">Group</th>
              <th class="px-2 py-2">Status</th>
              <th class="px-2 py-2">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id" class="text-white bg-main bg-opacity-70 border-b text-xs">
              <td class="px-2 py-2 font-bold">{{ user.name }}</td>
              <td class="px-2 py-2">{{ user.username }}</td>
              <td class="px-2 py-2">{{ user.email }}</td>
              <td class="px-2 py-2">{{  user.group ? user.group.name : '' }}</td>
              <td class="px-2 py-2">
                <span class="text-xs rounded-full px-2 py-1-0.5 text-white font-semibold"
                  :class="{ 'bg-green-500': true, 'bg-red-500': user.status == '0' }">{{ user.status == '1' ? 'Active' :
                    'Inactive' }}</span>
              </td>
              <td class="px-2 py-2">
                <div class="space-x-4">
                  <button @click="showSettingModal(user, 'settingUser')"><v-icon name="bi-gear-fill" animation="spin"
                      hover speed="slow" class="text-blue-500"></v-icon></button>
                  <button @click="handleClickModal(user, 'removeModal')"><v-icon name="io-person-remove-sharp"
                      class="text-red-500"></v-icon></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- MODALS -->
  <!-- Create User -->
  <div id="createUserModal"
    class="modal overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-100% max-h-full"
    :class="{ 'modal-hide': !modals['createUserModal'], 'modal-show': modals['createUserModal'] }">
    <div class="relative p-4 w-full max-w-lg max-h-full modal-content"
      :class="{ 'modal-content-hide': !modals['createUserModal'], 'modal-content-show': modals['createUserModal'] }">
      <div class="relative  rounded-lg shadow-xl">
        <div class="flex items-center justify-between p-4 border-b-2 border-secondary rounded-t bg-primary">
          <h3 class="text-xl font-extrabold text-light">
            Create User
          </h3>
          <button type="button" @click="hideModal('createUserModal')"
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
              <input type="text"
                class="bg-neutral-100 w-full rounded shadow focus:outline-secondary p-1.5 text-xs border border-light">
            </div>
          </div>
          <div>
            <div class="flex flex-col space-y-2">
              <label for="" class="text-sm font-semibold text-primary">Email</label>
              <input type="text"
                class="bg-neutral-100 w-full rounded shadow focus:outline-secondary p-1.5 text-xs border border-light">
            </div>
          </div>
        </div>
        <div class="p-5 bg-white border-t-2 rounded-b flex justify-center items-center space-x-5">
          <button class="px-5 py-1 rounded border bg-green-500 text-white font-bold shadow hover:bg-green-600"
            @click="createUsers()">Create</button>
          <button class="px-5 py-1 rounded border bg-red-500 text-white font-bold shadow hover:bg-red-600"
            @click="hideModal('createUserModal')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Create User -->

  <!-- Add User -->
  <div id="addUserModal"
    class="modal overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-100% max-h-full"
    :class="{ 'modal-hide': !modals['addUserModal'], 'modal-show': modals['addUserModal'] }">
    <div class="relative p-4 w-full max-w-7xl max-h-full modal-content"
      :class="{ 'modal-content-hide': !modals['addUserModal'], 'modal-content-show': modals['addUserModal'] }">
      <div class="relative  rounded-lg shadow-xl">
        <div class="flex items-center justify-between p-4 border-b-2 border-secondary rounded-t bg-primary">
          <h3 class="text-xl font-extrabold text-light">
            Add User
          </h3>
          <button type="button" @click="hideModal('addUserModal')"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-4 h-4 ms-auto inline-flex justify-center items-center">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <div class="p-5 bg-white flex flex-col space-y-5">
          <div class="flex">
            <label for="" class="bg-light px-4 py-2 rounded-l-md shadow font-bold text-accent">Search</label>
            <input v-model="searchQuery" @input="searchFunction" type="text" placeholder="Search here"
              class="border py-2 pl-2 text-xs text-accent shadow rounded-r-md outline-secondary">
          </div>
          <table class="table-auto w-full">
            <thead class="text-left uppercase text-xs bg-light text-accent font-bold ">
              <tr>
                <th class="px-2 py-2">Username</th>
                <th class="px-2 py-2">Name</th>
                <th class="px-2 py-2">Employee Id</th>
                <th class="px-2 py-2">Action</th>
              </tr>
            </thead>
            <tbody class="text-xs text-accent">
              <tr v-for="user in icssUsers.data" :key="user.user_id" class="border-b-2">
                <td class="px-2 py-2">{{ user.user_id }}</td>
                <td class="px-2 py-2">{{ user.user_description }}</td>
                <td class="px-2 py-2">{{ user.employee_id }}</td>
                <td class="px-2 py-2">
                  <button @click="handleClickModal(user, 'warningModal')"
                    class="px-4 bg-green-500 text-white rounded-sm shadow font-bold uppercase text-xs">Add</button>
                </td>
              </tr>
            </tbody>
          </table>
          <Pagination v-if="icssUsers.data" :pagination="icssUsers" :onPageChange="fetchIcssUsers" />
        </div>
      </div>
    </div>
  </div>
  <!-- End Add User -->

  <!-- Settings -->
  <div id="settingUser"
    class="modal overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-100% max-h-full"
    :class="{ 'modal-hide': !modals['settingUser'], 'modal-show': modals['settingUser'] }">
    <div class="relative p-4 w-full max-w-2xl max-h-full modal-content"
      :class="{ 'modal-content-hide': !modals['settingUser'], 'modal-content-show': modals['settingUser'] }">
      <div class="relative  rounded-lg shadow-xl">
        <div class="flex items-center justify-between p-4 border-b-2 border-secondary rounded-t bg-primary">
          <h3 class="text-xl font-extrabold text-light">
            Settings
          </h3>
          <button type="button" @click="hideModal('settingUser')"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-4 h-4 ms-auto inline-flex justify-center items-center">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <div class="p-5 bg-white flex flex-col space-y-5">
          <div class="space-x-2 text-accent">
            <label class="uppercase font-bold text-xs text-main">Name:</label>
            <span class="uppercase text-xs font-bold">{{ selectedUser.name }}</span>
          </div>
          <div class="space-x-2 text-accent">
            <label class="uppercase font-bold text-xs text-main">Username:</label>
            <span class="text-xs font-bold">{{ selectedUser.username }}</span>
          </div>
          <div>
            <label class="uppercase font-bold text-xs text-main">Group</label>
            <div class="w-full lg:w-1/2">
              <select id="countries" v-model="selectedGroupUser"
                class="bg-gray-50 border border-gray-300 text-accent text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 mt-2">
                <option value="">--Choose a Group--</option>
                <option v-for="group in groups" :key="group.id" :value="group.id">{{ group.name }}</option>
              </select>
            </div>
            <small class="text-red-500">{{ formEdit.errors.group_id }}</small>
          </div>
          <div class="flex space-x-2">
            <label class="uppercase font-bold text-xs text-main mt-0.5">Status:</label>
            <label class=" items-center cursor-pointer">
              <input type="checkbox" v-model="selectedUserStatus" class="sr-only peer">
              <div
                class="relative w-11 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
              </div>
            </label>
            <span class="text-xs font-bold uppercase mt-0.5 dark:text-gray-300"
              :class="{ 'text-green-500': selectedUserStatus, 'text-red-500': !selectedUserStatus }">{{
                selectedUserStatus
                  ? 'Active' : 'Inactive' }}</span>
          </div>
        </div>
        <div class="p-5 bg-white border-t-2 rounded-b flex justify-center items-center space-x-5">
          <button class="px-5 py-1 rounded border bg-green-500 text-white font-bold shadow hover:bg-green-600"
            @click="updateUser('settingUser')">Save</button>
          <button class="px-5 py-1 rounded border bg-red-500 text-white font-bold shadow hover:bg-red-600"
            @click="hideModal('settingUser')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Settings -->

  <Warning :divId="'addUserLists'" :onUserCreate="updateUsersList" />
  <Remove :divId="'removeUser'" :onUserRemove="updateUsersList" />
  <!-- END MODALS -->
</template>