import React, { useEffect, useState } from 'react'
import { FaCheckDouble } from 'react-icons/fa'

const SuccessIcon = ({ isShow }) => {
    const [invisible, setInvisible] = useState(true)

    useEffect(() => {
        setTimeout(() => setInvisible(false), 1000)
    }, [])

    return (
        <FaCheckDouble
            className={`ms-2 animate__animated ${
                isShow ? 'animate__tada' : 'animate__fadeOut'} ${
                invisible ? 'invisible' : ''
            }`}
            size="1.5em"
            color="#AAAAAA"
        />
    )
}

export default SuccessIcon
