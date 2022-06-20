import {useQuery} from "react-query";
import {getConfig} from "../api/config";

const useConfigQuery = (setResources) => {
    const {
        isLoading,
        isFetching,
        data: {
            roles = [],
            resources = {},
        } = {}
    } = useQuery(["config"], getConfig, {
        select({roles, resources}) {
            const mappedRoles = roles.map(role => ({
                ...role,
                parentRoles: Object.values(role.parentRoles),
            }))

            return {
                roles: mappedRoles,
                resources,
            }
        },
        onSuccess({resources: currentResources}) {
            const resources = {};

            for (const name in currentResources) {
                const {parent, children, resource: {roles}} = currentResources[name]

                resources[name] = {
                    name,
                    parent,
                    children,
                    roles,
                    initialRoles: roles,
                    blockedRoles: [],
                }
            }

            setResources(resources)
        }
    })

    return {
        isLoading,
        isFetching,
        roles,
        resources,
    }
}

export default useConfigQuery;
