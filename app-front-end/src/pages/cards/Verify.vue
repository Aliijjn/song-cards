<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useCardStore } from "@/pages/cards/cardStore.ts";
import type { SongCardDTO } from "@/types/types.ts";
import { Table, TableBody, TableCell, TableEmpty, TableRow } from "@/components/ui/table";
import { Checkbox } from "@/components/ui/checkbox";
import { Button } from "@/components/ui/button";
import { Pencil } from 'lucide-vue-next';
import EditSongCardDialog from "@/pages/cards/components/EditSongCardDialog.vue";
import { router } from "@/router.ts";
import {exportCardData, getCardData} from "@/pages/cards/api.ts";
import {Spinner} from "@/components/ui/spinner";

const cardStore = useCardStore();

const isLoading = ref(true)
const isCreatingCards = ref(false);
const validCards = ref<SongCardDTO[]>([])
const invalidCards = ref<SongCardDTO[]>([])

const isEditing = ref(false)
const editedCardIndex = ref<number | null>(null)

onMounted(async () => {
  const response = await getCardData(cardStore.selectedPlaylists);
  if (response.status === "success") {
    const { valid, invalid } = response.value;

    validCards.value = valid;
    invalidCards.value = invalid;
    cardStore.selectedSongCards = JSON.parse(JSON.stringify(validCards.value));
  } else {
    console.error("Unable to get card data", response.message);
  }
  isLoading.value = false
});

function toggleSongCard(songCard: SongCardDTO) {
  if (isCreatingCards.value) return;

  if (cardStore.selectedSongCards.findIndex((sc) => sc.id === songCard.id) !== -1) {
    cardStore.selectedSongCards = cardStore.selectedSongCards.filter((sc) => sc.id !== songCard.id);
  } else {
    cardStore.selectedSongCards.push(songCard);
  }
}

function editSongCard(index: number) {
  editedCardIndex.value = index;
  isEditing.value = true
}

async function startExport() {
  isCreatingCards.value = true
  const response = await exportCardData(cardStore.selectedSongCards)
  if (response.status === "success") {
    const uuid = response.value;
    router.push(`/cards/preview/${uuid}`);
  }
  isCreatingCards.value = false
}
</script>

<template>
  <EditSongCardDialog v-if="editedCardIndex !== null" v-model="invalidCards[editedCardIndex]" v-model:is-open="isEditing" />

  <div class="flex flex-col gap-5 max-w-[720px]">
    <div class="flex justify-between">
      <div>
        <div class="flex text-3xl">Verify Data</div>
        <div>These songs likely have some invalid data. You can manually edit and include them before continuing</div>
      </div>
    </div>

    <Table>
      <TableEmpty v-if="isCreatingCards">
        <div class="flex gap-2">
          <div> Creating cards, this might take a while </div>
          <Spinner class="size-10" />
        </div>
      </TableEmpty>
      <TableEmpty v-else-if="isLoading">
        <Spinner class="size-10" />
      </TableEmpty>
      <TableEmpty v-else-if="!invalidCards.length">
        No invalid songs found
      </TableEmpty>
      <TableBody v-else>
        <TableRow
            v-for="(invalidCard, index) in invalidCards"
            :key="invalidCard.id"
            @click="toggleSongCard(invalidCard)"
        >
          <TableCell>
            <div class="flex flex-row items-center gap-3">
              <Checkbox
                  :id="invalidCard.id"
                  :model-value="cardStore.selectedSongCards.includes(invalidCard)"
                  :disabled="isCreatingCards"
                  class="mx-2"
              />
              <img
                  :src="invalidCard.imageUrl"
                  alt="image"
                  class="h-15 w-15 rounded object-cover"
              />
              <div class="flex flex-col truncate">
                <div class="font-bold"> {{ invalidCard.name }} </div>
                <div> {{ invalidCard.artist }} </div>
              </div>
            </div>
          </TableCell>

          <!-- Year -->
          <TableCell class="pl-12 pr-7">
            <div class="flex justify-end px-3">
            </div>
            {{ invalidCard.release_year }}
          </TableCell>

          <!-- Actions -->
          <TableCell class="px-5">
            <Button size="icon" variant="outline" :disabled="isCreatingCards" @click="editSongCard(index)">
              <Pencil />
            </Button>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>

  <div class="apply-button">
    <Button size="lg" :disabled="isCreatingCards" @click="startExport"> Create Cards </Button>
  </div>
</template>

<style scoped lang="css">
.apply-button {
  position: fixed;
  bottom: 1rem;
  margin: 0 auto;
}
</style>