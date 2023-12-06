import React from 'react'
import ResourceRolesItem, { ResourceRolesItemLoading } from '../components/ResourceRolesItem'
import RoleHeader, { RoleHeaderLoading } from '../components/RoleHeader'
import useCleaner from '../hooks/useCleaner'
import ButtonWithLoading from '../components/ButtonWithLoading'
import SuccessIcon from '../components/SuccessIcon'
import useSuccessIcon from '../hooks/useSuccessIcon'
import { FormControl } from 'react-bootstrap'
import useTextFilter from '../hooks/useTextFilter'
import LoadingIcon from '../components/LoadingIcon'
import { useConfig, useConfigQuery, useSaveResourcesRoles } from '../hooks/resources/roles'
import { useIsMutating } from '@tanstack/react-query'

export default function ResourcesRoles () {
  const { isLoading, resources: dbResources, roles } = useConfigQuery()
  const { editResource, resources } = useConfig(dbResources)

  const { textSearch, handleTextSearchChange, filterByText } = useTextFilter()
  const resourcesCount = Object.keys(dbResources).length

  const filteredResources = filterByText(Object.entries(resources), ([name]) => name.toLowerCase())

  const saveBtn = <SaveButton isLoading={isLoading} resources={resources} />

  return (
    <div className={`acl-page-container ${isLoading ? 'is-loading' : ''}`}>
      <div className="d-flex align-items-center border-bottom pb-3 gap-3">
        <h3 className="m-0">Access Control Configuration</h3>
        <LoadingIcon isLoading={isLoading} size="md" />
        <CleanButton isLoading={isLoading} />
      </div>

      <section className="mt-5">

        {saveBtn}

        {resourcesCount > 10 && (
          <FormControl
            className="my-2"
            placeholder="Search..."
            value={textSearch}
            onChange={handleTextSearchChange} />
        )}

        <table className="table table-bordered">
          <thead>
            <tr>
              <th className="text-center align-middle" rowSpan="2" style={{
                width: isLoading ? '30%' : null
              }}>Resource</th>
              <th className="text-center align-middle" colSpan="200">Roles</th>
            </tr>
            <tr>
              <th className="text-center align-middle" style={{ width: isLoading ? '10%' : null }}>All</th>
              {isLoading && (
                <>
                  <RoleHeaderLoading />
                  <RoleHeaderLoading />
                  <RoleHeaderLoading />
                  <RoleHeaderLoading />
                </>
              )}
              {!isLoading && roles.map(role => (
                <RoleHeader key={role.role} role={role} />
              ))}
            </tr>
          </thead>
          <tbody>
            {isLoading && (
              <>
                <ResourceRolesItemLoading />
                <ResourceRolesItemLoading />
                <ResourceRolesItemLoading />
              </>
            )}
            {!isLoading && filteredResources.map(([name, item]) => (
              <ResourceRolesItem
                key={name}
                resource={item}
                appRoles={roles}
                onEdit={editResource} />
            ))}
            {!isLoading && filteredResources.length === 0 && (
              <tr>
                <td className="text-center" colSpan={100}>No items found</td>
              </tr>
            )}
          </tbody>

        </table>

        {saveBtn}

      </section>
    </div>
  )
}

function SaveButton ({ isLoading, resources }) {
  const isSaving = useIsMutating({ mutationKey: ['resources'] }) > 0
  const { saveConfig } = useSaveResourcesRoles()
  const { isShowSuccessIcon, showSuccessIcon } = useSuccessIcon()

  async function save () {
    await saveConfig(resources)
    showSuccessIcon()
  }

  return (
    <div className="mb-2 d-flex align-items-center">
      <ButtonWithLoading
        isLoading={isSaving}
        disabled={isSaving || isLoading}
        onClick={save}
        minWidth={165}
        label="Save Configuration"
        loadingLabel="Saving Data..." />
      <SuccessIcon isShow={isShowSuccessIcon} />
    </div>
  )
}

function CleanButton ({ isLoading }) {
  const { cleanResources, isCleaning } = useCleaner()

  return (
    <ButtonWithLoading
      variant="outline-danger"
      className="ms-auto"
      isLoading={isCleaning}
      disabled={isCleaning || isLoading}
      onClick={cleanResources}
      minWidth={165}
      label="Clean Unused Resources"
      loadingLabel="Cleaning Resources..."
    />
  )
}