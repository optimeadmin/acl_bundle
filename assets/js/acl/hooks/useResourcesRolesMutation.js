import React, {useCallback} from 'react';
import {useMutation, useQueryClient} from "react-query";
import {saveResourcesRoles} from "../api/endpoints";

const useResourcesRolesMutation = (resources) => {
    const queryClient = useQueryClient()

    const {mutateAsync, isLoading: isSaving} = useMutation(saveResourcesRoles, {
        onSuccess() {
            queryClient.invalidateQueries(["config"])
        }
    })

    const saveConfig = useCallback(async () => {
        await mutateAsync(resources)
    }, [mutateAsync, resources])

    return {
        isSaving,
        saveConfig,
    }
};

export default useResourcesRolesMutation;
