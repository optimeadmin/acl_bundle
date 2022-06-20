import React from 'react';
import ResourceRolesItem from "../components/ResourceRolesItem";
import useConfig from "../hooks/useConfig";
import {Button} from "react-bootstrap"
import RoleHeader from "../components/RoleHeader"

const ResourcesRoles = () => {
    const {isLoading, resources, roles, editResource, saveConfig} = useConfig()

    const handleSaveConfigClick = () => {
        saveConfig()
    }

    if (isLoading) {
        return <h3>Loading...</h3>
    }

    return (
        <div>
            <h3 className="border-bottom pb-3">Access Control Configuration</h3>

            <section className="mt-5">

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

                <Button variant="primary" onClick={handleSaveConfigClick}>Save Configuration</Button>
            </section>
        </div>
    );
};

export default ResourcesRoles;
