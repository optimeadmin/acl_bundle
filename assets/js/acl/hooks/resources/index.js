import { useMutation, useQuery, useQueryClient } from 'react-query'
import { useImmer } from 'use-immer'
import { v4 as uuid } from 'uuid'
import { getResources, saveResources as saveResourcesApi  } from '../../api/endpoints'

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
    valid: !!item?.id
  }
}

function useResourcesQuery () {
  const { isLoading, isFetching, data: resources = [] } = useQuery({
    queryKey: ['resources'], 
    async queryFn(){
      const resources = await getResources()

      return resources.map(item => createItem(item))
    },
    keepPreviousData: true
  })

  return {
    isLoading,
    isFetching,
    resources,
  }
}

export function useManageResources() {
  const [resourcesFromState, setResources] = useImmer()
  const { isLoading, isFetching, resources: dbResources } = useResourcesQuery()
  
  const resources = resourcesFromState ?? dbResources
  console.log({  resources })
  const selectedCount = resources.filter(r => r.selected).length

  function updateResource(key, data) {
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
  }

  function addResource() {
    setResources(resources => (resources ?? dbResources).unshift(createItem()))
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


export function useSaveResources() {
  const queryClient = useQueryClient()

  const { isLoading: isSaving, mutateAsync } = useMutation({
    mutationFn: saveResourcesApi,
    onSuccess() {
      queryClient.invalidateQueries(['resources'])
      queryClient.invalidateQueries(['config'])
    }
  })

  async function saveResources(resources) {
    const selectedResources = resources.filter(resource => resource.selected)

    if (selectedResources.length === 0) {
      return
    }

    return await mutateAsync(selectedResources)
  }

  return {
    isSaving,
    saveResources
  }
}
