import React from 'react'
import { Button, Modal } from 'react-bootstrap'

const ModalResourceReferences = ({ name, references, show, onHide }) => {
  return (
    <Modal show={show} onHide={onHide} size="lg">
      <Modal.Header closeButton>
        <Modal.Title>
          References for "<i>{name}</i>"
        </Modal.Title>
      </Modal.Header>
      <Modal.Body>
        <ul>
          {references.map(reference => (
            <li key={reference} className="fst-italic">{reference}</li>
          ))}
        </ul>
        {references.length === 0 && (
          <h5>This resource not have direct references</h5>
        )}
      </Modal.Body>
      <Modal.Footer>
        <Button
          variant="secondary"
          size="sm" onClick={onHide}
        >Close</Button>
      </Modal.Footer>
    </Modal>
  )
}

export default ModalResourceReferences
