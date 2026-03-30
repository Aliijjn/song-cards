<script setup lang="ts">
import {InputGroup, InputGroupAddon, InputGroupInput} from "@/components/ui/input-group";
import {Search} from "lucide-vue-next";

const search = defineModel<string>()
</script>

<template>
  <InputGroup v-bind="$props" class="w-60">
    <InputGroupInput v-model="search" placeholder="Search" />
    <InputGroupAddon>
      <Search />
    </InputGroupAddon>
  </InputGroup>
</template>