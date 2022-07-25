import React, { useEffect, useState } from 'react'

const EditedField = ({ children, edited = false, margin, block = false }) => {
  margin ??= block ? 1 : 0

  return (
    <div className={`acl-edited-field ${block ? 'd-block' : 'd-inline-block'}`}>
      {children}
      <div className={
        `animate__animated border-bottom border-2 border-secondary mt-${margin} ${
          edited ? 'animate__bounceIn' : 'animate__fadeOut'
        }`
      }></div>
    </div>
  )
}

export default EditedField
