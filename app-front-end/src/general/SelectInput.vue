<script setup lang="ts">
import {
  Select,
  SelectTrigger,
  SelectValue,
  SelectContent,
  SelectItem,
} from '@/components/ui/select';

type Option = {
  label: string;
  value: any;
  disabled?: boolean;
};

defineProps<{
  options: Option[];
  placeholder?: string;
}>();

const model = defineModel<any>();
</script>

<template>
  <Select v-model="model">
    <SelectTrigger v-bind="$attrs">
      <SelectValue :placeholder="placeholder ?? 'Select option'" />
    </SelectTrigger>

    <SelectContent>
      <SelectItem
        v-for="option in options"
        :key="option.value"
        :value="option.value"
        :disabled="option.disabled"
      >
        {{ option.label }}
      </SelectItem>
    </SelectContent>
  </Select>
</template>
