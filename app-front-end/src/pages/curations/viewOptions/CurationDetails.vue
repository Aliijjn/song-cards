<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Save } from 'lucide-vue-next';
import { Textarea } from '@/components/ui/textarea';
import { Spinner } from '@/components/ui/spinner';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import CurationAPI from '@/pages/curations/api.ts';
import CurationDTO = App.Data.CurationDTO;
import CurationUpdateDTO = App.Data.CurationUpdateDTO;

const curation = defineModel<CurationDTO>({ required: true });
const isLoading = defineModel<boolean>('isLoading', { required: true });

async function update() {
  if (!curation.value) return;

  const update: CurationUpdateDTO = {
    name: curation.value.name,
    description: curation.value.description,
  };
  isLoading.value = true;
  await CurationAPI.update(curation.value.id, update);
  isLoading.value = false;
}
</script>

<template>
  <div class="flex flex-col gap-4">
    <div class="flex flex-col">
      <Label for="title"> Title </Label>
      <Input id="title" v-model="curation.name" />
    </div>
    <div class="flex flex-col">
      <Label for="created_by"> Created By </Label>
      <Input id="created_by" v-model="curation.createdBy" disabled />
    </div>
    <div class="flex flex-col">
      <Label for="description"> Description </Label>
      <Textarea id="description" v-model="curation.description" />
    </div>
    <Button class="mt-2 w-20" size="lg" :disabled="isLoading" @click="update">
      <Spinner v-if="isLoading" />
      <Save v-else />
      Save
    </Button>
  </div>
</template>
