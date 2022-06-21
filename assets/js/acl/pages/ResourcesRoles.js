import React, {useState} from 'react';
import ResourceRolesItem from "../components/ResourceRolesItem";
import useConfig from "../hooks/useConfig";
import {Button, Spinner} from "react-bootstrap"
import RoleHeader from "../components/RoleHeader"
import {FaCheckDouble} from "react-icons/fa"

const ResourcesRoles = () => {
    const {isLoading, hasData, isSaving, resources, roles, editResource, saveConfig} = useConfig()
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

    if (!hasData) {
        return <h3>Loading...</h3>
    }

    const renderSaveBtn = (
        <div className="mb-2 d-flex align-items-center">
            <Button
                disabled={isSaving || isLoading}
                variant="primary"
                onClick={handleSaveConfigClick}
                style={{
                    minWidth: 165,
                    display: 'inline-block',
                }}>
                {isSaving && (
                    <Spinner
                        className="me-2"
                        animation="border"
                        size="sm"
                    />
                )}
                {isSaving
                    ? 'Saving Data...'
                    : 'Save Configuration'
                }
            </Button>
            <FaCheckDouble
                className={`ms-2 animate__animated ${
                    isSaved ? 'animate__tada' : 'animate__fadeOut'} ${
                    showSavedIcon ? '' : 'invisible'
                }`}
                size="1.5em"
                color="#AAAAAA"
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
    );
};

export default ResourcesRoles;
