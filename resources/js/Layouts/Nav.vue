<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { emit, on } from '../eventBus';

const props = defineProps({
  hideSidebar: Boolean,
});
const triggerButton = ref(props.hideSidebar)

const hideShowSidebar = () => {
  triggerButton.value = !triggerButton.value
  emit('hide-show-sidebar', triggerButton.value)
}

// Toggle Fullscreen
const toggleFullScreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen().catch(err => {
      alert(`Error attempting to enable full-screen mode: ${err.message} (${err.name})`);
    });
  } else {
    document.exitFullscreen();
  }
};

const handleKeyDown = (event) => {
  if (event.key === 'F11') {
    event.preventDefault();
    toggleFullScreen();
  }
};

onMounted(() => {
  document.addEventListener('keydown', handleKeyDown);
});

onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleKeyDown);
});
// End Toggle Fullscreen

</script>
<template>

  <nav class="bg-light shadow-sm shadow-accent min-h-9 p-[0.53rem] text-accent border-b-2 border-light">
    <div class="flex justify-between mx-auto py-1.5 px-4">
      <button @click="hideShowSidebar">
        <v-icon name="fa-bars" animation="pulse" hover></v-icon>
      </button>
      <div class="space-x-6 text-white">
        <!-- <Link href="/">Home</Link>
        <Link href="/sms-setting">SMS Settings</Link>
        <Link href="/setting">Settings</Link> -->
        <!-- <a href="/">Home</a>
        <a href="/setup">Setup</a> -->

        <!-- Fullscreen Button -->
        <div>
          <button @click="toggleFullScreen" class="text-main"><v-icon name="bi-arrows-fullscreen" animation="pulse"
              hover></v-icon></button>
        </div>
        <!-- End Fullscreen Button -->
      </div>
    </div>
  </nav>
</template>