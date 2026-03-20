<script setup lang="ts">
import { onMounted, ref } from 'vue';
import {Button} from "@/components/ui/button";
import { Grid2X2Plus } from "lucide-vue-next";
import {Table, TableBody, TableCell, TableEmpty, TableRow} from "@/components/ui/table";
import ExportDO = App.Data.ExportDO;
import dayjs from "dayjs";
import {fetchExports} from "@/pages/cards/api.ts";

const recentExports = ref<ExportDO[]>([]);
const isLoading = ref(true);

onMounted(async () => {
  const response = await fetchExports();
  if (response.status === "success") {
    recentExports.value = response.value;
  }
  isLoading.value = false;
})

function auth() {
  window.location.href = 'https://127.0.0.1:8001/api/auth';
}
</script>

<template>
  <div class="flex flex-col gap-20">
    <span class="text-5xl"> Song Cards </span>

    <div class="flex justify-between items-center gap-20">
      <div class="flex flex-col gap-2">
        <span class="text-3xl">Custom Cards</span>
        <span>Create custom song cards from your own playlists. Requires Spotify login</span>
      </div>
      <Button size="lg" class="!px-6 h-12" @click="auth">
        <Grid2X2Plus />
        Create Custom Cards
      </Button>
    </div>

    <div class="flex flex-col gap-5">
      <div class="flex flex-col gap-2">
        <span class="text-3xl">Previous Exports</span>
      </div>
      <Table class=" max-w-[1080px] table-fixed">
        <TableBody v-if="isLoading">
          <TableRow>
            <TableCell>
              Loading...
            </TableCell>
          </TableRow>
        </TableBody>
        <TableBody v-else>
          <TableEmpty v-if="!recentExports.length">
            No exports found
          </TableEmpty>
          <TableRow
              v-for="recentExport in recentExports"
              :key="recentExport.uuid"
          >
            <TableCell class="flex flex-row items-center gap-3 max-w-[1080px] cursor-pointer">
              <div class="flex flex-col truncate">
                <div class="font-bold"> {{ recentExport.name }} </div>
                <div> {{ recentExport.user_id }} </div>
              </div>
              <div class="ml-auto px-3 text-right">
                {{ dayjs(recentExport.created_at).format("MMM DD YYYY HH:mm") }}
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </div>
</template>