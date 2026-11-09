import '../css/app.css';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import { useAuthStore } from '@/stores/auth';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

const auth = useAuthStore(pinia);

window.addEventListener('haat:unauthorized', () => {
    const wasAdmin = auth.isAdmin;
    auth.clear();
    router.push(wasAdmin ? '/admin/login' : '/login');
});

app.mount('#app');
