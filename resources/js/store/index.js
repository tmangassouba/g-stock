import Vue from 'vue'
import Vuex from 'vuex'
import user from './modules/user'
import menu from './modules/menu'
import parametres from './modules/parametres'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
    modules: {
        user,
        menu,
        parametres
    },
    strict: debug,
    // plugins: debug ? [createLogger()] : []
})