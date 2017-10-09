
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// vendor
require('./bootstrap');
window.Vue = require('vue');

// 3rd party
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';

Vue.use(Vuetify);

Vue.component('moon-loader', require('vue-spinner/src/MoonLoader.vue'));

// app
import router from './router/';
import store from './store/';
import eventBus from './event/';
import formatters from './common/Formatters';

Vue.use(formatters);
Vue.use(eventBus);
Vue.component('example', require('./components/Example.vue'));

const admin = new Vue({
    el: '#admin',
    eventBus,
    router,
    store,
    data: () => ({
        drawer: true,
    }),
    computed: {
        showLoader() {
            return store.getters.showLoader;
        },
        showSnackbar: {
            get() {
                return store.getters.showSnackbar;
            },
            set(val) {
                if(!val) store.commit('hideSnackbar');
            }
        },
        snackbarMessage() {
            return store.getters.snackbarMessage;
        },
        snackbarColor() {
            return store.getters.snackbarColor;
        },
        snackbarDuration() {
            return store.getters.snackbarDuration;
        },

        // dialog
        showDialog: {
            get() {
                return store.getters.showDialog;
            },
            set(val) {
                if(!val) store.commit('hideDialog');
            }
        },
        dialogType() {
            return store.getters.dialogType
        },
        dialogTitle() {
            return store.getters.dialogTitle
        },
        dialogMessage() {
            return store.getters.dialogMessage
        }
    },
    methods: {
        menuClick(routeName, routeType) {

            let rn = routeType || 'vue';

            if(rn==='vue') {

                store.commit('showLoader');

                this.$router.push({name: routeName});
            }
        },
        dialogOk() {
            store.commit('dialogOk');
        },
        dialogCancel() {
            store.commit('dialogCancel');
        }
    }
});