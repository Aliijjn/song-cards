<script setup lang="ts">
import { useColorMode } from '@vueuse/core';
import { ref, computed } from 'vue';
import { CircleDashed, Moon, Sun } from 'lucide-vue-next';

type ColorOption = {
  value: string;
  displayName: string;
  icon: any;
};

const mode = useColorMode();
const options: ColorOption[] = [
  {
    value: 'light',
    displayName: 'Light Mode',
    icon: Sun,
  },
  {
    value: 'dark',
    displayName: 'Dark Mode',
    icon: Moon,
  },
  {
    value: 'auto',
    displayName: 'System Default',
    icon: CircleDashed,
  },
];

const index = ref<number>(options.findIndex((o) => o.value === mode.value));
const selected = computed(() => options[index.value] as ColorOption);

function increment() {
  index.value = (index.value + 1) % options.length;
  mode.value = options[index.value]!.value;
  console.log(index.value);
}
</script>

<template>
  <div class="flex items-center !gap-1" @click="increment">
    <component :is="selected.icon" />
    {{ selected.displayName }}
    {{ mode }}
  </div>
</template>
