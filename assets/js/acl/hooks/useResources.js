import React, { useCallback, useEffect, useState } from 'react'
import useResourcesQuery, { createItem } from './useResourcesQuery'
import { useImmer } from 'use-immer'
import useResourcesMutation from './useResourcesMutation'

const useResources = () => {
    const [resources, setResources] = useImmer([])
    const { isLoading, isFetching, resources: dbResources } = useResourcesQuery(setResources)
    const { saveResources, isSaving } = useResourcesMutation(resources)
    const [selectedCount, setSelectedCount] = useState(0)

    const updateResource = useCallback((key, data) => {
        setResources(resources => {
            const index = resources.findIndex(item => item.key === key)

            if (-1 === index) {
                return
            }

            resources[index] = { ...resources[index], ...data }
            resources[index].valid = true

            if (resources[index].name.length < 3) {
                resources[index].selected = false
                resources[index].valid = false
            }
        })
    }, [setResources, dbResources])

    const addResource = useCallback(() => {
        setResources(resources => {
            resources.unshift(createItem())
        })
    }, [setResources, dbResources])

    useEffect(() => {
        setSelectedCount(resources.filter(r => r.selected).length)
    }, [resources])

    return {
        isLoading,
        isFetching,
        isSaving,
        resources,
        selectedCount,
        updateResource,
        addResource,
        saveResources,
    }
}

export default useResources
