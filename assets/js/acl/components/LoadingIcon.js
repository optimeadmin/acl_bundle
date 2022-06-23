import React from 'react'
import { Spinner } from 'react-bootstrap'

const LoadingIcon = ({ isLoading, size = 'sm', className = '' }) => {
    // const [invisible, setInvisible] = useState(true)
    //
    // useEffect(() => {
    //     setTimeout(() => setInvisible(false), 1000)
    // }, [])

    if (!isLoading) {
        return null
    }

    return (
        <Spinner
            className={`me-2 ${className}`}
            animation="border"
            size={size}
        />
    )
}

export default LoadingIcon
