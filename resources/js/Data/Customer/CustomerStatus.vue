<script setup>
import { ref, onMounted, watch, computed, onUnmounted } from 'vue';
import { emit } from '../../eventBus';
import LiveUpdates from '../../Partial/LiveUpdatesButton.vue';

const props = defineProps({
  data: Object,
});
const dateInput = ref('');
const fetchData = ref({});
const account = ref('Doughnut')
const accountParam = ref('Line')
const toggleTable = ref(true)
const minimize = ref(false)
const loadingButton = ref()
const now = ref(new Date());
const report = props.data
const reportLive = ref(Boolean(Number(report.get_latest)))

const tableHeader1 = ref([
  'Active Accounts',
  'Connected',
  'Disconnected',
])
const tableHeader2 = ref([
  'Deactivated MTD',
  'New Installed Account',
  'Disconnected Accounts',
  'Prequal Accounts',
  'Served Discon Notice',
])

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
      radius: 6
    }
  },
  plugins: {
    legend: {
      position: 'left'
    }
  },
  scales: {
    x: {
      ticks: {
        font: {
          size: 8
        }
      }
    },
  }
});

const accountDataParam = ref({
  labels: [],
  datasets: [{
  }]
});

const accountOptionsParam = ref({
  responsive: true,
  maintainAspectRatio: false,
  elements: {
    point: {
      radius: 6
    }
  },
  plugins: {
    legend: {
      labels: {
        font: {
          size: 8
        }
      },
      position: 'left',

    },
  },
  scales: {
    x: {
      ticks: {
        font: {
          size: 8
        }
      }
    },
  }
});

const dateFormat = (date) => {
  const now = date;
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

dateInput.value = dateFormat(now.value);

const getCustomerStatus = async () => {
  try {
    const response = await axios.post('/customer-status', { date: dateInput.value });
    chartData(response)


  } catch (error) {
    console.error('Error fetching customer status:', error);
  }
};

function chartData(response) {
  const customerStatusAccount = response.data.accounts;
  const customerStatusWithDate = response.data.accountsParam;
  fetchData.value = response.data
  // Accounts
  accountData.value = {
    labels: customerStatusAccount.map(function (data) {
      return data.tag;
    }),
    datasets: [{
      label: '',
      backgroundColor: ['#008000', '#32cd32', '#FFCE56'],
      data: customerStatusAccount.map(function (data) {
        return data.account_count;
      }),
      hoverOffset: 20,
      borderColor: "#f8f8ff  ",
    }],
  };

  // WithParam
  accountDataParam.value = {
    labels: customerStatusWithDate.map(function (data) {
      return data.tag;
    }),
    datasets: [{
      label: '',
      backgroundColor: ['#008000', '#32cd32', '#FFCE56', '#b22222', '#c0c0c0'],
      // backgroundColor: ['#008000', '#32cd32', '#FFCE56', '#b22222', '#0000ff', '#ffa500', '#800000', '#c0c0c0'],
      data: customerStatusWithDate.map(function (data) {
        return data.account_count;
      }),
      hoverOffset: 20,
      borderColor: "#f8f8ff  ",
    }],
  };
}

// Live Button
const liveDate = (newDate) => {
  dateInput.value = newDate
  getCustomerStatus()
}

const buttonUpdate = (value) => {
  reportLive.value = value
}

const setupListener = () => {

  window.Echo.channel('customer-status')
    .listen('CustomerStatusUpdated', (data) => {
      if (reportLive.value) {
        chartData(data);
        console.log(report.id + '-customer-status');
      }
    });
};
// End Live Button

const minimizeCont = () => {
  minimize.value = !minimize.value
}

const chartType = (type) => {
  account.value = type
}
const chartTypeParam = (type) => {
  accountParam.value = type
}
const chartView = () => {
  toggleTable.value = !toggleTable.value
}

const updateDate = async () => {
  loadingButton.value = true
  const response = await axios.post('/customer-status', { date: dateInput.value });
  emit(report.id + '-liveButton')
  chartData(response)
}

const formattedCurrency = (value) => {
  return new Intl.NumberFormat('en-US').format(value);
};

onMounted(() => {
  getCustomerStatus()
  setupListener();
});


onUnmounted(() => {
  window.Echo.leaveChannel('customer-status');
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
    <div class=" text-center">
      <h3 class="text-xl uppercase font-black text-white">{{ report.report.name }}</h3>
    </div>
    <!-- :class="{ 'animation-transition': true, 'min-h-96': !toggleTable, 'min-h-0': toggleTable }" -->
    <div class="bg-light rounded mt-5 dark:bg-main"
      :class="{ 'animation-transition': true, 'min-h-32': !toggleTable, 'min-h-0': toggleTable }">
      <div class="flex justify-between space-x-4 p-4">
        <div class="flex justify-between space-x-4 ">
          <span class="font-bold text-primary dark:text-white">Date:</span>
          <input type="date" class="text-sm font-bold rounded px-2 text-secondary shadow-xl" v-model="dateInput"
            @change="updateDate">
          <LiveUpdates :data="report" :getInitialData="liveDate" :buttonUpdate="buttonUpdate" />
        </div>
        <div class="flex space-x-2">
          <!-- <label class="inline-flex items-center cursor-pointer">
          <input type="checkbox" value="" class="sr-only peer" v-model="toggleTable" @change="toggle">
          <div
            class="relative w-11 h-6 bg-accent peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-secondary">
          </div>
          <span class="ms-3 text-sm font-medium text-accent dark:text-gray-300">Toggle for table view</span>
        </label> -->
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
      <div v-if="toggleTable">
        <div class="shadow p-4">
          <div class="grid grid-cols-4 gap-4">
            <div v-for="(accountCount, index) in fetchData.accounts"
              class="bg-accent text-white rounded text-center flex flex-col p-2 transition-all duration-300 hover:bg-green-600 cursor-default">
              <label class="text-xs font-bold">{{ tableHeader1[index] }}</label>
              <span class="text-2xl font-black">{{ formattedCurrency(accountCount.account_count) }}</span>
            </div>
            <div v-for="(accountCount, index) in fetchData.accountsParam"
              class="bg-accent text-white rounded text-center flex flex-col p-2 transition-all duration-300 hover:bg-green-600 cursor-default">
              <label class="text-xs font-bold">{{ tableHeader2[index] }}</label>
              <span class="text-2xl font-black">{{ formattedCurrency(accountCount.account_count) }}</span>
            </div>
          </div>

          <!-- <table class="table-auto w-full bg-white">
            <thead class="text-left text-xs bg-primary text-white font-bold border-b">
              <tr>
                <th class="px-2 py-2">Active Accounts</th>
                <th class="px-2 py-2">Connected</th>
                <th class="px-2 py-2">Disconnected</th>
                <th class="px-2 py-2">Deactivated MTD</th>
                <th class="px-2 py-2">Newly Installed Accounts</th>
                <th class="px-2 py-2">Disconnected Accounts</th>
                <th class="px-2 py-2">Prequal Accounts </th>
                <th class="px-2 py-2">Served Discon Notice</th>
              </tr>
            </thead>
            <tbody>
              <tr class="text-accent border-b text-sm">
                <td v-for="accountCount in fetchData.accounts" :key="accountCount.tag" class="px-2 py-2">{{
                  formattedCurrency(accountCount.account_count) }}</td>
                <td v-for="accountCount in fetchData.accountsParam" :key="accountCount.tag" class="px-2 py-2">{{
                  formattedCurrency(accountCount.account_count) }}</td>
              </tr>
            </tbody>
          </table> -->
        </div>
      </div>
      <div v-else class="grid grid-cols-2 gap-4">
        <div class="p-2">
          <div class="text-xs space-x-4 mb-2 text-center">
            <button @click="chartType('Doughnut')" class="text-white font-bold px-2 py-0.5 rounded "
              :class="{ 'bg-secondary shadow-xl': account == 'Doughnut', 'bg-accent hover:scale-110 transition-transform duration-300': account != 'Doughnut' }"
              :disabled="account == 'Doughnut'">Pie</button>
            <button @click="chartType('Bar')" class="text-white font-bold px-2 py-0.5 rounded "
              :class="{ 'bg-secondary shadow-xl': account == 'Bar', 'bg-accent hover:scale-110 transition-transform duration-300': account != 'Bar' }"
              :disabled="account == 'Bar'">Bar</button>
            <button @click="chartType('Line')" class="text-white font-bold px-2 py-0.5 rounded "
              :class="{ 'bg-secondary shadow-xl': account == 'Line', 'bg-accent hover:scale-110 transition-transform duration-300': account != 'Line' }"
              :disabled="account == 'Line'">Line</button>
          </div>
          <div class="max-h-32">
            <component :is="account" :data="accountData" :options="accountOptions" />
          </div>
        </div>
        <div class="p-2">
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
        </div>

      </div>
    </div>
  </div>
</template>