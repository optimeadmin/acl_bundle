import React, { useState } from 'react'
import ResourceRolesItem from '../components/ResourceRolesItem'
import useConfig from '../hooks/useConfig'
import RoleHeader from '../components/RoleHeader'
import { FaCheckDouble } from 'react-icons/fa'
import useCleaner from '../hooks/useCleaner'
import ButtonWithLoading from '../components/ButtonWithLoading'

const ResourcesRoles = () => {
    const { isLoading, hasData, isSaving, resources, roles, editResource, saveConfig } = useConfig()
    const { cleanResources, isCleaning } = useCleaner()
    const [isSaved, setSaved] = useState(false)
    const [showSavedIcon, setShowSavedIcon] = useState(false)

    const handleSaveConfigClick = () => {
        setShowSavedIcon(true)
        saveConfig().then(() => {
            setSaved(true)
            setTimeout(() => {
                setSaved(false)
            }, 1000)
        })
    }

    const handleCleanClick = () => {
        cleanResources()
    }

    if (!hasData) {
        return <h3>Loading...</h3>
    }

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
            <FaCheckDouble
                className={`ms-2 animate__animated ${
                    isSaved ? 'animate__tada' : 'animate__fadeOut'} ${
                    showSavedIcon ? '' : 'invisible'
                }`}
                size="1.5em"
                color="#AAAAAA"
            />
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
    )

    return (
        <div>
            <h3 className="border-bottom pb-3">Access Control Configuration</h3>

            <section className="mt-5">

                {renderSaveBtn}

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
                    {Object.entries(resources).map(([name, item]) => (
                        <ResourceRolesItem
                            key={name}
                            resource={item}
                            appRoles={roles}
                            onEdit={editResource}
                        />
                    ))}
                    </tbody>

                </table>

                {renderSaveBtn}

            </section>
        </div>
    )
}

export default ResourcesRoles

