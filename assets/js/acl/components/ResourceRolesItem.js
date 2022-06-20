import React from 'react';

const ResourceRolesItem = ({resource, appRoles, onEdit: editRoles}) => {
    const {name, roles, initialRoles, blockedRoles} = resource

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

    const isBlocked = (role) => {
        return blockedRoles.includes(role)
    }

    const isBlockedAll = () => {
        return blockedRoles.length === appRoles.length
    }

    const isSelectedAll = () => {
        return appRoles.length === roles.length
    }

    return (
        <tr className="acl-resource-container" data-resource="edit">
            <td className="fw-bold">{name}</td>
            <td className="text-center align-middle js-acl-select-all-roles">
                <div>
                    <div className="d-inline-block border-2 border-bottom border-light">
                        <input
                            disabled={isBlockedAll()}
                            type="checkbox"
                            checked={isSelectedAll()}
                            onChange={handleSelectAllChange}
                        />
                    </div>
                </div>
            </td>
            {appRoles.map(({role}) => (
                <td key={role} className="text-center align-middle">
                    <div>
                        <div className={`d-inline-block border-2 border-bottom border-${
                            isChanged(role) ? 'secondary' : 'light'
                        }`}>
                            <input
                                disabled={isBlocked(role)}
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
