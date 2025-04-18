import axios from "axios";
import { jwtDecode } from "jwt-decode"; // Import correct

const API_URL = import.meta.env.VITE_API_BASE_URL;

class AuthService {
    async login(email, password) {
        try {
            const response = await axios.post(
                `${API_URL}/auth`,
                { email, password },
                { headers: { "Content-Type": "application/ld+json" } }
            );

            if (response.data.token) {
                const token = response.data.token;
                localStorage.setItem("token", token);
                axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

                // Décoder le token pour récupérer les infos utilisateur
                const decodedUser = jwtDecode(token);

                console.log("decodedUser", decodedUser);

                // Vérifier que les champs nécessaires sont bien présents
                const user = {
                    id: decodedUser.id || null,
                    nom: decodedUser.nom || "Inconnu",
                    prenom: decodedUser.prenom || "Inconnu",
                    email: decodedUser.username || "Inconnu",
                    roles: decodedUser.roles || [],
                    tp: decodedUser.tp || null,
                    td: decodedUser.td || null,
                    promo: decodedUser.promo || null,
                };

                /*// Stocker les infos de l'utilisateur dans localStorage
                localStorage.setItem("user", JSON.stringify(user));*/
                localStorage.setItem("roles", JSON.stringify(user.roles));
                localStorage.setItem("email", JSON.stringify(user.email));

                return { token, user };
            }
        } catch (error) {
            throw new Error(error.response?.data?.message || "Erreur de connexion");
        }
        console.log("Erreur lors de la connexion :", error);
    }

    async requestPasswordReset(email) {
        try {
            const response = await axios.post(`${API_URL}/password-reset`, { email }, {
                headers: { "Content-Type": "application/ld+json" }
            })
            return response.data
        } catch (error) {
            console.error("Erreur lors de la demande de réinitialisation:", error)
            throw error
        }
    }

    async resetPassword(token, newPassword) {
        try {
            const response = await axios.post(`${API_URL}/password-reset/confirm`, { token, newPassword }, {
                headers: { "Content-Type": "application/ld+json" }
            })
            return response.data
        } catch (error) {
            console.error("Erreur lors de la réinitialisation du mot de passe:", error)
            throw error
        }
    }

    logout() {
        localStorage.removeItem("token");
        localStorage.removeItem("user");
        localStorage.removeItem("roles");
        delete axios.defaults.headers.common["Authorization"];
    }

    getToken() {
        return localStorage.getItem("token");
    }

    async getUser(id) {
        try {
            const response = await axios.get(`${API_URL}/users/${id}`, {
                headers: { Authorization: `Bearer ${this.getToken()}` }
            })
            return response.data
        } catch (error) {
            console.error("Erreur lors de la récupération de l'utilisateur :", error)
            throw error
        }
    }

    async register({ email, roles, password, nom, prenom, roleapp, td, tp, promo }) {
        try {
            const response = await axios.post(
                `${API_URL}/users`,
                { email, roles, password, nom, prenom, roleapp, td, tp, promo },
                {
                    headers: { "Content-Type": "application/ld+json" }
                }
            );
            return response.data;
        } catch (error) {
            console.error("Erreur lors de l'inscription :", error.response?.data || error);
            throw error;
        }
    }


    setupAxiosInterceptors() {
        const token = this.getToken();
        if (token) {
            axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
            return true;
        }
        return false;
    }

    isTokenValid() {
        const token = this.getToken();
        if (!token) return false;

        try {
            const decoded = jwtDecode(token);
            const isValid = decoded.exp * 1000 > Date.now();
            if (!isValid) {
                this.logout();
            }
            return isValid;
        } catch {
            return false;
        }
    }

    isTokenExpired() {
        const token = localStorage.getItem('token')
        if (!token) return true

        try {
            const decoded = jwtDecode(token)
            return decoded.exp * 1000 < Date.now()
        } catch (error) {
            console.error("Erreur lors de la vérification de l'expiration du token:", error)
            return true
        }
    }

}

export default new AuthService();