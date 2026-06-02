<script setup lang="ts">
import { Dialog, DialogContent } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import CurationCombineDTO = App.Data.CurationCombineDTO;
import { onMounted, ref } from 'vue';
import CurationTable from '@/pages/curations/components/CurationTable.vue';
import CurationAPI from '@/pages/curations/api.ts';
import { router } from '@/router.ts';
import {
  Stepper,
  StepperIndicator,
  StepperItem,
  StepperTitle,
  StepperTrigger,
} from '@/components/ui/stepper';
import { Label } from '@/components/ui/label';
import CurationSummaryDTO = App.Data.CurationSummaryDTO;

const isOpen = defineModel<boolean>('isOpen');
const { currentCurationId } = defineProps<{ currentCurationId: string }>();

const stepIndex = ref(1);
const combine = ref<CurationCombineDTO>({
  name: '',
  description: '',
  userId: 1,
  keepOriginal: true,
  curationIds: [],
});
const curations = ref<CurationSummaryDTO[]>([]);
const isLoading = ref<boolean>(true);

onMounted(async () => {
  const response = await CurationAPI.all();

  if (response.status === 'success') {
    curations.value = response.value.filter((curation) => curation.id !== currentCurationId);
  }
  isLoading.value = false;
});

async function apply() {
  const response = await CurationAPI.combine(currentCurationId, combine.value);

  if (response.status === 'success') {
    const newCurationId = response.value;

    router.push(`/curations/${newCurationId}`);
  }
}
</script>

<template>
  <Dialog v-model:open="isOpen">
    <form>
      <DialogContent class="sm:max-w-[600px]">
        <Stepper v-model="stepIndex" class="block w-full" orientation="horizontal">
          <div class="relative mb-8">
            <!-- Background line -->
            <div class="absolute top-6 left-40 w-[calc(100%-21.5rem)] h-px bg-border" />

            <!-- Active progress line -->
            <div
              class="absolute top-6 left-40 h-px bg-primary transition-all duration-300"
              :class="stepIndex === 1 ? 'w-0' : 'w-[calc(100%-21.5rem)]'"
            />

            <div class="relative flex justify-around">
              <StepperItem v-for="step in 2" :key="step" :step="step" class="flex-1">
                <StepperTrigger class="flex flex-col items-center gap-2 w-full">
                  <StepperIndicator
                    class="z-10 size-10 rounded-full border-2 bg-background transition-all duration-200"
                    :class="[
                      step <= stepIndex
                        ? 'border-primary bg-primary text-primary-foreground'
                        : 'border-muted bg-background text-muted-foreground',
                    ]"
                  >
                    {{ step }}
                  </StepperIndicator>

                  <div class="text-center">
                    <StepperTitle
                      class="font-medium"
                      :class="step <= stepIndex ? 'text-foreground' : 'text-muted-foreground'"
                    >
                      {{ step === 1 ? 'Curation Data' : 'Select Playlists' }}
                    </StepperTitle>
                  </div>
                </StepperTrigger>
              </StepperItem>
            </div>
          </div>

          <div class="flex flex-col gap-4 mt-4">
            <template v-if="stepIndex === 1">
              <div>
                <Label for="title">Title</Label>
                <Input v-model="combine.name" id="title" />
              </div>

              <div>
                <Label for="description">
                  Description
                  <span class="text-muted-foreground text-sm">(optional)</span>
                </Label>
                <Textarea v-model="combine.description" id="description" />
              </div>
            </template>

            <template v-if="stepIndex === 2">
              <curation-table
                v-model:selected="combine.curationIds"
                :curations="curations"
                :is-loading="isLoading"
              />
            </template>
          </div>

          <div class="flex items-center justify-between mt-4">
            <Button :disabled="stepIndex === 1" variant="outline" @click="stepIndex--">
              Back
            </Button>
            <div class="flex items-center gap-3">
              <Button v-if="stepIndex !== 2" @click="stepIndex++"> Next</Button>
              <Button v-if="stepIndex === 2" type="submit" @click="apply"> Submit</Button>
            </div>
          </div>
        </Stepper>
      </DialogContent>
    </form>
  </Dialog>
</template>
