<script setup lang="ts">
import {router} from "@/router.ts";
import dayjs from "dayjs";
import {Button} from "@/components/ui/button";
import {Table, TableBody, TableCell, TableEmpty, TableRow} from "@/components/ui/table";
import {ArrowLeft, ArrowRight} from "lucide-vue-next";
import {ref, watch} from "vue";
import ExportDTO = App.Data.ExportDTO;
import {fetchExports} from "@/pages/cards/api.ts";

const { showFooter = false } = defineProps<{
  showFooter?: boolean;
}>()

const start = defineModel<number>('start', { default: 0 });
const length = defineModel<number>('length', { default: 5 });

const isLoading = ref(false);
const exports = ref<ExportDTO[]>([]);

watch([start, length], async () => {
  isLoading.value = true;
  const response = await fetchExports(1, start.value, length.value);
  if (response.status === "success") {
    exports.value = response.value;
  }
  isLoading.value = false;
}, { immediate: true });
</script>

<template>
  <div class="flex flex-col gap-4">
    <Table class=" max-w-[1080px] table-fixed">
      <TableBody v-if="isLoading">
        <TableRow>
          <TableCell>
            Loading...
          </TableCell>
        </TableRow>
      </TableBody>
      <TableBody v-else>
        <TableEmpty v-if="!exports.length">
          No exports found
        </TableEmpty>
        <TableRow
            v-for="e in exports"
            :key="e.id"
            @click="router.push(`cards/preview/${e.id}`)"
            class="max-w-[1080px]"
        >
          <TableCell class="flex flex-row items-center gap-3">
            <div class="flex flex-col truncate">
              <div class="font-bold"> {{ e.name }} </div>
              <div> {{ e.user_name }} </div>
            </div>

          </TableCell>
          <TableCell>
            {{ dayjs(e.created_at).format("MMMM DD YYYY HH:mm") }}
          </TableCell>
          <TableCell class="text-right">
            <Button size="icon" variant="outline"> <ArrowRight /> </Button>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>

    <div v-if="!isLoading && showFooter" class="flex flex-row justify-center">
      <div class="flex flex-row justify-between items-center gap-4">
        <Button variant="outline" :disabled="start <= 0" @click="start = Math.max(0, start - length)">
          <ArrowLeft />
          Previous
        </Button>
        <div>
          {{ start + 1 }}-{{ start + exports.length }}
        </div>
        <Button variant="outline" :disabled="exports.length < length" @click="start += length">
          Next
          <ArrowRight />
        </Button>
      </div>
    </div>
  </div>
</template>