import { useEffect, useState } from 'react'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { v4 as uuid } from 'uuid'
import { getResources, saveResources as saveResourcesApi } from '../../api/endpoints'

function createItem (item) {
  return {
    id: item?.id ?? null,
    key: uuid(),
    initialName: item?.name ?? '',
    initialDescription: item?.description ?? '',
    name: item?.name ?? '',
    description: item?.description ?? '',
    references: item?.references ?? [],
    createdByUser: item?.createdByUser ?? true,
    selected: false,
    valid: !!item?.id,
  }
}

const resourcesPlaceholder = []

export function useResourcesQuery () {
  const { isLoading, isFetching, data: resources = resourcesPlaceholder } = useQuery({
    queryKey: ['resources'],
    async queryFn ({ signal }) {
      const resources = await getResources(signal)

      return resources.map((item) => createItem(item))
    },
    keepPreviousData: true,
  })

  return {
    isLoading,
    isFetching,
    resources,
  }
}

export function useManageResources () {
  const [resources, setResources] = useState([])
  const { isLoading, isFetching, resources: dbResources } = useResourcesQuery()

  useEffect(() => {
    setResources(dbResources)
  }, [dbResources])

  const selectedCount = resources.filter((r) => r.selected).length

  function updateResource (key, data) {
    setResources((resources) => {
      const newResources = [...resources]

      const index = newResources.findIndex((item) => item.key === key)

      if (index === -1) {
        return newResources
      }

      const newItem = { ...newResources[index], ...data, valid: true }

      if (newItem.name.length < 3) {
        newItem.selected = false
        newItem.valid = false
      }

      newResources[index] = newItem

      return newResources
    })
  }

  function addResource () {
    setResources((resources) => {
      return [createItem(), ...resources]
    })
  }

  return {
    isLoading,
    isFetching,
    resources,
    selectedCount,
    updateResource,
    addResource,
  }
}

export function useSaveResources () {
  const queryClient = useQueryClient()

  const { isPending: isSaving, mutateAsync } = useMutation({
    mutationFn: saveResourcesApi,
    mutationKey: ['resources'],
    async onSuccess () {
      await Promise.all([
        queryClient.invalidateQueries(['resources']),
        queryClient.invalidateQueries(['config']),
      ])
    },
  })

  async function saveResources (resources) {
    const selectedResources = resources.filter((resource) => resource.selected)

    if (selectedResources.length === 0) {
      return
    }

    return await mutateAsync(selectedResources)
  }

  return {
    isSaving,
    saveResources,
  }
}
