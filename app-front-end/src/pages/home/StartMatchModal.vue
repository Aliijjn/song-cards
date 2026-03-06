<script setup lang="ts">
import { ref, computed } from 'vue'
import { Button } from '@/components/ui/button'
import { useBaseStore } from "@/stores/baseStore";
import { difficulties, type Difficulty, type GenreDTO } from "@/types/types.ts";
import {
  Dialog, DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { ButtonGroup } from "@/components/ui/button-group";
import { Separator } from "@/components/ui/separator";
import { router } from "@/router.ts";

//=============================================================================

const store = useBaseStore()

function startMatch() {
  store.startMatchModal = false
  router.push('/game');
}
</script>

<template>
  <Dialog v-model:open="store.startMatchModal" class="rounded-2xl">
    <form>
      <DialogContent class="sm:max-w-[425px] rounded-2xl p-0 gap-0">
        <DialogHeader class="p-6">
          <DialogTitle>
            {{ store.selectedGenre?.name ?? "Quick Match" }}
          </DialogTitle>
          <DialogDescription>
            {{ store.selectedGenre?.description ?? "The easiest way to play, using all genres" }}
          </DialogDescription>
          <div class="grid gap-4 mt-2">
            <div class="grid gap-2">
              <Label for="difficulty-button">Difficulty</Label>
              <ButtonGroup id="difficulty-button" class="flex w-full rounded-full">
                <Button
                    v-for="difficulty in difficulties"
                    :key="difficulty.value"
                    :variant="store.difficulty.name === difficulty.name ? 'default' : 'outline'"
                    class="flex-1  rounded-full"
                    @click="store.difficulty = difficulty"
                >
                  {{ difficulty.name }}
                </Button>
              </ButtonGroup>
            </div>
            <div v-if="store.difficulty.name === 'Custom'" class="grid gap-2">
              <Label for="difficulty-slider">Custom Difficulty</Label>
              <Input
                  id="difficulty-slider"
                  v-model="store.difficulty.value"
                  class="rounded-full px-4"
                  type="number"
                  :min="0"
                  :max="85"
                  default-value="50"
              />
            </div>
          </div>
        </DialogHeader>

        <Separator />

        <DialogFooter class="p-3">
          <DialogClose as-child>
            <Button variant="outline" class="rounded-full" @click="store.startMatchModal = false">
              Cancel
            </Button>
          </DialogClose>
          <Button type="submit" class="rounded-full" @click="startMatch">
            Start Match
          </Button>
        </DialogFooter>
      </DialogContent>
    </form>
  </Dialog>
</template>
