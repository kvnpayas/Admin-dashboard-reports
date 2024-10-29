<script setup>
import { ref } from 'vue';
import { emit, on } from '../eventBus';
import Nav from '../Layouts/Nav.vue';
import Sidebar from '../Layouts/Sidebar.vue';
import FlashSuccess from '../Partial/FlashSuccess.vue';
import FlashFail from '../Partial/FlashFail.vue';
import TriggerButton from '../Partial/TriggerFlashMessage.vue';

const hideSidebar = ref(false)

on('hide-show-sidebar', (value) => {
  hideSidebar.value = value
})
</script>
<template>
  <div class="flex">
    <FlashSuccess />
    <FlashFail />
    <TriggerButton />
    <div class="shadow-[0_35px_60px_-15px_rgba(0,0,0,0.7)] overflow-y-auto fixed transition-all delay-300 duration-300 ease-in-out" :class="{'w-64': !hideSidebar, 'w-0': hideSidebar}">
      <Sidebar />
    </div>
    <div class="w-full transition-all delay-300 duration-300" :class="{'ml-64': !hideSidebar, 'ml-0': hideSidebar}">
      <header>
        <Nav :hideSidbar="hideSidebar"/>
      </header>
      <main >
        <!-- <Process /> -->
        <slot />
      </main>
    </div>
  </div>
</template>