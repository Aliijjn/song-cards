<script setup lang="ts">
import { computed } from 'vue';
import { useColorMode } from '@vueuse/core';
import { Switch } from '@/components/ui/switch';
import { CircleDashed, Moon, Sun, User } from 'lucide-vue-next';
import { router } from '@/router.ts';
import { Button } from '@/components/ui/button';
import AuthenticateButton from '@/general/AuthenticateButton.vue';
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuSub,
  DropdownMenuSubTrigger,
  DropdownMenuPortal,
  DropdownMenuSubContent,
  DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { Separator } from '@/components/ui/separator';
import {
  NavigationMenu,
  NavigationMenuList,
  NavigationMenuItem,
} from '@/components/ui/navigation-menu';

type ColorOption = {
  value: string;
  displayName: string;
  icon: any;
};

const mode = useColorMode();

const colorOptions: ColorOption[] = [
  {
    value: 'light',
    displayName: 'Light Mode',
    icon: Sun,
  },
  {
    value: 'dark',
    displayName: 'Dark Mode',
    icon: Moon,
  },
  {
    value: 'auto',
    displayName: 'System Default',
    icon: CircleDashed,
  },
];
</script>

<template>
  <div class="flex justify-end items-center gap-4 p-2">
    <div class="flex gap-2">
      <Button variant="ghost" size="sm" @click="router.push('/')">Higher Lower</Button>
      <Button variant="ghost" size="sm" @click="router.push('/cards')">Game Cards</Button>
      <Button variant="ghost" size="sm" @click="router.push('/curations')">Curations</Button>
    </div>

    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <Button variant="outline" size="icon">
          <User class="size-5 stroke-[1.2]" />
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="start" class="w-35 mr-3">
        <DropdownMenuGroup>
          <DropdownMenuItem>
            <AuthenticateButton />
          </DropdownMenuItem>
          <DropdownMenuSub>
            <DropdownMenuSubTrigger data-sidebar="left"> Theme</DropdownMenuSubTrigger>
            <DropdownMenuPortal>
              <DropdownMenuSubContent>
                <DropdownMenuItem
                  v-for="option in colorOptions"
                  :key="option.value"
                  @click="mode = option.value"
                >
                  <component :is="option.icon" />
                  {{ option.displayName }}
                </DropdownMenuItem>
              </DropdownMenuSubContent>
            </DropdownMenuPortal>
          </DropdownMenuSub>
        </DropdownMenuGroup>
      </DropdownMenuContent>
    </DropdownMenu>
  </div>
  <Separator />
</template>

<style>
.header-grid {
  display: flex;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem;
}
</style>
