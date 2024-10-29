<script setup>
import { ref } from 'vue';
import { on, emit } from '../../eventBus';
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  divId: String,
  onUserCreate: Function,
});

const page = usePage()

const modals = ref({});
const divId = ref({});
const addUser = ref({});
// const users = ref({});

divId.value = props.divId;

on('warningModal', (user) => {
  addUser.value = user;
  showModal(divId.value);
});

const showModal = (id) => {
  modals.value[id] = true;
  emit('hideUserLists');
};

const hideModal = (id) => {
  modals.value[id] = false;
};

const createUsers = (id) => {
  // /user-maintenance/decrypt-password/{user_id}'
  axios.post('/user-maintenance/decrypt-password/' + addUser.value.user_id).then(response => {
    props.onUserCreate(response.data.user, 'create')
    emit('successMessage', response.data.success)
    hideModal(id);
  })
    .catch(error => {
      console.error(error);
    });
  // hideModal(id);
};
</script>
<template>
  <div :id="divId"
    class="modal overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-100% max-h-full"
    :class="{ 'modal-hide': !modals[divId], 'modal-show': modals[divId] }">
    <div class="relative p-4 w-full max-w-xl max-h-full modal-content"
      :class="{ 'modal-content-hide': !modals[divId], 'modal-content-show': modals[divId] }">
      <div class="relative  rounded-lg shadow-xl">
        <div class="flex items-center justify-between p-4 border-b-2 border-secondary rounded-t bg-primary">
          <h3 class="text-xl font-extrabold text-light">
            Warning!
          </h3>
          <button type="button" @click="hideModal(divId)"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-4 h-4 ms-auto inline-flex justify-center items-center">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <div class="p-5 bg-white flex flex-col space-y-5 items-center">
          <v-icon name="ri-error-warning-line" scale="4" class="text-yellow-500" animation="pulse" />
          <span class="text-lg text-accent font-semibold">Are you sure you want to add <label
              class="text-secondary font-bold underline">{{ addUser.user_description }}</label> and grant access to tei
            dashboard?</span>
        </div>
        <div class="p-5 bg-white border-t-2 rounded-b flex justify-center items-center space-x-5">
          <button class="px-5 py-1 rounded border bg-green-500 text-white font-bold shadow hover:bg-green-600"
            @click="createUsers(divId)">Add</button>
          <button class="px-5 py-1 rounded border bg-red-500 text-white font-bold shadow hover:bg-red-600"
            @click="hideModal(divId)">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>