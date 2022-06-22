import React, { useState } from 'react'
import { matchOrX } from '../utils/match'

const useTextFilter = () => {
    const [textSearch, setTextSearch] = useState('')

    const handleTextSearchChange = (event) => {
        setTextSearch(event.target.value)
    }

    const containsTextSearch = (content) => {
        if (textSearch.length < 3) {
            return true
        }

        return matchOrX(content?.toLowerCase() ?? '', textSearch?.toLowerCase() ?? '')
    }

    return {
        textSearch,
        handleTextSearchChange,
        containsTextSearch,
    }
}

export default useTextFilter
