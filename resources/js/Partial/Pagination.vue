<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  pagination: Object,
  onPageChange: Function
});
const currentPage = ref(props.pagination ? props.pagination.current_page : 1);

watch(() => props.pagination, (newPagination) => {
  if (newPagination) {
    currentPage.value = newPagination.current_page;
  }
});


const nextPage = () => {
  if (props.pagination && props.pagination.next_page_url) {
    props.onPageChange(currentPage.value + 1);
  }
};

const prevPage = () => {
  if (props.pagination && props.pagination.prev_page_url) {
    props.onPageChange(currentPage.value - 1);
  }
};

const goToPage = (page) => {
  props.onPageChange(page);
};

</script>
<template>
  <div class="pagination w-full flex justify-center" v-if="pagination && pagination.data">
    <div v-if="pagination.last_page > 5" class="font-bold text-accent space-x-2 text-xs">
      <button @click="prevPage" :disabled="!pagination.prev_page_url" class="py-2 px-3 bg-light border"><</button>
      <button v-if="pagination.current_page >= pagination.last_page - 2" @click="goToPage(1)" class="py-2 px-3 bg-light border" >1</button>
      <span v-if="pagination.current_page >= pagination.last_page - 2" class="py-2 px-3 bg-light border">...</span>
      <button v-if="pagination.current_page > 1" @click="goToPage(pagination.current_page - 1)" class="py-2 px-3 bg-light border">{{ pagination.current_page - 1 }}</button>
      <button class="py-2 px-3 bg-secondary border text-white">{{ pagination.current_page }}</button>
      <button v-if="pagination.current_page < pagination.last_page" @click="goToPage(pagination.current_page + 1)" class="py-2 px-3 bg-light border">{{ pagination.current_page + 1 }}</button>
      <button v-if="pagination.current_page == 1" @click="goToPage(pagination.current_page + 2)" class="py-2 px-3 bg-light border">{{ pagination.current_page + 2 }}</button>
      <span v-if="pagination.current_page < pagination.last_page - 2" class="py-2 px-3 bg-light border">...</span>
      <button v-if="pagination.current_page < pagination.last_page - 1" @click="goToPage(pagination.last_page)" class="py-2 px-3 bg-light border">{{ pagination.last_page }}</button>
      <button @click="nextPage" :disabled="!pagination.next_page_url" class="py-2 px-3 bg-light border">></button>
    </div>
    <div v-else>
      <button @click="prevPage" :disabled="!pagination.prev_page_url">Previous</button>
      <button v-for="page in pagination.last_page" :key="page" @click="goToPage(page)">{{ page }}</button>
      <button @click="nextPage" :disabled="!pagination.next_page_url">Next</button>
    </div>
  </div>
</template>