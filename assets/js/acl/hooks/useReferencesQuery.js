import React, { useEffect } from 'react'
import { useQuery } from 'react-query'
import { getReferences } from '../api/endpoints'

const useReferencesQuery = (type, setReferences) => {
    const {
        isLoading,
        isFetching,
        data: references = [],
        dataUpdatedAt,
    } = useQuery(['references'], getReferences, {
        keepPreviousData: true,
        select (data) {
            return type in data ? data[type] : data['news']
        },
    })

    useEffect(() => {
        if (0 === dataUpdatedAt || isFetching) {
            return
        }

        setReferences(
            references
                .filter(item => item.active
                    || '__HIDDEN__' !== item.resource
                    || null !== item.route
                )
                .map(item => ({
                    ...item,
                    selected: false,
                    hidden: false,
                    initialResource: item.resource,
                }))
        )
    }, [dataUpdatedAt, isFetching])

    return {
        isLoading,
        isFetching,
        references,
    }
}

export default useReferencesQuery
