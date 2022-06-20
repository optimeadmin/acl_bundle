import {useQuery} from "react-query";
import {getConfig} from "../api/config";

const useConfigQuery = (setResources) => {
    const {
        isLoading,
        data: {
            roles = [],
            resources = {},
            rolesCount
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
                rolesCount: mappedRoles.length,
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
                }
            }

            setResources(resources)
        }
    })

    return {
        isLoading,
        roles,
        rolesCount,
        resources,
    }
}

export default useConfigQuery;
