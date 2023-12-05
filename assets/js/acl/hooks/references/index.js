import { useState } from 'react'
import { useMutation, useQuery, useQueryClient } from 'react-query'
import { getReferences, saveReferences as saveReferencesApi } from '../../api/endpoints'

const emptyReferences = []

export function useReferencesQuery (type) {
  const { isLoading, isFetching, data: references = emptyReferences } = useQuery({
    queryKey: ['references'],
    queryFn: getReferences,
    keepPreviousData: true,
    select (data) {
      return type in data ? data[type] : data.news
    }
  })

  return {
    isLoading,
    isFetching,
    references,
    length: references.length,
  }
}

export function useReferences (databaseReferences) {
  const [references, setReferences] = useState(null)
  const [prevReferences, setPrevReferences] = useState(null)

  if (prevReferences !== databaseReferences) {
    setPrevReferences(databaseReferences)
    setReferences(
      databaseReferences
        .filter(item => item.active ||
          item.resource !== '__HIDDEN__' ||
          item.route !== null
        )
        .map(item => ({
          ...item,
          selected: false,
          hidden: false,
          initialResource: item.resource,
        }))
    )
  }

  function updateReference (identifier, data) {
    setReferences(references => {
      const newReferencs = [...(references ?? databaseReferences)]
      const index = newReferencs.findIndex(item => item.identifier === identifier)

      if (index === -1) {
        return references
      }

      newReferencs[index] = { ...newReferencs[index], ...data }

      return newReferencs
    })
  }

  return {
    references: (references ?? databaseReferences),
    count: (references ?? databaseReferences).length,
    updateReference,
  }
}

export function useSaveReferences () {
  const queryClient = useQueryClient()

  const { isLoading: isSaving, mutateAsync } = useMutation({
    mutationFn: saveReferencesApi,
    mutationKey: ['references'],
    async onSuccess () {
      await Promise.all([
        queryClient.invalidateQueries(['resources']),
        queryClient.invalidateQueries(['config']),
        queryClient.invalidateQueries(['references']),
      ])
    }
  })

  async function saveReferences (references) {
    const selectedReferences = references.filter(
      item => item.selected || item.hidden
    )

    if (selectedReferences.length === 0) {
      return
    }

    return await mutateAsync(selectedReferences)
  }

  return {
    isSaving,
    saveReferences
  }
}
