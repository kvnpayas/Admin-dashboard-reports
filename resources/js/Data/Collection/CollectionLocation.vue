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
const toggleTable = ref(false)
const collectionData = ref({})
const headers = ref({})
const account = ref('Bar')
const fromDate = ref('')
const toDate = ref('')
const loading = ref(true)
const minimize = ref(false)

const getCollection = async () => {
  loading.value = true
  try {
    // console.log(dateInput.value)
    const response = await axios.post('/collection-location', { dateFrom: fromDate.value, dateTo: toDate.value });
    chartData(response)
    // collectionData.value = response.data.data
  } catch (error) {
    console.error('Error fetching customer status:', error);
  } finally {
    loading.value = false
  }
};

// Live Button
const liveDate = (newDate) => {
  const date = new Date(newDate);
  const previousDay = new Date(date);
  previousDay.setDate(date.getDate() - 1);

  const formatDate = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  };

  fromDate.value = formatDate(previousDay);
  toDate.value = formatDate(date)
  getCollection()
}

const buttonUpdate = (value) => {
  reportLive.value = value
}

const setupListener = () => {

  window.Echo.channel('collection-update')
    .listen('CollectionUpdate', (data) => {
      if (reportLive.value) {
        chartData(data)
        console.log(report.id + '-collection-location')
      }
    });
};
// End Live Button

const getData = async () => {
  // console.log(dateInput.value);
  loading.value = true
  try {
    const response = await axios.post('/collection-location', { dateFrom: fromDate.value, dateTo: toDate.value });
    chartData(response)
    console.log(response.data)
    emit(report.id + '-liveButton')
    collectionData.value = response.data.data

  } catch (error) {
    console.error('Error fetching customer status:', error);
  } finally {
    loading.value = false
  }
}
// CHART FUNCTIONS
const accountData = ref({
  labels: [],
  datasets: [{
  }]
});

const accountOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  elements: {
    point: {
      radius: 4
    }
  },
  plugins: {
    legend: {
      position: 'top'
    }
  },
});

function chartData(response) {
  console.log(response.data)
  const collections = response.data.data;
  collectionData.value = response.data.data
  headers.value = response.data.headers
  const bgColor = {
    'BDO': '#162B73',
    'EASYPAY': '#77B113',
    'METROBANK': '#5086B6',
    'CITYWALK': '#9D3735',
    'MAGIC': '#F8D000',
    'MAIN': '#E76727',
    'MATMAGIC': '#57B400',
    'METRO': '#7F76F3',
    'MARKTCTY': '#37446B',
    'SM': '#2020EA',
  };
  // Accounts
  accountData.value = {
    labels: Object.keys(collections),
    datasets: []
  };

  Object.values(collections).forEach((dateData, index) => {
    Object.entries(dateData).forEach(([center, data]) => {
      let dataset = accountData.value.datasets.find(ds => {
        return ds.label.replace('TARLAC ELECTRIC ', '') === center.replace('TARLAC ELECTRIC ', '')
      });
      if (!dataset) {
        dataset = {
          label: center.replace('TARLAC ELECTRIC ', ''),
          data: [],
          backgroundColor: bgColor[center.replace('TARLAC ELECTRIC ', '')],
          // backgroundColor: `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 1)`,
          // borderColor: `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 1)`,
          borderColor: bgColor[center.replace('TARLAC ELECTRIC ', '')],
          borderWidth: 2
        };
        accountData.value.datasets.push(dataset);
      }
      dataset.data.push(data.amount);
    });
  });

}

const chartType = (type) => {
  account.value = type
}
const chartView = () => {
  toggleTable.value = !toggleTable.value
}

const dateFormat = (date) => {
  const now = date;
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

const minimizeCont = () => {
  minimize.value = !minimize.value
}

// TABLE FUNCTIONs
const strHeader = (header) => {
  return header.replace('PAYMENT CENTER - ', '')
}

const formattedCurrency = (value) => {
  return new Intl.NumberFormat('en-US').format(value);
}

const dateTotalAmount = (date) => {
  let result = 0;
  Object.values(collectionData.value[date]).forEach(e => {
    result += e.amount;
  })
  return result;
}

const dateTotalAccount = (date) => {
  let result = 0;
  Object.values(collectionData.value[date]).forEach(e => {
    result += e.account_count;
  })
  return result;
}

const totalAmountLocation = (location) => {
  const header = location.replace('PAYMENT CENTER - ', 'TARLAC ELECTRIC ')
  let total = 0;
  Object.values(collectionData.value).forEach(e => {
    Object.entries(e).forEach(([key, value]) => {
      if (key == header) {
        total += value.amount;
      }
    })
  })
  return new Intl.NumberFormat('en-US').format(total);
}

const totalAccountLocation = (location) => {
  const header = location.replace('PAYMENT CENTER - ', 'TARLAC ELECTRIC ')
  let total = 0;
  Object.values(collectionData.value).forEach(e => {
    Object.entries(e).forEach(([key, value]) => {
      if (key == header) {
        total += value.account_count;
      }
    })
  })
  return total;
}

const grandTotalAmount = () => {
  let grandTotal = 0;
  Object.entries(collectionData.value).forEach(([key, value]) => {
    grandTotal += dateTotalAmount(key)
  })
  return grandTotal
}

const grandTotalAccount = () => {
  let grandTotal = 0;
  Object.entries(collectionData.value).forEach(([key, value]) => {
    grandTotal += dateTotalAccount(key)
  })
  return grandTotal
}

onMounted(() => {
  toDate.value = dateFormat(new Date())
  fromDate.value = dateFormat(new Date(new Date().setDate(new Date().getDate() - 1)))
  getCollection()
  // const currentdate = new Date();
  setupListener()
})
onUnmounted(() => {
  window.Echo.leaveChannel('collection-update');
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
      <div class="flex p-4">
        <div class="space-x-4 mr-4">
          <span class="font-bold text-primary dark:text-white">From:</span>
          <input type="date" class="text-sm font-bold rounded px-2 text-secondary shadow-xl" v-model="fromDate">
          <span class="font-bold text-primary dark:text-white">To:</span>
          <input type="date" class="text-sm font-bold rounded px-2 text-secondary shadow-xl" v-model="toDate">
          <button @click="getData()" class="px-2 py-1 rounded text-xs font-bold shadow-xl "
            :class="{ 'bg-green-500 hover:scale-110 transition-transform duration-300 text-white': !loading, 'bg-lgiht text-accent': loading }"
            :disabled="loading">Retrieve</button>
        </div>
        <LiveUpdates :data="report" :getInitialData="liveDate" :buttonUpdate="buttonUpdate"/>
        <div class="flex space-x-2 ml-auto">
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
                <th class="px-2 py-2">PAYMENT CENTER</th>
                <th v-for="header in headers" class="px-2 py-2 border-x" colspan="2">{{ strHeader(header) }}</th>
                <th class="px-2 py-2">Total Amount</th>
                <th class="px-2 py-2">Total Account</th>
              </tr>
              <tr class="text-accent">
                <th class="px-2 py-2">Collection Date</th>
                <template v-for="item in 10">
                  <th class="px-2 py-2 border-l">Amount</th>
                  <th class="px-2 py-2 border-r">Account</th>
                </template>
                <th class="px-2 py-2 border-r"></th>
                <th class="px-2 py-2"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(data, index) in collectionData" class="text-accent border-b text-[0.60rem] text-right">
                <td class="px-2 py-2 border-r font-bold text-left">
                  {{ index }}</td>
                <template v-for="(childData, childIndex) in data" :key="`child-${childIndex}`">
                  <td v-for="data in childData" class="border-r px-2 py-2">
                    {{ formattedCurrency(data) }}
                  </td>
                </template>
                <td class="border-r px-2 py-2 font-black">{{ formattedCurrency(dateTotalAmount(index)) }}</td>
                <td class="border-r px-2 py-2 font-black">{{ formattedCurrency(dateTotalAccount(index)) }}</td>
              </tr>
              <tr class="text-white border-b text-[0.60rem] text-right bg-accent">
                <td class="border-r px-2 py-2 font-bold">Grand Total</td>
                <template v-for="header in headers">
                  <td class="px-2 py-2 border-x font-black">{{ totalAmountLocation(header) }}</td>
                  <td class="px-2 py-2 border-x font-black">{{ totalAccountLocation(header) }}</td>
                </template>
                <td class="px-2 py-2 border-x font-black">{{ formattedCurrency(grandTotalAmount()) }}</td>
                <td class="px-2 py-2 border-x font-black">{{ formattedCurrency(grandTotalAccount()) }}</td>
              </tr>
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
        <div v-else class="p-2">
          <div class="text-xs space-x-4 mb-2 text-center">
            <button @click="chartType('Bar')" class="text-white font-bold px-2 py-0.5 rounded "
              :class="{ 'bg-secondary shadow-xl': account == 'Bar', 'bg-accent hover:scale-110 transition-transform duration-300': account != 'Bar' }"
              :disabled="account == 'Bar'">Bar</button>
            <button @click="chartType('Line')" class="text-white font-bold px-2 py-0.5 rounded "
              :class="{ 'bg-secondary shadow-xl': account == 'Line', 'bg-accent hover:scale-110 transition-transform duration-300': account != 'Line' }"
              :disabled="account == 'Line'">Line</button>
          </div>
          <div class="max-h-56">
            <component :is="account" :data="accountData" :options="accountOptions" />
          </div>
        </div>
        <!-- <div class="p-2">
        <div class="text-xs space-x-4 mb-2 text-center">
          <button @click="chartTypeParam('Doughnut')" class="text-white font-bold px-2 py-0.5 rounded "
            :class="{ 'bg-secondary shadow-xl': accountParam == 'Doughnut', 'bg-accent hover:scale-110 transition-transform duration-300': accountParam != 'Doughnut' }"
            :disabled="accountParam == 'Doughnut'">Pie</button>
          <button @click="chartTypeParam('Bar')" class="text-white font-bold px-2 py-0.5 rounded "
            :class="{ 'bg-secondary shadow-xl': accountParam == 'Bar', 'bg-accent hover:scale-110 transition-transform duration-300': accountParam != 'Bar' }"
            :disabled="accountParam == 'Bar'">Bar</button>
          <button @click="chartTypeParam('Line')" class="text-white font-bold px-2 py-0.5 rounded "
            :class="{ 'bg-secondary shadow-xl': accountParam == 'Line', 'bg-accent hover:scale-110 transition-transform duration-300': accountParam != 'Line' }"
            :disabled="accountParam == 'Line'">Line</button>
        </div>
        <div class="max-h-32">
          <component :is="accountParam" :data="accountDataParam" :options="accountOptionsParam" />
        </div>
      </div> -->
      </div>
    </div>
  </div>
</template>