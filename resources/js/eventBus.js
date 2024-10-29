import { ref } from 'vue';

const eventBus = ref(new Map());

export function emit(event, ...args) {
  if (eventBus.value.has(event)) {
    eventBus.value.get(event).forEach(callback => callback(...args));
  }
}

export function on(event, callback) {
  if (!eventBus.value.has(event)) {
    eventBus.value.set(event, []);
  }
  eventBus.value.get(event).push(callback);
}
