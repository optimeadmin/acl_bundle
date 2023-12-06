import React from 'react'
import EditedField from './EditedField'
import { FormCheck, Placeholder } from 'react-bootstrap'
import { getRandomInt } from '../utils/random'

const getMargin = (level) => {
  return (level || 0) * 10
}

export default function ResourceRolesItem ({ resource, appRoles, onEdit: editRoles }) {
  const { name, level, roles, initialRoles, blockedRoles } = resource

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
      editRoles(name, appRoles.map(({ role }) => role))
    } else {
      editRoles(name, blockedRoles)
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
      <td className={'fw-bold'}>
        <span style={{ marginLeft: getMargin(level) }}>
          {name}
        </span>
      </td>
      <td className="text-center align-middle js-acl-select-all-roles">
        <div>
          <div className="d-inline-block border-2 border-bottom border-light">
            <EditedField>
              <FormCheck
                disabled={isBlockedAll()}
                checked={isSelectedAll()}
                onChange={handleSelectAllChange} />
            </EditedField>
          </div>
        </div>
      </td>
      {appRoles.map(({ role }) => (
        <td key={role} className="text-center align-middle">
          <div>
            <EditedField edited={isChanged(role)}>
              <FormCheck
                disabled={isBlocked(role)}
                checked={hasRole(role)}
                onChange={e => toggleRole(role, e)} />
            </EditedField>
          </div>
        </td>
      ))}
    </tr>
  )
}

export function ResourceRolesItemLoading ({ rolesCount = 4 }) {
  return (
    <tr className="acl-resource-container" data-resource="edit">
      <td className={'fw-bold'}>
        <Placeholder animation="glow">
          <Placeholder xs={getRandomInt(4, 10)} />
        </Placeholder>
      </td>
      <td className="text-center align-middle js-acl-select-all-roles">
        <Placeholder animation="glow">
          <Placeholder xs={2} />
        </Placeholder>
      </td>
      {[...Array(rolesCount)].map((x, index) => (
        <td key={index} className='text-center'>
          <Placeholder animation="glow">
            <Placeholder xs={2} />
          </Placeholder>
        </td>
      ))}
    </tr>
  )
}
