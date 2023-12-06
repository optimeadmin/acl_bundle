import { useQuery } from '@tanstack/react-query'
import { getResources } from '../../api/endpoints'
import { v4 as uuid } from 'uuid'
import { useState } from 'react'

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
  const [previousResources, setPreviousResources] = useState(null)

  if (dataUpdatedAt !== 0 && previousResources !== resources) {
    setPreviousResources(resources)
    setResources(resources.map(item => createItem(item)))
  }

  return {
    isLoading,
    isFetching,
    resources,
  }
}

export default useResourcesQuery
