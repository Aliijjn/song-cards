<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { router } from '@/router.ts';
import SearchBar from '@/general/SearchBar.vue';
import SelectPlaylists from '@/pages/cards/components/SelectPlaylists.vue';
import { Spinner } from '@/components/ui/spinner';
import CurationAPI from '@/pages/curations/api.ts';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import CurationCreationDTO = App.Data.CurationCreationDTO;

const curation = ref<CurationCreationDTO>({
  name: '',
  description: '',
  userId: 1,
  playlistIds: [],
});

const search = ref('');
const isLoading = ref(true);

async function create() {
  await CurationAPI.create(curation.value);

  router.push('/curations');
}
</script>

<template>
  <div class="flex flex-col gap-10 mb-5">
    <div class="flex flex-col gap-3">
      <span class="text-3xl"> Curation Data </span>

      <div>
        <Label for="title">Title</Label>
        <Input v-model="curation.name" id="title" />
      </div>

      <div>
        <Label for="description">
          Description
          <span class="text-muted-foreground text-sm">(optional)</span>
        </Label>
        <Textarea v-model="curation.description" id="description" />
      </div>
    </div>

    <div class="flex flex-col gap-3">
      <div class="flex justify-between items-center">
        <div class="text-3xl">Select playlists</div>
        <SearchBar v-model="search" />
      </div>

      <SelectPlaylists
        v-model="curation.playlistIds"
        v-model:is-loading="isLoading"
        :search="search"
      />
    </div>
  </div>

  <div v-if="curation.playlistIds.length" class="apply-button">
    <Button size="lg" @click="create">
      <Spinner v-if="isLoading" :size="6" />
      Export {{ curation.playlistIds.length }} playlist{{
        curation.playlistIds.length === 1 ? '' : 's'
      }}
    </Button>
  </div>
</template>

<style scoped lang="css">
.apply-button {
  position: fixed;
  bottom: 1rem;
  margin: 0 auto;
}
</style>
