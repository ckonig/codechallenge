import api from '../../../services/api/MailService';

const state = {
    dict: {},
    mails: [],
    activeMail: null,
    isLoadingMail: false,
    loadMailError: false,
}

const getters = {
    getMails: (state, getters) => {
        return state.mails;
    },
    getActiveMail: (state, getters) => {
        return state.activeMail && state.dict[state.activeMail];
    },
    isLoadingMail: (state, getters) => {
        return state.isLoadingMail;
    },
    hasLoadMailError: (state, getters) => {
        return state.loadMailError;
    }
}

const actions = {
    getMail: ({ commit, state }, { id }) => {
        commit('setIsLoadingMail', true);
        commit('setLoadMailError', false);
        api.getMail(id)
            .then(mail => {
                commit('addMail', mail);
            })
            .catch(() => {
                console.log('caught in store');
                commit('setLoadMailError', true);
            }).finally(() => {
                commit('setIsLoadingMail', false);
            });
    },
    setActiveMail: ({ commit, state }, { id }) => {
        commit('setActiveMail', id);
    }
}

const mutations = {
    addMail: (state, mail) => {
        if (mail && mail.id) {
            state.dict[mail.id] = mail;
        }
        state.mails = Object.values(state.dict).reverse();
    },
    setActiveMail: (state, id) => {
        state.activeMail = id;
        console.log('active mail is now ' + id)
    },
    setIsLoadingMail: (state, value) => {
        state.isLoadingMail = value;
    },
    setLoadMailError: (state, value) => {
        state.loadMailError = value;
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
