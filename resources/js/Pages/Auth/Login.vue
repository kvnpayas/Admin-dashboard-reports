<script setup>
import Guest from "../../Layouts/Guest.vue"
import { useForm } from '@inertiajs/vue3';

defineOptions({ layout: Guest });

const form = useForm({
  username: null,
  password: null,
})

const login = () => {
  // console.log(form)
  form.post('/login', {
    onError: () => form.reset("password"),
  });
}

</script>
<template>
  <div class="min-w-96 bg-light shadow-lg shadow-accent rounded-md p-5">
    <div class="flex justify-center mb-5">
      <img src="/public/img/tei-logo-no-name.png" alt="My Image" class="h-12" />
      <div class="flex justify-center items-center">
        <span class="pt-3 px-2 text-sm text-primary font-extrabold uppercase">Dashboard</span>
      </div>
    </div>
    <!-- <div class="flex justify-center mb-5">
      <h3 class="text-3xl uppercase font-extrabold text-secondary">Login</h3>
    </div> -->
    <form @submit.prevent="login">
      <div class="p-5 flex flex-col space-y-5">
        <div>
          <div class="flex flex-col space-y-2">
            <label for="" class="text-sm font-semibold text-primary">Username</label>
            <input type="text" v-model="form.username"
              class="bg-neutral-100 w-full rounded shadow focus:outline-secondary p-1.5 text-xs border border-light text-accent">
              <small class="text-red-500 font-semibold">{{ form.errors.username }}</small>
          </div>
        </div>
        <div>
          <div class="flex flex-col space-y-2">
            <label for="" class="text-sm font-semibold text-primary">Password</label>
            <input type="password" v-model="form.password"
              class="bg-neutral-100 w-full rounded shadow focus:outline-secondary p-1.5 text-xs border border-light text-accent">
              <small class="text-red-500 font-semibold">{{ form.errors.password }}</small>
          </div>
        </div>
        <p class="text-xs text-accent">If you dont have access please contact support</p>
        <div class="flex justify-center">
          <button type="submit"
            class="w-full bg-secondary py-1.5 font-bold text-primary rounded shadow-md shadow-accent hover:bg-orange-600 transition-all duration-300 ease-in-out hover:-translate-y-1 hover:scale-105">Login</button>
        </div>
      </div>
    </form>
  </div>
</template>