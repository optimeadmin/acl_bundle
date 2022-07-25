import { useQuery } from 'react-query'
import { getConfig } from '../api/endpoints'
import { useEffect } from 'react'

const useConfigQuery = (setResources) => {
  const {
    isLoading,
    isFetching,
    data: {
      roles = [],
      resources = {}
    } = {},
    dataUpdatedAt
  } = useQuery(['config'], getConfig, {
    keepPreviousData: true,
    select ({ roles, resources }) {
      const mappedRoles = roles.map(role => ({
        ...role,
        parentRoles: Object.values(role.parentRoles)
      }))

      return {
        roles: mappedRoles,
        resources
      }
    }
  })

  useEffect(() => {
    if (dataUpdatedAt === 0) {
      return
    }

    const resourcesData = {}

    for (const name in resources) {
      const { parent, children, resource: { roles, level } } = resources[name]

      resourcesData[name] = {
        name,
        parent,
        level,
        children,
        roles,
        initialRoles: roles,
        blockedRoles: []
      }
    }

    setResources(resourcesData)
  }, [resources, dataUpdatedAt])

  return {
    isLoading,
    isFetching,
    roles,
    resources
  }
}

export default useConfigQuery
