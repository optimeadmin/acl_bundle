import { useEffect } from 'react'
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

  useEffect(() => {
    if (dataUpdatedAt === 0 || isFetching) {
      return
    }

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
  }, [dataUpdatedAt, isFetching, references])

  return {
    isLoading,
    isFetching,
    references
  }
}

export default useReferencesQuery
