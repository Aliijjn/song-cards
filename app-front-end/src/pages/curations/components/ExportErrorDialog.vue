<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';

const isOpen = defineModel<boolean>('isOpen');
const { errorCount } = defineProps<{ errorCount: number }>();
const emit = defineEmits<{ export: [skipErrors: boolean] }>();
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogContent class="sm:max-w-[420px]">
      <DialogHeader>
        <DialogTitle>Songs with errors</DialogTitle>
      </DialogHeader>
      <p class="text-sm">
        {{ errorCount }} song{{ errorCount === 1 ? '' : 's' }} in this curation
        {{ errorCount === 1 ? 'has' : 'have' }} unresolved errors. How would you like to export?
      </p>
      <DialogFooter class="flex gap-2 sm:flex-row">
        <Button variant="outline" class="flex-1" @click="emit('export', false)"> Export all</Button>
        <Button class="flex-1" @click="emit('export', true)"> Export without errors</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
