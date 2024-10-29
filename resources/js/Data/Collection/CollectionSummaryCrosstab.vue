<script setup>
import { router } from '@inertiajs/vue3';
import { emit } from '../../eventBus';
import { ref, onMounted, computed, onUnmounted } from 'vue';
import LiveUpdates from '../../Partial/LiveUpdatesButton.vue';

const props = defineProps({
  data: Object,
});
const toggleTable = ref(true)
const collectionData = ref({})
const collectionHeaders = ref({})
const dateInput = ref('')
const loading = ref(true)
const loadingButton = ref()
const minimize = ref(false)
const now = ref(new Date());
const account = ref('Bar')

const dateFormat = (date) => {
  const now = date;
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

dateInput.value = dateFormat(now.value);

const report = props.data
const reportLive = ref(Boolean(Number(report.get_latest)))

const collectionChartData = ref({
  labels: [],
  datasets: [{
  }]
});

const collectionChartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  elements: {
    point: {
      radius: 6
    }
  },
  plugins: {
    legend: {
      position: 'top',
    },
  },
});

const getCollection = async () => {
  loading.value = true
  try {
    // console.log(dateInput.value)
    const response = await axios.post('/collection-summary');
    chartData(response)
  } catch (error) {
    console.error('Error fetching customer status:', error);
  } finally {
    loading.value = false
  }
};

const updateDate = async () => {
  // console.log(dateInput.value);
  loading.value = true
  loadingButton.value = true
  try {
    const response = await axios.post('/collection-summary', { date: dateInput.value });
    chartData(response)
    emit(report.id + '-liveButton')
  } catch (error) {
    console.error('Error fetching customer status:', error);
  } finally {
    loading.value = false
  }
}

function chartData(response) {
  const collectionSummaryData = response.data.data;
  collectionData.value = response.data.data
  collectionHeaders.value = response.data.header

  collectionChartData.value = {
    labels: Object.keys(collectionSummaryData),
    datasets: []
  };
  // console.log(collectionChartData.value)
  Object.values(collectionHeaders.value).forEach((value) => {
    let dataset = {
      label: value,
      data: Object.entries(collectionSummaryData).flatMap(([index, data]) => {
        return Object.values(data).map((tagAmount) => {
          return tagAmount.tag == value ? tagAmount.collected_amount : null
        }).filter(amount => amount !== null);
      }),
      backgroundColor: `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 1)`,
      borderColor: "#f8f8ff  ",
    };
    collectionChartData.value.datasets.push(dataset);
  });
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

  window.Echo.channel('collection-summary')
    .listen('CollectionSummaryCrosstab', (data) => {
      if (reportLive.value) {
        chartData(data)
        console.log(report.id + '-collection-summary')
      }
    });
};
// End Live Button

const chartType = (type) => {
  account.value = type
}
const chartView = () => {
  6
  toggleTable.value = !toggleTable.value
}

const minimizeCont = () => {
  minimize.value = !minimize.value
}

// TABLE FUNCTIONs

const formattedCurrency = (value) => {
  return new Intl.NumberFormat('en-US').format(value);
}

const grandTotalAmount = (index) => {
  let grandTotal = 0;
  Object.values(collectionData.value).forEach((value) => {
    Object.values(value).forEach((data) => {
      if (data.tag == index) {
        grandTotal += Number(data.collected_amount)
      }
    })
  })
  return grandTotal
}


onMounted(() => {
  getCollection()
  setupListener();
})
onUnmounted(() => {
  window.Echo.leaveChannel('collection-summary');
});
</script>
<template>

  <div
    class="mt-10 shadow-xl shadow-neutral-400 rounded-xl px-4 pb-4 bg-accent bg-opacity-45 dark:bg-main dark:shadow-neutral-800"
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
        <div class="space-x-4 flex">
          <span class="font-bold text-primary dark:text-white">Date:</span>
          <input type="date" class="text-sm font-bold rounded px-2 text-secondary shadow-xl" v-model="dateInput"
            @change="updateDate">
          <LiveUpdates :data="report" :getInitialData="liveDate" :buttonUpdate="buttonUpdate" />
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
        <div v-else class="w-full max-w-[1865px] p-2">
          <table class="table-auto min-w-full bg-white">
            <thead class="text-left text-xs text-white font-bold border-b">
              <tr class="bg-accent text-center">
                <th class="px-2 py-2"></th>
                <th v-for="header in collectionHeaders" class="px-2 py-2 border-x">{{ header }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(data, index) in collectionData" class="text-accent border-b text-xs text-right">
                <td class="px-2 py-2 border-r font-black text-left">
                  {{ index }}</td>
                <td v-for="value in data" class="border-r px-2 py-2">
                  {{ formattedCurrency(value.collected_amount) }}
                </td>
              </tr>
              <tr class="text-white border-b text-xs text-right bg-accent">
                <td class="border-r px-2 py-2 font-bold">Grand Total</td>
                <td v-for="header in collectionHeaders" class="px-2 py-2 border-x font-black">{{
                  formattedCurrency(grandTotalAmount(header)) }}</td>
              </tr>
              <!-- <tr class="text-white border-b text-[0.60rem] text-right bg-accent">
                <td class="border-r px-2 py-2 font-bold">Grand Total</td>
                <template v-for="header in headers">
                  <td class="px-2 py-2 border-x font-black">{{ totalAmountLocation(header) }}</td>
                  <td class="px-2 py-2 border-x font-black">{{ totalAccountLocation(header) }}</td>
                </template>
<td class="px-2 py-2 border-x font-black">{{ formattedCurrency(grandTotalAmount()) }}</td>
<td class="px-2 py-2 border-x font-black">{{ formattedCurrency(grandTotalAccount()) }}</td>
</tr> -->
            </tbody>
          </table>
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
        <div v-else class="grid grid-cols-1">
          <div class="p-2 ">
            <div class="text-xs space-x-4 mb-4 text-center">
              <button @click="chartType('Bar')" class="text-white font-bold px-2 py-0.5 rounded "
                :class="{ 'bg-secondary shadow-xl': account == 'Bar', 'bg-accent hover:scale-110 transition-transform duration-300': account != 'Bar' }"
                :disabled="account == 'Bar'">Bar</button>
              <button @click="chartType('Line')" class="text-white font-bold px-2 py-0.5 rounded "
                :class="{ 'bg-secondary shadow-xl': account == 'Line', 'bg-accent hover:scale-110 transition-transform duration-300': account != 'Line' }"
                :disabled="account == 'Line'">Line</button>
            </div>
            <div class="max-h-56">
              <component :is="account" :data="collectionChartData" :options="collectionChartOptions" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>