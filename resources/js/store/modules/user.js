import notifier from '../../notifier'
import handleMessage from "../../handleMessage";

const state = {
    user: null,
}

const mutations = {
    /* User */
    setUser (state, payload) {
        state.user = payload
    },
}

const actions = {
    async getUser ({ commit }) {
        return await axios.get('/user')
        .then(({data}) => {
            commit('setUser', data.data)
        })
        .catch(({message}) => {
            notifier('Getting auth user : ' + handleMessage(message), 'danger')
        })
    },
}

const getters = {
    /* User */
    authUser: (state) => {
        return state.user
    },
    myShop: (state) => {
        return state.user ? state.user.shop : null
    },
    hasPermission: (state) => (perm) => {
        if (state.user) {
            for (const permission of state.user.permissions) {
                if (permission == perm) {
                    return true
                }
            }
        }
        return false
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}