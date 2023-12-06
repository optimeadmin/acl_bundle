import React from 'react'
import { Button, OverlayTrigger, Placeholder, Popover } from 'react-bootstrap'
import { getRandomInt } from '../utils/random'

export default function RoleHeader ({ role }) {
  const { label, parentRoles } = role

  const popover = (
    <Popover>
      <Popover.Header as="h3">Inherited Roles</Popover.Header>
      <Popover.Body>
        {parentRoles.map(role => (
          <div key={role}>{role}</div>
        ))}
      </Popover.Body>
    </Popover>
  )

  return (
    <th className="align-middle text-nowrap">
      <div className="d-flex align-items-center">
        <span>{label}</span>
        {parentRoles.length > 0 && (
          <OverlayTrigger trigger="focus" placement="top" overlay={popover}>
            <Button className="ms-auto rounded-circle" variant="outline-secondary" size="sm"> ? </Button>
          </OverlayTrigger>
        )}
      </div>
    </th>
  )
}

export function RoleHeaderLoading () {
  return (
    <th className="align-middle text-nowrap">
      <Placeholder animation="glow">
        <Placeholder xs={getRandomInt(2, 11)} />
      </Placeholder>
    </th>
  )
}
