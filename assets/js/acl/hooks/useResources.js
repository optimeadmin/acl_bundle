import { useCallback } from 'react'
import useResourcesQuery, { createItem } from './useResourcesQuery'
import { useImmer } from 'use-immer'
import useResourcesMutation from './useResourcesMutation'

const useResources = () => {
  const [resources, setResources] = useImmer([])
  const { isLoading, isFetching, resources: dbResources } = useResourcesQuery(setResources)
  const { saveResources, isSaving } = useResourcesMutation(resources)
  const selectedCount = resources.filter(r => r.selected).length

  const updateResource = useCallback((key, data) => {
    setResources(resources => {
      const index = resources.findIndex(item => item.key === key)

      if (index === -1) {
        return
      }

      resources[index] = { ...resources[index], ...data }
      resources[index].valid = true

      if (resources[index].name.length < 3) {
        resources[index].selected = false
        resources[index].valid = false
      }
    })
  }, [setResources])

  const addResource = useCallback(() => {
    setResources(resources => {
      resources.unshift(createItem())
    })
  }, [setResources, dbResources])

  return {
    isLoading,
    isFetching,
    isSaving,
    resources,
    selectedCount,
    updateResource,
    addResource,
    saveResources
  }
}

export default useResources
