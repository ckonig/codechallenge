import api from '../../../services/api/MailService';

const state = {
    mails: [],
    activeMail: '',
    isLoadingMail: false,
}

const getters = {
    getMails: (state, getters) => {
        return state.mails;
    },
    getActiveMail: (state, getters) => {
        return state.activeMail;
    },
    isLoadingMail: (state, getters) => {
        return state.isLoadingMail;
    }
}

const actions = {
    addMail: ({ commit, state }, { id }) => {
        commit('setIsLoadingMail', true);
        api.getMail(id).then(mail => {
            commit('addMail', mail);
            commit('setActiveMail', id);
            commit('setIsLoadingMail', false);
        })
    }
}

const mutations = {
    addMail: (state, mail) => {
        state.mails.unshift(mail);
    },
    setActiveMail: (state, id) => {
        state.activeMail = id;
    },
    setIsLoadingMail: (state, value) => {
        state.isLoadingMail = value;
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
