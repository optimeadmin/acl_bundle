import React, { useCallback } from 'react'
import { useImmer } from 'use-immer'
import useReferencesQuery from './useReferencesQuery'
import useReferencesMutation from './useReferencesMutation'

const useReferences = (type) => {
    const [references, setReferences] = useImmer([])
    const { isLoading, isFetching, references: dbReferences } = useReferencesQuery(type, setReferences)
    const { saveReferences, isSaving } = useReferencesMutation(references)

    const updateReference = useCallback((identifier, data) => {
        setReferences(references => {
            const index = references.findIndex(item => item.identifier === identifier)

            if (-1 === index) {
                return
            }

            references[index] = { ...references[index], ...data }
        })
    }, [dbReferences, setReferences])

    return {
        isLoading,
        isFetching,
        isSaving,
        references,
        count: references.length,
        serverCount: dbReferences.length,
        updateReference,
        saveReferences,
    }
}

export default useReferences
