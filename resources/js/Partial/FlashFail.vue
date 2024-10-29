<script setup>
import { on, emit } from '../eventBus';
import { router, usePage } from '@inertiajs/vue3'

const page = usePage()

on('failMessage', (message) => {
  page.props.flash.fail = message
  setTimeout(() => {
    page.props.flash.fail = null
  }, 3000)
})

const closeButton = () => {
  page.props.flash.fail = null
}

</script>

<template>
  <transition name="fade">
    <div v-if="$page.props.flash.fail"
      class="z-50 fixed top-10 left-1/2 transform -translate-x-1/2  bg-white text-black p-4 rounded shadow-xl w-1/6 max-w-sm border-t-4 border-red-500">
      <div class="flex justify-end">
        <button type="button" @click="closeButton()"
          class="text-red-400 bg-transparent hover:text-red-700 rounded-lg text-sm w-4 h-4 ms-auto inline-flex justify-center items-center">
          <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
          </svg>
          <span class="sr-only">Close modal</span>
        </button>
      </div>
      <div class="flex gap-4">
        <div class="flex items-center">
          <v-icon name="bi-x-circle-fill" class="text-red-500 mr-2" scale="2"></v-icon>
        </div>
        <div class="flex items-center">
          <span class="text-xs text-accent font-semibold"> {{ $page.props.flash.fail }}</span>
        </div>
      </div>
    </div>
  </transition>
</template>