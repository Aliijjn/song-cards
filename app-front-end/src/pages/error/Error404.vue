<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { router } from "@/router.ts";
import { Rewind } from "lucide-vue-next";
</script>

<template>
  <div class="flex flex-col align-center mt-10">
    <span class="text-9xl leading-[0.8]">
      404
    </span>
    <span class="text-3xl">
      Page Not Found
    </span>
    <Button class="!px-6 h-12 mt-10" @click="router.push('/')">
      <Rewind />
      Back To Home
    </Button>
  </div>
</template>