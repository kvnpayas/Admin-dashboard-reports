<script setup>
import { router } from '@inertiajs/vue3';
import { ref, onMounted, computed, onUnmounted } from 'vue';
import LiveUpdates from '../../Partial/LiveUpdatesButton.vue';
import { emit } from '../../eventBus';

const props = defineProps({
  data: Object,
});
const report = props.data
const reportLive = ref(Boolean(Number(report.get_latest)))
const toggleTable = ref(true)
const collectionData = ref({})
const dateInput = ref('')
const loading = ref(true)
const minimize = ref(false)
const now = new Date();
const year = now.getFullYear();
const month = String(now.getMonth() + 1).padStart(2, '0');
const day = String(now.getDate()).padStart(2, '0');
dateInput.value = `${year}-${month}-${day}`;

const getCollection = async () => {
  loading.value = true
  try {
    // console.log(dateInput.value)
    const response = await axios.post('/collection-summary-payment', { date: dateInput.value });
    collectionData.value = response.data.data
  } catch (error) {
    console.error('Error fetching customer status:', error);
  } finally {
    loading.value = false
  }
};

const minimizeCont = () => {
  minimize.value = !minimize.value
}

const updateDate = async () => {
  loading.value = true
  try {
    const response = await axios.post('/collection-summary-payment', { date: dateInput.value });
    collectionData.value = response.data.data
    emit(report.id + '-liveButton')
  } catch (error) {
    console.error('Error fetching customer status:', error);
  } finally {
    loading.value = false
  }
}

const formattedCurrency = (value) => {
  return new Intl.NumberFormat('en-US').format(value);
}

// Live Button
const liveDate = (newDate) => {
  dateInput.value = newDate
  getCollection()
}
const buttonUpdate = (value) => {
  reportLive.value = value
}

const setupListener = () => {

  window.Echo.channel('collection-payment-center')
    .listen('CollectionPaymentCenterEvent', (data) => {
      if (reportLive.value) {
        collectionData.value = data.data.data
        console.log(report.id + '-collection-payment')
      }
    });
};
// End Live Button

onMounted(() => {
  getCollection()
  setupListener()

})
onUnmounted(() => {
  window.Echo.leaveChannel('collection-payment-center');
});
</script>
<template>
  <div class="mt-10 shadow-xl shadow-neutral-400 rounded-xl px-4 pb-4 bg-accent bg-opacity-45 dark:bg-main dark:shadow-neutral-800"
    :class="{ 'open-container': !minimize, 'min-container': minimize }"
    style="transition: max-height 0.7s ease-in-out;">
    <div class="text-end">
      <button @click="minimizeCont()" class="hover:-translate-y-1 hover:scale-110 transition-transform duration-300 ">
        <v-icon name="fa-window-minimize" class="text-accent" scale="1.5"></v-icon>
      </button>
    </div>
    <div class="text-center">
      <h3 class="text-xl uppercase font-black text-white">{{ report.report.name }}</h3>
    </div>
    <div class="bg-light rounded mt-5 dark:bg-main"
      :class="{ 'animation-transition': true, 'min-h-32': !toggleTable, 'min-h-0': toggleTable }">
      <div class="flex justify-between space-x-4 p-4">
        <div class="flex space-x-4">
          <span class="font-bold text-primary dark:text-white">Date:</span>
          <input type="date" class="text-sm font-bold rounded px-2 text-secondary shadow-xl" v-model="dateInput"
            @change="updateDate">
            <LiveUpdates :data="report" :getInitialData="liveDate" :buttonUpdate="buttonUpdate"/>
        </div>
        <div class="flex space-x-2">
          <span class="text-xs font-bold mt-1 text-main dark:text-white">View:</span>
          <div class="">

            <button @click="chartView()" :disabled="!toggleTable"
              :class="{ 'text-secondary shadow-inner shadow-accent p-0.5 rounded': !toggleTable, 'dark:text-white text-accent hover:scale-110 transition-transform duration-300': toggleTable }">
              <v-icon name="fa-regular-chart-bar" scale="1"></v-icon>
            </button>
            <button @click="chartView()" :disabled="toggleTable"
              :class="{ 'text-secondary shadow-inner shadow-accent p-0.5 rounded': toggleTable, 'dark:text-white text-accent hover:scale-110 transition-transform duration-300 shadow-xl': !toggleTable }">
              <v-icon name="bi-table" scale="1"></v-icon>
            </button>
          </div>
        </div>
      </div>
      <div v-if="toggleTable" class="overflow-x-auto w-full">
        <div v-if="loading">
          <div role="status"
            class="w-full p-4 space-y-4 border border-accent divide-y divide-accent rounded shadow animate-pulse dark:divide-gray-700 md:p-6 dark:border-gray-700">
            <div class="flex items-center justify-between">
              <div>
                <div class="h-2.5 bg-accent rounded-full dark:bg-gray-600 w-24 mb-2.5"></div>
                <div class="w-32 h-2 bg-neutral-100 rounded-full dark:bg-gray-700"></div>
              </div>
              <div>
                <div class="h-2.5 bg-accent rounded-full dark:bg-gray-600 w-24 mb-2.5"></div>
                <div class="w-32 h-2 bg-neutral-100 rounded-full dark:bg-gray-700"></div>
              </div>
              <div>
                <div class="h-2.5 bg-accent rounded-full dark:bg-gray-600 w-24 mb-2.5"></div>
                <div class="w-32 h-2 bg-neutral-100 rounded-full dark:bg-gray-700"></div>
              </div>
              <div>
                <div class="h-2.5 bg-accent rounded-full dark:bg-gray-600 w-24 mb-2.5"></div>
                <div class="w-32 h-2 bg-neutral-100 rounded-full dark:bg-gray-700"></div>
              </div>
            </div>
            <div class="flex items-center justify-between pt-4">
              <div>
                <div class="h-2.5 bg-accent rounded-full dark:bg-gray-600 w-24 mb-2.5"></div>
                <div class="w-32 h-2 bg-neutral-100 rounded-full dark:bg-gray-700"></div>
              </div>
              <div>
                <div class="h-2.5 bg-accent rounded-full dark:bg-gray-600 w-24 mb-2.5"></div>
                <div class="w-32 h-2 bg-neutral-100 rounded-full dark:bg-gray-700"></div>
              </div>
              <div>
                <div class="h-2.5 bg-accent rounded-full dark:bg-gray-600 w-24 mb-2.5"></div>
                <div class="w-32 h-2 bg-neutral-100 rounded-full dark:bg-gray-700"></div>
              </div>
              <div>
                <div class="h-2.5 bg-accent rounded-full dark:bg-gray-600 w-24 mb-2.5"></div>
                <div class="w-32 h-2 bg-neutral-100 rounded-full dark:bg-gray-700"></div>
              </div>
            </div>
            <span class="sr-only">Loading...</span>
          </div>
        </div>
        <div v-else class="shadow w-full max-w-[1865px]">
          <div class="p-4">
            <div class="grid grid-cols-3 gap-4">
              <div v-for="data in collectionData"
                class="bg-accent text-white rounded text-center flex flex-col p-2 transition-all duration-300 hover:bg-green-600 cursor-default">
                <label class="text-sm font-bold">{{ data.tag }}</label>
                <span class="text-sm font-black">
                  {{ formattedCurrency(data.collected_amount) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="grid grid-cols-1">
        <div v-if="loading" class="flex justify-around ">
          <div role="status"
            class="max-w-sm p-4 border border-accent rounded shadow animate-pulse md:p-6 dark:border-gray-700">
            <div class="h-2.5 bg-accent rounded-full dark:bg-gray-700 w-32 mb-2.5"></div>
            <div class="w-48 h-2 mb-10 bg-gray-200 rounded-full dark:bg-gray-700"></div>
            <div class="flex items-baseline mt-4">
              <div class="w-full bg-gray-200 rounded-t-lg h-48 dark:bg-gray-700"></div>
              <div class="w-full h-40 ms-6 bg-accent rounded-t-lg dark:bg-gray-700"></div>
              <div class="w-full bg-gray-200 rounded-t-lg h-48 ms-6 dark:bg-gray-700"></div>
              <div class="w-full h-32 ms-6 bg-accent rounded-t-lg dark:bg-gray-700"></div>
              <div class="w-full bg-gray-200 rounded-t-lg h-52 ms-6 dark:bg-gray-700"></div>
              <div class="w-full bg-accent rounded-t-lg h-48 ms-6 dark:bg-gray-700"></div>
              <div class="w-full bg-gray-200 rounded-t-lg h-52 ms-6 dark:bg-gray-700"></div>
            </div>
            <span class="sr-only">Loading...</span>
          </div>
          <div role="status"
            class="max-w-sm p-4 border border-accent rounded shadow animate-pulse md:p-6 dark:border-gray-700">
            <div class="h-2.5 bg-accent rounded-full dark:bg-gray-700 w-32 mb-2.5"></div>
            <div class="w-48 h-2 mb-10 bg-gray-200 rounded-full dark:bg-gray-700"></div>
            <div class="flex items-baseline mt-4">
              <div class="w-full bg-accent rounded-t-lg h-48 dark:bg-gray-700"></div>
              <div class="w-full h-40 ms-6 bg-gray-200 rounded-t-lg dark:bg-gray-700"></div>
              <div class="w-full bg-accent rounded-t-lg h-48 ms-6 dark:bg-gray-700"></div>
              <div class="w-full h-32 ms-6 bg-gray-200 rounded-t-lg dark:bg-gray-700"></div>
              <div class="w-full bg-accent rounded-t-lg h-52 ms-6 dark:bg-gray-700"></div>
              <div class="w-full bg-gray-200 rounded-t-lg h-48 ms-6 dark:bg-gray-700"></div>
              <div class="w-full bg-accent rounded-t-lg h-52 ms-6 dark:bg-gray-700"></div>
            </div>
            <span class="sr-only">Loading...</span>
          </div>

        </div>
        <div v-else class="p-2">
          <!-- <div class="text-xs space-x-4 mb-2 text-center">
            <button @click="chartType('Bar')" class="text-white font-bold px-2 py-0.5 rounded "
              :class="{ 'bg-secondary shadow-xl': account == 'Bar', 'bg-accent hover:scale-110 transition-transform duration-300': account != 'Bar' }"
              :disabled="account == 'Bar'">Bar</button>
            <button @click="chartType('Line')" class="text-white font-bold px-2 py-0.5 rounded "
              :class="{ 'bg-secondary shadow-xl': account == 'Line', 'bg-accent hover:scale-110 transition-transform duration-300': account != 'Line' }"
              :disabled="account == 'Line'">Line</button>
          </div>
          <div class="max-h-56">
            <component :is="account" :data="accountData" :options="accountOptions" />
          </div> -->
        </div>
      </div>
    </div>
  </div>
</template>