<script setup lang="ts">
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';

export type Breadcrumb = {
  text: string;
  to?: string;
};

const { breadcrumbs } = defineProps<{ breadcrumbs: Breadcrumb[] }>();
</script>

<template>
  <teleport defer to="#breadcrumb-teleport">
    <Breadcrumb>
      <BreadcrumbList>
        <BreadcrumbItem>
          <BreadcrumbLink class="text-base" href="/"> Home</BreadcrumbLink>
        </BreadcrumbItem>
        <BreadcrumbItem v-for="(breadcrumb, i) in breadcrumbs" :key="i">
          <BreadcrumbSeparator class="pb-0.5 -mx-0.5" />
          <BreadcrumbLink class="text-base" v-if="breadcrumb.to" :href="breadcrumb.to ?? null">
            {{ breadcrumb.text }}
          </BreadcrumbLink>
          <BreadcrumbPage class="text-base" v-else> {{ breadcrumb.text }}</BreadcrumbPage>
        </BreadcrumbItem>
      </BreadcrumbList>
    </Breadcrumb>
  </teleport>
</template>
