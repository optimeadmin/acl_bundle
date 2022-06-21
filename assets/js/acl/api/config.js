import axios from "axios";
import {endpointApi} from "../index"

const api = axios.create({})

api.interceptors.request.use(function (config) {
    return {
        baseURL: endpointApi,
        ...config
    }
});

export const getConfig = () => {
    return api.get("/config/").then(({data}) => data)
}

export const saveResourcesRoles = (resourcesRoles) => {
    return api.put("/resources-roles/", resourcesRoles).then(({data}) => data)
}

export const cleanUnusedResources = () => {
    return api.put("/resources/clean/").then(({data}) => data)
}