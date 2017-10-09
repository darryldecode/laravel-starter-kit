import Vue from 'vue';

const EventBus = new Vue();

export default {

    install(Vue, options) {

        Vue.prototype.$eventBus = EventBus;
    }
}