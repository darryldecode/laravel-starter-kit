import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

export default new Router({
    routes: [
        {
            path: '/',
            redirect: '/dashboard',
        },
        {
            name: 'dashboard',
            path: '/dashboard',
            component: require('../pages/Home.vue'),
        },
        {
            name: 'users',
            path: '/users',
            component: require('../pages/Users.vue'),
        },
        {
            name: 'files',
            path: '/files',
            component: require('../pages/Files.vue'),
        },
        {
            name: 'settings',
            path: '/settings',
            component: require('../pages/Settings.vue'),
        }
    ],
})