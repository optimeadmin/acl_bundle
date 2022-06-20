import {useImmer} from "use-immer";
import {useCallback} from "react";
import useConfigQuery from "./useConfigQuery";
import {useMutation} from "react-query";
import {saveResourcesRoles} from "../api/config";

const updateParentRoles = (items, resource) => {
    const {parent, roles} = resource

    if (!parent || !items[parent]) {
        return;
    }

    const parentResource = items[parent]
    parentResource.roles = [...new Set([...parentResource.roles, ...roles])]

    updateParentRoles(items, parentResource)
}

const updateChildrenRoles = (items, resource) => {
    const {roles} = resource
    const children = Object.values(resource.children ?? {})

    children.forEach(name => {
        const resource = items[name]

        if (!resource) {
            return;
        }

        resource.roles = resource.roles?.filter(role => roles.includes(role))

        updateChildrenRoles(items, resource)
    })
}

const useConfig = () => {
    const [resources, setResources] = useImmer({})
    const {isLoading, roles, resources: savedResources, rolesCount} = useConfigQuery(setResources)
    const {mutate} = useMutation(saveResourcesRoles)

    const editResource = useCallback((name, roles) => {
        setResources(items => {
            if (!name in items) {
                return;
            }

            items[name].roles = roles

            updateParentRoles(items, items[name])
            updateChildrenRoles(items, items[name])
        })
    }, [savedResources, setResources])

    const saveConfig = useCallback(() => {
        mutate(resources)
    }, [mutate, resources])

    return {
        isLoading,
        roles,
        resources,
        editResource,
        saveConfig,
    }
}

export default useConfig;
