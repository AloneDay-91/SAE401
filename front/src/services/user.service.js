import axios from "axios";
import AuthService from "./auth.service.js";

const API_URL = import.meta.env.VITE_API_BASE_URL;

class UserService {
    async getUser(id) {
        try {
            // Vérifie si l'ID est fourni
            if (!id) {
                throw new Error("L'ID de l'utilisateur est requis.");
            }

            console.log("ID utilisateur:", id);

            // Ajoute le token d'authentification
            const token = AuthService.getToken();
            const response = await axios.get(`${API_URL}/users/${id}`, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            });

            return response.data;
        } catch (error) {
            console.error("Erreur lors de la récupération de l'utilisateur:", error);

            // Gestion d'erreur améliorée
            throw new Error(error.response?.data?.message || "Impossible de récupérer l'utilisateur.");
        }
    }
}

export default new UserService();
