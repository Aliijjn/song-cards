<script setup lang="ts">
import {
  NavigationMenu,
  NavigationMenuContent,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  NavigationMenuTrigger,
} from '@/components/ui/navigation-menu'
import { useColorMode } from "@vueuse/core";

const mode = useColorMode()
</script>

<template>
  <NavigationMenu>
    <NavigationMenuList>
      <NavigationMenuItem>
        <NavigationMenuTrigger>Theme</NavigationMenuTrigger>
        <NavigationMenuContent>
          <NavigationMenuLink @click="mode = 'light'">
            Light
          </NavigationMenuLink>
          <NavigationMenuLink @click="mode = 'dark'">
            Dark
          </NavigationMenuLink>
          <NavigationMenuLink @click="mode = 'auto'">
            System
          </NavigationMenuLink>
        </NavigationMenuContent>
      </NavigationMenuItem>
    </NavigationMenuList>
  </NavigationMenu>
</template>

