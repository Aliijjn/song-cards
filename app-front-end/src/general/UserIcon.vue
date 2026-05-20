<script setup lang="ts">
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuPortal,
  DropdownMenuSub,
  DropdownMenuSubContent,
  DropdownMenuSubTrigger,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { Check, CircleDashed, Key, Moon, Sun, User } from 'lucide-vue-next';
import { useColorMode } from '@vueuse/core';
import { auth } from '@/general/auth.ts';
import { Spinner } from '@/components/ui/spinner';
import { useGlobalStore } from '@/stores/globalStore.ts';

const globalStore = useGlobalStore();

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
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <div class="flex items-center">
        <Button v-if="globalStore.isUserSpotifyTokenValid" variant="outline" size="icon-lg">
          <User class="size-5 stroke-[1.2]" />
        </Button>
        <Button v-else>
          <User />
          Log In
        </Button>
      </div>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="start" class="w-45 mr-3">
      <DropdownMenuGroup>
        <DropdownMenuItem>
          <div class="flex items-center !gap-1" @click="auth">
            <Spinner v-if="globalStore.userSpotifyTokenValidity === null" size="6" />
            <template v-else-if="globalStore.isUserSpotifyTokenValid">
              <Check />
              Logged in with Spotify
            </template>
            <template v-else>
              <Key />
              Log in through Spotify
            </template>
          </div>
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
</template>
