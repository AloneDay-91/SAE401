import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import store from './store/index.js'
import router from './router/index.js'
import { onMounted } from 'vue'

const app = createApp(App)

onMounted(async () => {
    const isTokenExpired = await store.dispatch('checkTokenExpiration');
    if (isTokenExpired) {
        await store.dispatch('logout');
        router.push('/connexion');
    }
});

app.use(store)
app.use(router)

app.mount('#app')
