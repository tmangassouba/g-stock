require('./bootstrap');

import Vue from 'vue';

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';
import Buefy from 'buefy';
// import 'buefy/dist/buefy.css';
// import Vuex from 'vuex'
import 'es6-promise/auto'
import store from './store'
import handleMessage from "./handleMessage.js";

Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);
Vue.use(Buefy);
// Vue.use(Vuex)

/**
 * VueFilter
 */
import Vue2Filters from 'vue2-filters'
Vue.use(Vue2Filters)

Vue.prototype.$OPERATION_ENTREE = "ENTREE"
Vue.prototype.$OPERATION_SORTIE = "SORTIE"
Vue.prototype.$OPERATION_TRANSFERT = "TRANSFERT"

// Vue.prototype.$route = (...args) => route(...args).url()
Vue.prototype.$handleMessage = function (message, type) {
    let mess = handleMessage(message)
    if (mess) {
        this.$buefy.notification.open({
            duration: 10000,
            message: mess,
            type: `is-${type}`,
            hasIcon: true,
            position: 'is-bottom-right',
        })
    }
}

const app = document.getElementById('app');

new Vue({
    store,
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);
