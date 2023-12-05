import { useState } from 'react'
import { matchOrX } from '../utils/match'

export default function useTextFilter() {
  const [textSearch, setTextSearch] = useState('')

  function handleTextSearchChange(event) {
    setTextSearch(event.target.value)
  }

  function containsTextSearch(content) {
    if (textSearch.length < 3) {
      return true
    }

    return matchOrX(content?.toLowerCase() ?? '', textSearch?.toLowerCase() ?? '')
  }

  function filterByText(items, itemNormalizer = null) {
    return items?.filter(item => containsTextSearch(itemNormalizer ? itemNormalizer(item) : item)) ?? []
  }

  return {
    textSearch,
    handleTextSearchChange,
    containsTextSearch,
    filterByText,
  }
}
