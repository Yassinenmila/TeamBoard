import { defineStore } from "pinia"
import api from "@/services/api"

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    token: localStorage.getItem("token") || null,
    isAuthenticated: false
  }),

  actions: {

    // LOGIN
    async login(email, password) {
      const res = await api.post("/login", {
        email,
        password
      })

      this.user = res.data.user
      this.token = res.data.token
      this.isAuthenticated = true

      localStorage.setItem("token", this.token)
    },

    // LOGOUT
    logout() {
      this.user = null
      this.token = null
      this.isAuthenticated = false

      localStorage.removeItem("token")
    }
  }
})
