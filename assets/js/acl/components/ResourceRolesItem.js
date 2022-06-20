import React from 'react';

const ResourceRolesItem = ({resource, appRoles, onEdit: editRoles}) => {
    const {name, roles, initialRoles} = resource

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

    const handleSelectAllChange = (event) => {
        if (event.target.checked) {
            editRoles(name, appRoles.map(({role}) => role))
        } else {
            editRoles(name, [])
        }
    }

    const isChanged = (role) => {
        return hasRole(role) !== initialRoles.includes(role)
    }

    const isSelectedAll = () => {
        return appRoles.length === roles.length
    }

    return (
        <tr className="acl-resource-container" data-resource="edit">
            <td className="fw-bold">{name}</td>
            <td className="text-center align-middle js-acl-select-all-roles">
                <div><input
                    type="checkbox"
                    checked={isSelectedAll()}
                    onChange={handleSelectAllChange}
                /></div>
            </td>
            {appRoles.map(({role}) => (
                <td key={role} className="text-center align-middle">
                    <div>
                        <div className={`d-inline-block mb-1 border-secondary border-${
                            isChanged(role) ? 'bottom' : '0'
                        }`}>
                            <input
                                type="checkbox"
                                checked={hasRole(role)}
                                onChange={e => toggleRole(role, e)}
                            />
                        </div>
                    </div>
                </td>
            ))}
        </tr>
    );
};

export default ResourceRolesItem;
