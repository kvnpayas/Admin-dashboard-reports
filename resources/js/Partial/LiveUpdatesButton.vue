<script setup>
import { ref, watch } from 'vue';
import { emit, on } from '../eventBus';

const props = defineProps({
  data: Object,
  getInitialData: Function,
  buttonUpdate: Function,
});

const report = props.data

const reportLive = ref(Boolean(Number(report.get_latest)))

const loadingButton = ref()
const dateInput = ref()
const now = ref(new Date());

const dateFormat = (date) => {
  const now = date;
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

const updateLive = () => {
  updateGetLatest()
  if (reportLive.value) {
    dateInput.value = dateFormat(now.value);
    props.getInitialData(dateInput.value)
  }
  props.buttonUpdate(reportLive.value)
}

const updateGetLatest = async () => {
  loadingButton.value = true
  try {
    const response = await axios.post('/get-latest/' + report.id, { getLatest: reportLive.value });
    reportLive.value = response.data.data
    emit('triggerButton', report.name, reportLive.value)
  } catch (error) {
    console.error('Error fetching customer status:', error);
  } finally {
    loadingButton.value = false
  }
}

on(report.id + '-liveButton', () => {
  reportLive.value = false;
  props.buttonUpdate(reportLive.value)
  updateGetLatest()
});

</script>

<template>
  <div class="flex justify center space-x-2">
    <label class="switch">
      <input type="checkbox" v-model="reportLive" @change="updateLive" :disabled="loadingButton" />
      <span class="slider"></span>
    </label>
    <span class="text-xs text-accent uppercase font-black mt-1 dark:text-white"> Live updates</span>
  </div>
</template>