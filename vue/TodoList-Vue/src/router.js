import { createRouter, createWebHistory } from 'vue-router';
import Login from './components/Login.vue';
import TodoApp from './components/TodoApp.vue';

const routes = [
  {
    path: '/',
    name: 'Login',
    component: Login
  },
  {
    path: '/todo',
    name: 'TodoApp',
    component: TodoApp
  }
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
});

export default router;
