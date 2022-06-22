import React, { useCallback } from 'react'
import { useMutation, useQueryClient } from 'react-query'
import { saveReferences as saveReferencesApi } from '../api/endpoints'

const useReferencesMutation = (references) => {
    const queryClient = useQueryClient()

    const { isLoading: isSaving, mutateAsync } = useMutation(saveReferencesApi, {
        onSuccess () {
            queryClient.invalidateQueries(['resources'])
            queryClient.invalidateQueries(['config'])
            queryClient.invalidateQueries(['references'])
        }
    })

    const saveReferences = useCallback(async () => {
        const selectedReferences = references.filter(
            item => item.selected || item.hidden,
        )

        if (selectedReferences.length === 0) {
            return
        }

        return await mutateAsync(selectedReferences)
    }, [references, mutateAsync])

    return {
        isSaving,
        saveReferences,
    }
}

export default useReferencesMutation
