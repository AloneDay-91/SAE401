import { createStore } from 'vuex'
import AuthService from '@/services/auth.service.js'
import { jwtDecode } from 'jwt-decode'

const store = createStore({
    state: {
        user: AuthService.getToken() ? jwtDecode(AuthService.getToken()) : null, // Décode immédiatement si le token existe
        token: AuthService.getToken(),
    },
    mutations: {
        setUser(state, user) {
            state.user = user
        },
        setToken(state, token) {
            state.token = token
            if (token) {
                try {
                    const decodedToken = jwtDecode(token)
                    state.user = {
                        id: decodedToken.id,
                        nom: decodedToken.nom,
                        prenom: decodedToken.prenom,
                    }
                } catch (error) {
                    console.error("Erreur lors du décodage du token :", error)
                    state.user = null
                }
            } else {
                state.user = null
            }
        },
        logout(state) {
            state.user = null
            state.token = null
        }
    },
    actions: {
        async login({ commit }, { email, password }) {
            try {
                const response = await AuthService.login(email, password)
                commit('setToken', response.token) // Décode et stocke automatiquement
            } catch (error) {
                throw error
            }
        },
        logout({ commit }) {
            AuthService.logout()
            commit('logout')
        },
        async fetchUser({ commit, state }) {
            if (state.user && state.user.id) {
                try {
                    const user = await AuthService.getUser(state.user.id) // Doit exister dans `auth.service.js`
                    commit('setUser', user)
                } catch (error) {
                    console.error("Erreur lors de la récupération de l'utilisateur:", error)
                }
            }
        },

        async requestPasswordReset({ }, { email }) {
            try {
                await AuthService.requestPasswordReset(email)
            } catch (error) {
                console.error("Erreur lors de la demande de réinitialisation:", error)
                throw error
            }
        },
        async resetPassword({ }, { token, newPassword }) {
            try {
                await AuthService.resetPassword(token, newPassword)
            } catch (error) {
                console.error("Erreur lors de la réinitialisation du mot de passe:", error)
                throw error
            }
        },

        async register({ commit }, userData) {
            try {
                await AuthService.register(userData);
            } catch (error) {
                console.error("Erreur lors de l'inscription:", error);
                throw error;
            }
        },

        checkTokenExpiration({ commit, dispatch }) {
            const token = localStorage.getItem('token')
            if (token) {
                if (AuthService.isTokenExpired()) {
                    dispatch('logout')
                    router.push('/connexion')
                } else {
                    // Si le token est valide, assurez-vous que le state est à jour
                    commit('setToken', token)
                }
            }
        },
    },
    getters: {
        isAuthenticated: (state) => !!state.token,
        getUser: (state) => state.user
    }
})

export default store
