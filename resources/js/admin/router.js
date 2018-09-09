import Vue from 'vue';
import Router from 'vue-router';
import store from '../common/Store';

Vue.use(Router);

const router = new Router({
    routes: [
        {
            path: '/',
            redirect: '/dashboard',
        },
        {
            name: 'dashboard',
            path: '/dashboard',
            component: require('./dashboard/Home'),
        },
        {
            path: '/users',
            component: require('./users/Users'),
            children: [
                {
                    path:'/',
                    name:'users.list',
                    component: require('./users/components/UserLists')
                },
                {
                    path:'create',
                    name:'users.create',
                    component: require('./users/components/UserFormAdd')
                },
                {
                    path:'edit/:id',
                    name:'users.edit',
                    component: require('./users/components/UserFormEdit'),
                    props: (route) => ({propUserId: route.params.id}),
                },
                {
                    path:'groups',
                    name:'users.groups.list',
                    component: require('./users/components/GroupLists')
                },
                {
                    path:'groups/create',
                    name:'users.groups.create',
                    component: require('./users/components/GroupFromAdd')
                },
                {
                    path:'groups/edit/:id',
                    name:'users.groups.edit',
                    component: require('./users/components/GroupFromEdit'),
                    props: (route) => ({propGroupId: route.params.id}),
                },
                {
                    path:'permissions',
                    name:'users.permissions.list',
                    component: require('./users/components/PermissionLists')
                },
                {
                    path:'permissions/create',
                    name:'users.permissions.create',
                    component: require('./users/components/PermissionFormAdd')
                },
                {
                    path:'permissions/edit/:id',
                    name:'users.permissions.edit',
                    component: require('./users/components/PermissionFormEdit'),
                    props: (route) => ({propPermissionId: route.params.id}),
                },
            ]
        },
        {
            name: 'files',
            path: '/files',
            component: require('./files/Files'),
        },
        {
            name: 'settings',
            path: '/settings',
            component: require('./settings/Settings'),
        }
    ],
});

router.beforeEach((to, from, next) => {
    store.commit('showLoader');
    next();
});

router.afterEach((to, from) => {
    setTimeout(()=>{
        store.commit('hideLoader');
    },1000);
});

export default router;