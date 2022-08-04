import { useState } from 'react'
import { useQuery } from 'react-query'
import { getReferences } from '../api/endpoints'

const useReferencesQuery = (type, setReferences) => {
  const {
    isLoading,
    isFetching,
    data: references = [],
    dataUpdatedAt
  } = useQuery(['references'], getReferences, {
    keepPreviousData: true,
    select (data) {
      return type in data ? data[type] : data.news
    }
  })
  const [previousReferences, setPreviousReferences] = useState(null)

  if (dataUpdatedAt !== 0 && !isFetching && previousReferences !== references) {
    setPreviousReferences(references)
    setReferences(
      references
        .filter(item => item.active ||
          item.resource !== '__HIDDEN__' ||
          item.route !== null
        )
        .map(item => ({
          ...item,
          selected: false,
          hidden: false,
          initialResource: item.resource
        }))
    )
  }

  return {
    isLoading,
    isFetching,
    references
  }
}

export default useReferencesQuery
