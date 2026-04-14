import { defineStore } from "pinia"
import api from "@/services/api"

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    token: localStorage.getItem("token") || null
  }),

  getters: {
    isAuthenticated: (state) => !!state.token
  },

  actions: {

    async login(email:string, password:string) {
      const res = await api.post("/login", {
        email,
        password
      })

      this.user = res.data.user
      this.token = res.data.token

      localStorage.setItem("token", this.token)
    },

    logout() {
      this.user = null
      this.token = null

      localStorage.removeItem("token")
    }
  }
})
