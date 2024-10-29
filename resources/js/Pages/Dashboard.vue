<script setup>
import { ref, onMounted, onBeforeUnmount, computed, onUnmounted } from 'vue';
import Header from '../Partial/UserHeader.vue';
import MeterReading from '../Data/MeterReading/MeterReading.vue';

const props = defineProps({
  customReport: Object,
});

const reports = props.customReport
</script>
<template>
  <div class="shadow-inner py-5">
    <div class="max-w-full mx-10 my-5">
      <Header />
      <div v-for="row in reports">
        <div :class="`grid grid-cols-1 xl:grid-cols-${row.col} space-x-4`">
          <div v-for="report in row.report_cols" :key="report.id" :class="`col-span-${report.span}`">
            <component :is="report.component" :data="report"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>