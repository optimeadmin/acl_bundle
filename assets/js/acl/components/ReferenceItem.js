import React from 'react'
import { FormCheck, FormControl } from 'react-bootstrap'
import EditedField from './EditedField'

const ReferenceItem = ({ item, onEdit: handleEdit, showHide = true }) => {
    const {
        identifier,
        resource,
        initialResource,
        name,
        route,
        routePath,
        selected,
    } = item

    const handleSelectedChange = (event) => handleEdit(
        identifier,
        { selected: event.target.checked }
    )

    const handleResourceChange = (event) => handleEdit(
        identifier,
        {
            selected: true,
            resource: event.target.value,
        }
    )

    return (
        <tr className="align-middle">
            <td className="text-center align-middle" style={{ width: 40 }}>
                <div>
                    <FormCheck
                        checked={selected}
                        onChange={handleSelectedChange}
                    />
                </div>
            </td>
            <td>
                <EditedField edited={initialResource !== resource} block>
                    <FormControl
                        // size="sm"
                        value={resource}
                        onChange={handleResourceChange}
                    />
                </EditedField>
            </td>
            <td className="small">{name}</td>
            <td className="small">{route}</td>
            <td className="small">{routePath}</td>
            {showHide && (
                <td></td>
            )}
        </tr>
    )
}

export default ReferenceItem
