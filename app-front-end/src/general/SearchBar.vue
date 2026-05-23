<script setup lang="ts">
import { InputGroup, InputGroupAddon, InputGroupInput } from '@/components/ui/input-group';
import { Search } from 'lucide-vue-next';
import { computed } from 'vue';

const search = defineModel<string>();
const { size = 'default' } = defineProps<{ size?: 'default' | 'lg' }>();

const sizeClass = computed(() => {
  let s = 'w-60';

  if (size === 'lg') s += ' h-[43.2px]';

  return s;
});
</script>

<template>
  <InputGroup v-bind="$props" :class="sizeClass">
    <InputGroupInput v-model="search" placeholder="Search" />
    <InputGroupAddon>
      <Search />
    </InputGroupAddon>
  </InputGroup>
</template>
