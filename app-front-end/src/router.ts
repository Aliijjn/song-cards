import { createWebHistory, createRouter } from 'vue-router'

import Home from './pages/home/Home.vue'
import Game from './pages/game/Game.vue'
import GameLost from './pages/game/Lost.vue'
import CardHome from './pages/cards/Home.vue'
import CardSelect from './pages/cards/Select.vue'
import CardVerify from './pages/cards/Verify.vue'
import CardCreate from './pages/cards/Create.vue'

const routes = [
    { path: '/', component: Home },

    { path: '/game', component: Game },
    { path: '/game/lost', component: GameLost },

    { path: '/cards', component: CardHome },
    { path: '/cards/select', component: CardSelect },
    { path: '/cards/verify', component: CardVerify },
    { path: '/cards/create', component: CardCreate },
]

export const router = createRouter({
    history: createWebHistory(),
    routes,
})