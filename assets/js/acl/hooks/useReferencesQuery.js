import React from 'react'
import { useQuery } from 'react-query'
import { getReferences } from '../api/endpoints'

const useReferencesQuery = (type, setReferences) => {
    const {
        isLoading,
        isFetching,
        data: references = [],
    } = useQuery(['references'], getReferences, {
        keepPreviousData: true,
        select (data) {
            return type in data ? data[type] : data['news']
        },
        onSuccess (data) {
            setReferences(
                data
                    .filter(item => item.active
                        || '__HIDDEN__' !== item.resource
                        || null !== item.route
                    )
                    .map(item => ({
                        ...item,
                        selected: false,
                        initialResource: item.resource,
                    }))
            )
        }
    })

    return {
        isLoading,
        isFetching,
        references,
    }
}

export default useReferencesQuery
