import React, { useState } from 'react'
import { Button, FormCheck, FormControl } from 'react-bootstrap'
import ModalResourceReferences from './ModalResourceReferences'
import EditedField from './EditedField'

const ResourceItem = ({ item, onEdit: handleEdit }) => {
  const {
    key,
    name,
    description,
    initialName,
    initialDescription,
    references,
    createdByUser,
    selected,
    valid
  } = item
  const [showReferences, setShowReferences] = useState(false)

  const updateProperty = (property, value) => {
    handleEdit(key, { [property]: value, selected: true })
  }
  const handleNameChange = (event) => updateProperty('name', event.target.value)
  const handleDescriptionChange = (event) => updateProperty('description', event.target.value)
  const handleSelectedChange = (event) => handleEdit(key, { selected: event.target.checked })

  return (
    <tr>
      <td className="text-center align-middle" style={{ width: 40 }}>
        <div>
          <FormCheck
            disabled={!valid}
            checked={selected}
            onChange={handleSelectedChange}
          />
        </div>
      </td>
      <td>
        <EditedField edited={initialName !== name} block>
          <FormControl
            value={name}
            onChange={handleNameChange}
          />
        </EditedField>
      </td>
      <td>
        <EditedField edited={initialDescription !== description} block>
          <FormControl
            rows="1"
            as="textarea"
            value={description}
            onChange={handleDescriptionChange}
          />
        </EditedField>
      </td>
      <td className="text-center align-middle">{createdByUser ? 'User' : 'App'}</td>
      <td className="text-center align-middle">
        <Button variant="outline-secondary" size="sm" onClick={() => setShowReferences(true)}>
                    Show ({references?.length ?? 0})
        </Button>
        <ModalResourceReferences
          name={name}
          references={references}
          show={showReferences}
          onHide={() => setShowReferences(false)}
        />
      </td>
    </tr>
  )
}

export default ResourceItem
