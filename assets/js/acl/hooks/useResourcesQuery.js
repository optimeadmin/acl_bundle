import { useQuery } from 'react-query'
import { getResources } from '../api/endpoints'
import { v4 as uuid } from 'uuid'

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
        valid: !!item?.id,
    }
}

const useResourcesQuery = (setResources) => {
    const {
        isLoading,
        isFetching,
        data: resources = [],
    } = useQuery(['resources'], getResources, {
        keepPreviousData: true,
        onSuccess (resources) {
            setResources(resources.map(item => createItem(item)))
        }
    })

    return {
        isLoading,
        isFetching,
        resources,
    }
}

export default useResourcesQuery
