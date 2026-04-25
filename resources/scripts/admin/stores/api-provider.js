import { defineStore } from 'pinia'
import axios from 'axios'

export const useApiProviderStore = defineStore('apiProvider', {
  state: () => ({
    providers: [],
    provider: {},
  }),
  actions: {
    fetchProviders(params) {
      return new Promise((resolve, reject) => {
        axios
          .get('/api/v1/api-providers', { params })
          .then((response) => {
            this.providers = response.data.data
            resolve(response)
          })
          .catch((err) => {
            reject(err)
          })
      })
    },
    fetchProvider(id) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/api/v1/api-providers/${id}`)
          .then((response) => {
            this.provider = response.data.data
            resolve(response)
          })
          .catch((err) => {
            reject(err)
          })
      })
    },
    addProvider(data) {
      return new Promise((resolve, reject) => {
        axios
          .post('/api/v1/api-providers', data)
          .then((response) => {
            resolve(response)
          })
          .catch((err) => {
            reject(err)
          })
      })
    },
    updateProvider(data) {
      return new Promise((resolve, reject) => {
        axios
          .put(`/api/v1/api-providers/${data.id}`, data)
          .then((response) => {
            resolve(response)
          })
          .catch((err) => {
            reject(err)
          })
      })
    },
    deleteProvider(id) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/api/v1/api-providers/${id}`)
          .then((response) => {
            resolve(response)
          })
          .catch((err) => {
            reject(err)
          })
      })
    },
    testProvider(data) {
      return new Promise((resolve, reject) => {
        axios
          .post('/api/v1/api-providers/test', data)
          .then((response) => {
            resolve(response)
          })
          .catch((err) => {
            reject(err)
          })
      })
    },
  },
})
