import { useQuery } from 'react-query'
import { getConfig } from '../api/endpoints'
import { useState } from 'react'

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
  const [previousResources, setPreviousResources] = useState(null)

  if (dataUpdatedAt !== 0 && resources !== previousResources) {
    setPreviousResources(resources)
    setResources(() => {
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

      return resourcesData
    })
  }

  return {
    isLoading,
    isFetching,
    roles,
    resources
  }
}

export default useConfigQuery
