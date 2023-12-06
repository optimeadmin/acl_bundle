import { useState } from 'react'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useImmer } from 'use-immer'
import { getConfig, saveResourcesRoles } from '../../../api/endpoints'
import { produce } from 'immer'

const emptyData = { roles: [], resources: {} }

function mapResourcesToQuery (resources) {
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
}

export function useConfigQuery () {
  const { isLoading, isFetching, data: { roles, resources } = emptyData } = useQuery({
    queryKey: ['config'],
    queryFn: getConfig,
    keepPreviousData: true,
    select ({ roles, resources }) {
      const mappedRoles = roles.map(role => ({
        ...role,
        parentRoles: Object.values(role.parentRoles)
      }))

      return {
        roles: mappedRoles,
        resources: mapResourcesToQuery(resources),
      }
    }
  })

  return {
    isLoading,
    isFetching,
    roles,
    resources
  }
}

function updateParentRoles (items, resource) {
  const { parent, roles } = resource

  if (!parent || !items[parent]) {
    return
  }

  const parentResource = items[parent]
  parentResource.roles = [...new Set([...parentResource.roles, ...roles])]
  parentResource.blockedRoles = [...roles]

  updateParentRoles(items, parentResource)
}

function updateChildrenRoles (items, resource) {
  const { roles } = resource
  const children = Object.values(resource.children ?? {})

  children.forEach(name => {
    if (!(name in items)) {
      return
    }

    const resource = items[name]

    resource.roles = resource.roles?.filter(role => roles.includes(role))

    updateChildrenRoles(items, resource)
  })
}

function mapResourcesToState (resources) {
  return produce(resources, resources => {
    Object.entries(resources).forEach(([, resource]) => {
      updateParentRoles(resources, resource)
      updateChildrenRoles(resources, resource)
    })

    return resources
  })
}

export function useConfig (dbResources) {
  const [resources, setResources] = useImmer({})
  const [prevResources, setPrevResources] = useState(null)

  if (prevResources !== dbResources) {
    setPrevResources(dbResources)
    setResources(mapResourcesToState(dbResources))
  }

  function editResource (name, roles) {
    setResources(items => {
      if (!(name in items)) {
        return
      }

      items[name].roles = roles

      updateParentRoles(items, items[name])
      updateChildrenRoles(items, items[name])
    })
  }

  return {
    resources,
    editResource,
  }
}

export function useSaveResourcesRoles () {
  const queryClient = useQueryClient()

  const { mutateAsync, isLoading: isSaving } = useMutation({
    mutationFn: saveResourcesRoles,
    mutationKey: ['resources', 'roles'],
    async onSuccess () {
      await queryClient.invalidateQueries(['config'])
    }
  })

  return {
    isSaving,
    saveConfig: mutateAsync
  }
}