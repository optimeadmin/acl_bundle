import { useQuery } from 'react-query'
import { getResources } from '../api/endpoints'
import { v4 as uuid } from 'uuid'
import { useEffect } from 'react'

export const createItem = (item) => {
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

const useResourcesQuery = (setResources) => {
  const {
    isLoading,
    isFetching,
    data: resources = [],
    dataUpdatedAt
  } = useQuery(['resources'], getResources, {
    keepPreviousData: true
  })

  useEffect(() => {
    if (dataUpdatedAt === 0) {
      return
    }

    setResources(resources.map(item => createItem(item)))
  }, [resources, dataUpdatedAt])

  return {
    isLoading,
    isFetching,
    resources,
  }
}

export default useResourcesQuery
