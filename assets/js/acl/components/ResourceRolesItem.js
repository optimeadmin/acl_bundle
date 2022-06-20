import React from 'react';

const ResourceRolesItem = ({resource, appRoles, onEdit: editRoles}) => {
    const {name, roles} = resource

    const hasRole = (role) => {
        return roles.includes(role)
    }

    const toggleRole = (role, event) => {
        if (event.target.checked) {
            if (!roles.includes(role)) {
                editRoles(name, [...roles, role])
            }
        } else {
            editRoles(name, roles.filter(r => r !== role))
        }
    }

    return (
        <tr className="acl-resource-container" data-resource="edit">
            <td className="fw-bold">{name}</td>
            <td className="text-center align-middle js-acl-select-all-roles">
                <div><input type="checkbox"/></div>
            </td>
            {appRoles.map(({role}) => (
                <td key={role} className="text-center align-middle">
                    <div>
                        <input
                            type="checkbox"
                            checked={hasRole(role)}
                            onChange={e => toggleRole(role, e)}
                        />
                    </div>
                </td>
            ))}
        </tr>
    );
};

export default ResourceRolesItem;
