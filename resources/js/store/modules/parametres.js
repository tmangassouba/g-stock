import notifier from '../../notifier'
import handleMessage from "../../handleMessage";

const state = {
    parametre: null,
}

const mutations = {
    setParametre (state, payload) {
        state.parametre = payload
    },
}

const actions = {
    async getEntreprise ({ commit }) {
        return await axios.get('/entreprise')
        .then(({data}) => {
            commit('setParametre', data.data)
        })
        .catch(({message}) => {
            notifier('Profil organisation : ' + handleMessage(message), 'danger')
        })
    },
}

const getters = {
    getParametre: (state) => {
        return state.parametre
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}