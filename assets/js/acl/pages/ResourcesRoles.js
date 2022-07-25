import React from 'react'
import ResourceRolesItem from '../components/ResourceRolesItem'
import useConfig from '../hooks/useConfig'
import RoleHeader from '../components/RoleHeader'
import useCleaner from '../hooks/useCleaner'
import ButtonWithLoading from '../components/ButtonWithLoading'
import SuccessIcon from '../components/SuccessIcon'
import useSuccessIcon from '../hooks/useSuccessIcon'
import { FormControl } from 'react-bootstrap'
import useTextFilter from '../hooks/useTextFilter'
import LoadingIcon from '../components/LoadingIcon'

const ResourcesRoles = () => {
  const { isLoading, hasData, isSaving, resources, roles, editResource, saveConfig } = useConfig()
  const { cleanResources, isCleaning } = useCleaner()
  const { isShowSuccessIcon, showSuccessIcon } = useSuccessIcon()
  const { textSearch, handleTextSearchChange, containsTextSearch } = useTextFilter()
  const resourcesCount = Object.keys(resources).length

  const handleSaveConfigClick = () => {
    saveConfig().then(() => {
      showSuccessIcon()
    })
  }

  const handleCleanClick = () => {
    cleanResources()
  }

  const filterByText = ([name]) => {
    return containsTextSearch(name.toLowerCase())
  }

  if (!hasData) {
    return <h3>Loading...</h3>
  }

  const filteredResources = Object.entries(resources).filter(filterByText)

  const renderSaveBtn = (
    <div className="mb-2 d-flex align-items-center">
      <ButtonWithLoading
        isLoading={isSaving}
        disabled={isSaving || isLoading}
        onClick={handleSaveConfigClick}
        minWidth={165}
        label="Save Configuration"
        loadingLabel="Saving Data..."
      />
      <SuccessIcon isShow={isShowSuccessIcon}/>
    </div>
  )

  return (
    <div className={`acl-page-container ${isLoading ? 'is-loading' : ''}`}>
      <div className="d-flex align-items-center border-bottom pb-3 gap-3">
        <h3 className="m-0">Access Control Configuration</h3>
        <LoadingIcon isLoading={isLoading} size="md"/>
        <ButtonWithLoading
          variant="outline-danger"
          className="ms-auto"
          isLoading={isCleaning}
          disabled={isCleaning || isLoading}
          onClick={handleCleanClick}
          minWidth={165}
          label="Clean Unused Resources"
          loadingLabel="Cleaning Resources..."
        />
      </div>

      <section className="mt-5">

        {renderSaveBtn}

        {resourcesCount > 10 && (
          <FormControl
            className="my-2"
            placeholder="Search..."
            value={textSearch}
            onChange={handleTextSearchChange}
          />
        )}

        <table className="table table-bordered">
          <thead>
            <tr>
              <th className="text-center align-middle" rowSpan="2">Resource</th>
              <th className="text-center align-middle" colSpan="200">Roles</th>
            </tr>
            <tr>
              <th className="text-center align-middle">All</th>
              {roles.map(role => (
                <RoleHeader key={role.role} role={role}/>
              ))}
            </tr>
          </thead>
          <tbody>
            {filteredResources.map(([name, item]) => (
              <ResourceRolesItem
                key={name}
                resource={item}
                appRoles={roles}
                onEdit={editResource}
              />
            ))}
            {filteredResources.length === 0 && (
              <tr>
                <td className="text-center" colSpan={100}>No items found</td>
              </tr>
            )}
          </tbody>

        </table>

        {renderSaveBtn}

      </section>
    </div>
  )
}

export default ResourcesRoles
