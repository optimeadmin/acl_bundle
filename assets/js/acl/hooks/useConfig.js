import { useImmer } from 'use-immer'
import { useCallback } from 'react'
import useConfigQuery from './useConfigQuery'
import useResourcesRolesMutation from './useResourcesRolesMutation'

const updateParentRoles = (items, resource) => {
  const { parent, roles } = resource

  if (!parent || !items[parent]) {
    return
  }

  const parentResource = items[parent]
  parentResource.roles = [...new Set([...parentResource.roles, ...roles])]
  parentResource.blockedRoles = [...roles]

  updateParentRoles(items, parentResource)
}

const updateChildrenRoles = (items, resource) => {
  const { roles } = resource
  const children = Object.values(resource.children ?? {})

  children.forEach(name => {
    const resource = items[name]

    if (!resource) {
      return
    }

    resource.roles = resource.roles?.filter(role => roles.includes(role))

    updateChildrenRoles(items, resource)
  })
}

const setResourcesFactory = (set) => {
  return (resources) => {
    Object.entries(resources).forEach(([, resource]) => {
      updateParentRoles(resources, resource)
      updateChildrenRoles(resources, resource)
    })

    set(resources)
  }
}

const useConfig = () => {
  const [resources, setResources] = useImmer({})
  const {
    isLoading,
    isFetching,
    roles,
    resources: savedResources
  } = useConfigQuery(setResourcesFactory(setResources))

  const { saveConfig, isSaving } = useResourcesRolesMutation(resources)

  const editResource = useCallback((name, roles) => {
    setResources(items => {
      if (!(name in items)) {
        return
      }

      items[name].roles = roles

      updateParentRoles(items, items[name])
      updateChildrenRoles(items, items[name])
    })
  }, [savedResources, setResources])

  return {
    hasData: !isLoading,
    isLoading: isFetching,
    isSaving,
    roles,
    resources,
    editResource,
    saveConfig
  }
}

export default useConfig
