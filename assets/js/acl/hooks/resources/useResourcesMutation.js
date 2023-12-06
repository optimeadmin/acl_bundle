import { useCallback } from 'react'
import { useMutation, useQueryClient } from '@tanstack/react-query'
import { saveResources as saveResourcesApi } from '../../api/endpoints'

const useSaveResources = resources => {
  const queryClient = useQueryClient()

  const { isLoading: isSaving, mutateAsync } = useMutation(saveResourcesApi, {
    onSuccess () {
      queryClient.invalidateQueries(['resources'])
      queryClient.invalidateQueries(['config'])
    }
  })

  const saveResources = useCallback(
    async () => {
      const selectedResources = resources.filter(resource => resource.selected)

      if (selectedResources.length === 0) {
        return
      }

      return await mutateAsync(selectedResources)
    },
    [resources, mutateAsync]
  )

  return {
    isSaving,
    saveResources
  }
}

export default useSaveResources
