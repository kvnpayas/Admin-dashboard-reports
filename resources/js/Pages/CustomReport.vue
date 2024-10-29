<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { emit } from '../eventBus';

const page = usePage()

const reportLists = ref({})
const selectedRow = ref('')
const selectedCol = ref('')
const saveButton = ref()
const selectedReportError = ref(false)

const props = defineProps({
  customReport: Object,
});

const imageSrc = 'img/graph.png'

const defaultRow = ref([
  {
    col: 1,
    user_id: page.props.auth.user.id,
    add: true,
    row: 1,
    col: 1,
    report_cols: [{
      report_id: null,
      max_col: null,
      span: null,
    }]
  },
]);

const rowLists = props.customReport.length > 0 ? props.customReport : defaultRow.value;
const checkRow = () => {
  for (const row of rowLists) {
    for (const report of row.report_cols) {
      if (report.report_id === null) {
        saveButton.value = false;
        return;
      }
    }
  }
  saveButton.value = true;
};
checkRow()
const addColumn = (row) => {
  const rowNumber = Number(rowLists[row].col) + 1
  rowLists[row].col = rowNumber
  rowLists[row].report_cols.push({
    report_id: null,
    max_col: null,
    span: null,
  });

  if (rowLists[row].col == 3) {
    rowLists[row].add = '0';
  }
  checkRow()
}
const removeColumn = (row) => {
  rowLists[row].col -= 1
  if (rowLists[row].report_cols.length > 0) {
    rowLists[row].report_cols.pop();
  }

  if (rowLists[row].col != 3) {
    rowLists[row].add = '1';
  }
  checkRow()
}

const selectedReport = (report, modal) => {
  if (report.max_col >= rowLists[selectedRow.value].col) {
    rowLists[selectedRow.value].report_cols[selectedCol.value] = {
      report_id: report.id,
      name: report.name,
      max_col: report.max_col,
      span: 1,
    }
    const minCols = getMinCol(selectedRow.value)
    if (rowLists[selectedRow.value].col < minCols) {
      rowLists[selectedRow.value].add = '1';
    } else {
      rowLists[selectedRow.value].add = '0';
    }
    hideModal(modal)
  } else {
    selectedReportError.value = true
    console.log("Error")
  }

  checkRow()
}

const removeReport = (row, index) => {
  if (rowLists[row].report_cols[index].span != 1) {
    rowLists[row].report_cols[index] = {
      report_id: null,
      max_col: null,
      span: null,
    }
    // console.log
    rowLists[row].report_cols.push({
      report_id: null,
      max_col: null,
      span: null,
    });
  } else {
    rowLists[row].report_cols[index] = {
      report_id: null,
      max_col: null,
      span: null,
    }
  }

  const minCols = getMinCol(row)

  if (rowLists[row].col == 3) {
    rowLists[row].add = '0';
  } else {
    if (minCols) {
      if (rowLists[row].col < minCols) {
        rowLists[row].add = '1';
      } else {
        rowLists[row].add = '0';
      }
    } else {
      rowLists[row].add = '1';
    }
  }

  checkRow()
}

const getMinCol = (row) => {

  const maxCols = rowLists[row].report_cols.map(report => report.max_col);
  const validMaxCols = maxCols.filter(max_col => max_col !== null);
  return validMaxCols.length > 0 ? Math.min(...validMaxCols) : null;
}

const addRow = () => {
  rowLists.push({
    col: 1,
    user_id: page.props.auth.user.id,
    add: true,
    row: 1,
    col: 1,
    report_cols: [{
      report_id: null,
      max_col: null,
      span: 1,
    }]
  });
  checkRow()
}
const removeRow = () => {
  if (rowLists.length > 0) {
    rowLists.pop();
  }
  checkRow()
}

const spanCol = (row, col) => {
  // const nxtRow = row + 1
  rowLists[row].report_cols.pop();
  rowLists[row].report_cols[col].span = 2
  checkRow()
}

const removeSpan = (row, col) => {
  rowLists[row].report_cols[col].span = 1
  rowLists[row].report_cols.push({
    report_id: null,
    max_col: null,
    span: null,
  });
  checkRow()
}

const checkColLength = (row) => {
  const sum = rowLists[row].report_cols.reduce((acc, report) => {
    return acc + (Number(report.span) ?? 0);
  }, 0);
  return sum
}

const savelayout = async () => {
  if(saveButton.value){
    await axios.post('/dashboard-customizer', rowLists).then(response => {
      emit('successMessage', response.data.success)
    })
  }else{
    emit('failMessage', 'Error saving layout!')
  }
}

// Modals
const modals = ref({});

const showModal = async (id, row, col) => {
  selectedRow.value = row
  selectedCol.value = col
  const response = await axios.get('/report-lists')
  reportLists.value = response.data
  modals.value[id] = true;
};

const hideModal = (id) => {
  modals.value[id] = false;
  selectedReportError.value = false
};
// End Modals

console.log(rowLists)
</script>
<template>
  <div class="shadow-inner py-5">
    <div class="max-w-[95%] mx-auto my-5 shadow-xl">
      <div class="bg-main p-4 rounded-t-md flex">
        <h3 class="text-2xl font-extrabold text-light uppercase">Custom Report Dashboard</h3>
      </div>
      <div class="p-5 bg-accent bg-opacity-45 rounded-b-md">
        <div class="border-2 bg-light rounded space-y-5 p-5">
          <div class="text-center">
            <h3 class="text-lg md:text-xl uppercase tracking-[0.8rem] text-primary font-semibold">Welcome Back!</h3>
            <span class="text-lg md:text-xl uppercase tracking-[0.5rem] text-secondary">{{ $page.props.auth.user.name
              }}</span>
          </div>
          <div v-for="(col, row) in rowLists" :key="row" class="border-2 p-5 bg-neutral-100 z-1">
            <div class="flex p-2 mb-3">
              <span class="uppercase text-xs font-black">Row {{ row + 1 }}</span>
              <button @click="addColumn(row)"
                :class="{ 'bg-green-500 hover:bg-green-600 hover:scale-110 transition-all duration-300': col.add == '1', 'bg-accent': col.add == '0' }"
                class="ml-auto px-4 py-1 rounded-md uppercase font-extrabold text-white text-xs"
                :disabled="col.add == '0'">Add
                Column</button>
            </div>
            <div :class="`grid grid-cols-1 xl:grid-cols-${col.col} gap-4`">
              <div v-for="(data, index) in col.report_cols" :key="data.id"
                class="shadow border border-light rounded p-2 text-center relative" :class="`col-span-${data.span}`">
                <button v-if="index != 0 && index == col.col - 1" @click="removeColumn(row)"
                  class="absolute right-[-20px] top-[-10px] hover:scale-110 transition-all duration-300">
                  <span class=" text-xs font-black text-white">
                    <v-icon name="io-remove-circle" class="text-red-500 mr-2"></v-icon>
                  </span>
                </button>
                <div v-if="data.report_id" class="flex flex-col gap-4">
                  <div class="flex justify-end gap-1">
                    <div v-if="col.col == 3">
                      <div v-if="checkColLength(row) == col.col">
                        <button v-if="data.span == 2" @click="removeSpan(row, index)"
                          class=" text-white rounded text-xs ">
                          <v-icon name="bi-caret-left-square-fill" class="text-yellow-500"></v-icon>
                        </button>
                      </div>
                      <div v-else>
                        <div v-if="index != col.report_cols.length - 1">
                          <button v-if="data.span == 1" @click="spanCol(row, index)"
                            class=" text-white rounded text-xs ">
                            <v-icon name="bi-caret-right-square-fill" class="text-yellow-500"></v-icon>
                          </button>
                          <button v-else @click="removeSpan(row, index)"
                            class="bg-red-500 text-white px-2 rounded text-xs ">
                            remove span
                          </button>
                        </div>
                        <div v-else>
                          <button v-if="data.span == 2" @click="removeSpan(row, index)"
                            class="bg-red-500 text-white px-2 rounded text-xs ">
                            remove span
                          </button>
                        </div>
                      </div>
                    </div>
                    <button @click="removeReport(row, index)"
                      class=" text-xs text-white hover:scale-110 transition-all duration-300">
                      <v-icon name="oi-x-circle-fill" class="text-red-500"></v-icon>
                    </button>

                  </div>
                  <div class="flex flex-col justify-center">
                    <div>
                      <span class="uppercase text-lg font-black text-primary">{{ data.name }}</span>
                    </div>
                    <div class="flex justify-center content-between opacity-55">
                      <img :src="imageSrc" alt="Dynamic Image" class="h-20">
                    </div>
                    <!-- {{  col.col }} -->
                    <!-- && checkColLength(row) != col.col -->
                    <!-- v-if="col.col == 3" -->
                    <!-- && data.span != 1  && index == col.reports.length - 1 -->
                    <!-- {{ checkColLength(row) }} - 
                   {{ col.col }} -->
                  </div>
                  <!-- <div>
                    <button @click="removeReport(row, index)"
                      class="bg-red-500 rounded py-0.5 px-4 text-xs text-white hover:scale-110 transition-all duration-300">
                      <v-icon name="bi-trash-fill"></v-icon>
                      <span>Remove Report</span>
                    </button>
                  </div> -->
                </div>
                <div v-else>
                  <button @click="showModal('reportLists', row, index)"
                    class="p-2 hover:scale-110 transition-all duration-300">
                    <span class="text-accent block">Add Report</span>
                    <v-icon name="fa-chart-pie" class="text-accent mr-2" scale="4"></v-icon>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="space-x-2">
            <button @click="addRow()"
              class="shadow-xl bg-primary rounded px-4 py-1 text-xs text-white hover:-translate-y-1 transition-all duration-300">
              <v-icon name="bi-plus-circle-fill" class="text-green-500 mr-2"></v-icon>Add row
            </button>
            <button @click="removeRow()" class="shadow-xl  rounded px-4 py-1 text-xs text-white"
              :class="{ 'bg-red-500 hover:-translate-y-1 transition-all duration-300': rowLists.length != 1, 'bg-accent': rowLists.length == 1 }"
              :disabled="rowLists.length == 1">
              <v-icon name="io-remove-circle" class="text-white mr-2"></v-icon>Remove last row
            </button>
          </div>
          <div class="text-center">
            <button @click="savelayout()"
            :class="{'bg-green-500 hover:-translate-y-1': saveButton, 'bg-accent': !saveButton}"
              class=" px-4 rounded text-white shadow-xl text-xl font-black transition-all duration-300" :disabled="!saveButton">Save
              Layout</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODALS -->
  <!-- Report Lists -->
  <div id="reportLists"
    class="modal overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-100% max-h-full"
    :class="{ 'modal-hide': !modals['reportLists'], 'modal-show': modals['reportLists'] }">
    <div class="relative p-4 w-full max-w-lg max-h-full modal-content"
      :class="{ 'modal-content-hide': !modals['reportLists'], 'modal-content-show': modals['reportLists'] }">
      <div class="relative  rounded-lg shadow-xl">
        <div class="flex items-center justify-between p-4 border-b-2 border-secondary rounded-t bg-primary">
          <h3 class="text-xl font-extrabold text-light">
            Add Report
          </h3>
          <button type="button" @click="hideModal('reportLists')"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-4 h-4 ms-auto inline-flex justify-center items-center">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <div class=" bg-white flex flex-col space-y-2 py-5 px-5">
          <div v-if="selectedReportError" class="text-center">
            <span class="text-xs text-red-500">Invalid report selected! Please reduce the column in the row or choose
              another report.</span>
          </div>
          <button v-for="list in reportLists" :key="list.id" @click="selectedReport(list, 'reportLists')"
            class="text-accent bg-light border p-1 shadow flex justify-between hover:bg-secondary hover:text-white hover:scale-x-105 hover:-translate-y-1 transition-all duration-300">
            <span class="py-1 text-xs ">{{ list.name }}</span>
          </button>
        </div>
        <!-- <div class="p-5 bg-white border-t-2 rounded-b flex justify-center items-center space-x-5">
          <button class="px-5 py-1 rounded border bg-green-500 text-white font-bold shadow hover:bg-green-600"
            @click="createUsers()">Create</button>
          <button class="px-5 py-1 rounded border bg-red-500 text-white font-bold shadow hover:bg-red-600"
            @click="hideModal('reportLists')">Cancel</button>
        </div> -->
      </div>
    </div>
  </div>
  <!-- End Report Lists -->
</template>