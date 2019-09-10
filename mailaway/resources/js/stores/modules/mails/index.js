import api from '../../../services/api/MailService';

const state = {
    dict: {},
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
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
