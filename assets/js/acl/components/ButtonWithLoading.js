import React from 'react'
import { Button, Spinner } from 'react-bootstrap'

const ButtonWithLoading = ({
    isLoading,
    onClick,
    label,
    loadingLabel,
    disabled,
    minWidth = 0,
    ...props
}) => {

    return (
        <Button
            {...{ variant: 'primary', ...props }}
            disabled={disabled ?? isLoading}
            onClick={onClick}
            style={{
                minWidth,
                display: 'inline-block',
            }}>
            {isLoading && (
                <Spinner
                    className="me-2"
                    animation="border"
                    size="sm"
                />
            )}
            {isLoading ? (loadingLabel ?? label) : label}
        </Button>
    )
}

export default ButtonWithLoading
