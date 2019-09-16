import api from '../../../services/api/MailService';

const state = {
    dict: {},
    mails: [],
    activeMail: null,
    isLoadingMail: false,
    loadMailError: false,
    draft: {
        errors: [],
        errorMessage: "",
        result: "",
        isLoading: false,
    }
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
    },
    draft: (state, getters) => {
        return state.draft;
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
                commit('setLoadMailError', true);
            }).finally(() => {
                commit('setIsLoadingMail', false);
            });
    },
    setActiveMail: ({ commit, state }, { id }) => {
        commit('setActiveMail', id);
    },
    sendDraft: ({ commit, dispatch, state }, { draft }) => {
        commit('setDraftState', {
            errors: [],
            errorMessage: '',
            result: '',
            isLoading: true,
        });
        api.sendMail(
            draft.title,
            draft.fromName,
            draft.fromEmail,
            draft.to,
            draft.body_txt,
            draft.body_html
        )
            .then(mail => {
                if (mail && mail.status && mail.id) {
                    dispatch("getMail", { id: mail.id });
                    commit('setDraftState', {
                        errors: [],
                        errorMessage: '',
                        result: 'Created Mail',
                        isLoading: false,
                    });
                }
            })
            .catch(error => {
                if (error.response && error.response.status == 422) {
                    commit('setDraftState', {
                        errors: error.response.data.errors,
                        errorMessage: error.response.data.message,
                        result: '',
                        isLoading: false,
                    });
                    /**
                     * @todo cover errors for single emails
                     * errors: {to.0: ["The to.0 must be a valid email address."]}
                     */
                } else {
                    console.log('Unexpected error: ', error);
                }
            });
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
    },
    setIsLoadingMail: (state, value) => {
        state.isLoadingMail = value;
    },
    setLoadMailError: (state, value) => {
        state.loadMailError = value;
    },
    setDraftState: (state, value) => {
        state.draft = value;
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
