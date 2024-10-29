<script setup>
import { ref } from 'vue';
import { on, emit } from '../eventBus';
import { router } from '@inertiajs/vue3'

const divId = ref('')
const message = ref('')
const selectedFunction = ref('')
const modals = ref(false);


on('confirmModal', (id, mess, selFunction) => {
  console.log(id, mess, selFunction);
  divId.value = id
  message.value = mess
  selectedFunction.value = selFunction
  showModal();
});

const proceed = () => {
  hideModal()
  emit(selectedFunction.value)
}

const showModal = () => {
  modals.value = true;
  // emit('hideUserLists');
};

const hideModal = () => {
  modals.value = false;
};

</script>
<template>
  <div
    class="modal overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-[100] justify-center items-center w-full md:inset-0 h-100% max-h-full"
    :class="{ 'modal-hide': !modals, 'modal-show': modals }">
    <div class="relative p-4 w-full max-w-lg max-h-full modal-content"
      :class="{ 'modal-content-hide': !modals, 'modal-content-show': modals }">
      <div class="relative  rounded-lg shadow-xl">
        <div class="flex items-center justify-between p-4 border-b-2 border-secondary rounded-t bg-primary">
          <h3 class="text-xl font-extrabold text-white">
            Warning!
          </h3>
          <button type="button" @click="hideModal()"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-4 h-4 ms-auto inline-flex justify-center items-center">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <div class="p-5 bg-white flex flex-row space-x-5 items-center">
          <v-icon name="ri-error-warning-line" scale="3" class="text-yellow-500" animation="pulse" />
          <span class="text-lg text-accent font-semibold">{{ message }}</span>
        </div>
        <div class="p-5 bg-white border-t-2 rounded-b flex justify-center items-center space-x-5">
          <button class="px-5 py-1 rounded border bg-green-500 text-white font-bold shadow hover:bg-green-600"
            @click="proceed()">Proceed</button>
          <button class="px-5 py-1 rounded border bg-neutral-500 text-white font-bold shadow hover:bg-neutral-600"
            @click="hideModal()">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>