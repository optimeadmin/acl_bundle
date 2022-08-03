import React, { useRef } from 'react'

import { useLocation } from 'react-router-dom'
import { Transition, TransitionGroup } from 'react-transition-group'

function PageAnimation ({ children }) {
  const { pathname } = useLocation()
  const $ref = useRef()

  console.log({ pathname })
  return (
    <TransitionGroup component={null}>
      <Transition
        key={pathname}
        timeout={1000}
        mountOnEnter
        unmountOnExit
        nodeRef={$ref}
      >
        {state => (
          <div
            ref={$ref}
            className={`animate__animated ${state === 'exiting' ? 'd-none animate__fadeOut' : 'animate__fadeIn'}`}
          >
            {console.log({ state })}
            {children}
          </div>
        )}
      </Transition>
    </TransitionGroup>
  )
}

export default PageAnimation
