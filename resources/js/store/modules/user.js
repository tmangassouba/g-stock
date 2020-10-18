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
    hasRole: (state) => (role) => {
        if (state.user) {
            for (const _role of state.user.roles) {
                if (_role.name == role) {
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