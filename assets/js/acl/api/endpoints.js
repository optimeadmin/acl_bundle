import axios from 'axios'
import { endpointApi } from '../index'

const api = axios.create({})

api.interceptors.request.use(function (config) {
  return {
    baseURL: endpointApi,
    ...config
  }
})

export const getConfig = () => {
  return api.get('/config/').then(({ data }) => data)
}

export const saveResourcesRoles = (resourcesRoles) => {
  return api.put('/resources-roles/', resourcesRoles).then(({ data }) => data)
}

export const cleanUnusedResources = () => {
  return api.delete('/resources/clean/').then(({ data }) => data)
}

export const getResources = (signal) => {
  return api.get('/resources/', { signal }).then(({ data }) => data)
}

export const saveResources = (resources) => {
  return api.put('/resources/', resources).then(({ data }) => data)
}

export const getReferences = () => {
  return api.get('/references/').then(({ data }) => data)
}

export const saveReferences = (references) => {
  return api.post('/references/', references).then(({ data }) => data)
}
