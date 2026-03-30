import { createWebHistory, createRouter } from 'vue-router'

import Home from './pages/home/Home.vue'
import Game from './pages/game/Game.vue'
import GameLost from './pages/game/Lost.vue'
import CardHome from './pages/cards/Home.vue'
import CardPreviousExports from './pages/cards/PreviousExports.vue'
import CardSelect from './pages/cards/Select.vue'
import CardVerify from './pages/cards/Verify.vue'
import CardPreview from './pages/cards/Preview.vue'
import Error404 from "@/pages/error/Error404.vue";

const routes = [
    { path: '/', component: Home },

    { path: '/game', component: Game },
    { path: '/game/lost', component: GameLost },

    { path: '/cards', component: CardHome },
    { path: '/cards/previous-exports', component: CardPreviousExports },
    { path: '/cards/select', component: CardSelect },
    { path: '/cards/verify', component: CardVerify },
    { path: '/cards/preview/:uuid', component: CardPreview },

    { path: '/:pathMatch(.*)*', component: Error404 },
]

export const router = createRouter({
    history: createWebHistory(),
    routes,
})