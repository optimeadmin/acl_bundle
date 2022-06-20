import {useImmer} from "use-immer";
import {useCallback} from "react";
import useConfigQuery from "./useConfigQuery";
import {useMutation} from "react-query";
import {saveResourcesRoles} from "../api/config";

const useConfig = () => {
    const [resources, setResources] = useImmer({})
    const {isLoading, roles, resources: savedResources} = useConfigQuery(setResources)
    const {mutate} = useMutation(saveResourcesRoles)

    const editResource = useCallback((name, roles) => {
        setResources(items => {
            if (!name in items) {
                return;
            }

            items[name].roles = roles
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
