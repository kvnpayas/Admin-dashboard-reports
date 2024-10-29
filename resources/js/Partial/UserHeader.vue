<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const days = ["Sun", "Mon", "Tues", "Wed", "Thu", "Fri", "Sat"];
const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
const date = ref({});
// const currentTime = ref(getCurrentTime());
const currentDate = ref(getDate());
const hours = ref('');
const minutes = ref('');
const seconds = ref('');

function getCurrentTime() {
  const time = new Date();
  hours.value = time.getHours()
  minutes.value = time.getMinutes()
  seconds.value = time.getSeconds()
}
function getDate() {
  const now = new Date();
  date.value['days'] = days[now.getDay()];
  date.value['day'] = now.getDate();
  date.value['month'] = months[now.getMonth()];
  date.value['year'] = now.getFullYear();
  return date
}

function updateDate() {
  currentDate.value = getDate()
}

onMounted(() => {
  // getCurrentTime();
  const interval = setInterval(getCurrentTime, 1000);
  const intervalDate = setInterval(updateDate, 3600000);
  onUnmounted(() => {
    clearInterval(interval);
    clearInterval(intervalDate);
  });
});
// console.log(days[now.getDay()])
</script>
<template>
  <div class="relative h-20 text-center">
    <div class="absolute left-1/2 transform -translate-x-1/2">
      <h3 class="text-lg md:text-2xl uppercase tracking-[0.8rem] text-primary font-semibold dark:text-light">Welcome Back!</h3>
      <span class="text-lg md:text-2xl uppercase tracking-[0.5rem] text-secondary">{{ $page.props.auth.user.name
        }}</span>
    </div>
    <div class="absolute right-0 bg-white bg-opacity-65 grid grid-cols-5 rounded py-2 px-4 text-accent shadow-lg hover:bg-opacity-100 transition-all duration-300">
      <div class="flex flex-col justify-center">
        <span class="uppercase text-2xl tracking-[0.5rem]">{{ date.days }}</span>
        <span class="text-[0.5rem] uppercase text-secondary">Day</span>
      </div>
      <div class="flex justify-around">
        <div class="flex flex-col justify-center">
          <span class="uppercase text-2xl tracking-[0.5rem]">{{ hours }}</span>
          <span class="text-[0.5rem] uppercase text-secondary">Hours</span>
        </div>
        <span class="text-2xl">:</span>
      </div>
      <div class="flex justify-around">
        <div class="flex flex-col justify-center">
          <span class="uppercase text-2xl tracking-[0.5rem]">{{ minutes }}</span>
          <span class="text-[0.5rem] uppercase text-secondary">Minutes</span>
        </div>
        <span class="text-2xl">:</span>
      </div>
      <div class="flex flex-col justify-start">
        <span class="uppercase text-2xl tracking-[0.5rem]">{{ seconds }}</span>
        <span class="text-[0.5rem] uppercase text-secondary">Seconds</span>
      </div>
      <div class="flex flex-col justify-center">
        <span class="uppercase text-xs tracking-widest font-black">{{ date.month }}</span>
        <span class="uppercase text-xs font-black">{{ date.day }}</span>
        <span class="uppercase text-xs font-black">{{ date.year }}</span>
      </div>
    </div>
  </div>
  <!-- <div class="grid lg:grid-cols-4 grid-cols-1 gap-5">
    <div class="col-span-2  bg-light border-light rounded-md shadow-xl shadow-neutral-400 grid grid-cols-2">
      <h3 class="text-3xl font-semibold text-primary p-4">Welcome back!
        <span class="uppercase text-secondary">{{ $page.props.auth.user.name }}</span>
      </h3>
      <div class=" bg-gradient-primary p-4 rounded flex justify-end " style="--mask:
    radial-gradient(8.94rem at 12rem 50%,#000 99%,#0000 101%) 0 calc(50% - 8rem)/100% 16rem,
    radial-gradient(8.94rem at -8rem 50%,#0000 99%,#000 101%) 4rem 50%/100% 16rem repeat-y;
  -webkit-mask: var(--mask);
          mask: var(--mask);">
        <img src="/public/img/user-dash.png" alt="" class="h-40 mr-6">
      </div>
    </div>
    <div
      class="border-2 bg-light border-light rounded-md p-4 shadow-xl shadow-neutral-400 flex justify-center items-center">
      <div>
        <div class="grid grid-cols-2 gap-3">
          <span class="text-6xl font-black text-secondary uppercase">{{ date.days }}</span>
          <div class="flex flex-col mt-1.5 text-lg font-black text-accent uppercase">
            <span>{{ date.month }}</span>
            <span>{{ date.year }}</span>
          </div>
        </div>
        <div class="">
          <span class="text-3xl font-black text-primary">{{ currentTime }}</span>
        </div>
      </div>
    </div>
  </div> -->
</template>