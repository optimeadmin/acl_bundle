import React from 'react'
import { Button } from 'react-bootstrap'
import LoadingIcon from './LoadingIcon'

const ButtonWithLoading = ({
    isLoading,
    onClick,
    label,
    loadingLabel,
    disabled,
    active = true,
    minWidth = 0,
    ...props
}) => {

    return (
        <Button
            {...{ variant: (active ? 'primary' : 'outline-primary'), ...props }}
            disabled={disabled ?? isLoading}
            onClick={onClick}
            style={{
                minWidth,
                display: 'inline-block',
            }}>

            <LoadingIcon isLoading={isLoading}/>

            {isLoading ? (loadingLabel ?? label) : label}
        </Button>
    )
}

export default ButtonWithLoading
