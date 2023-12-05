import { useState } from 'react'

export default function useSuccessIcon () {
  const [isShowSuccessIcon, setIsShowSuccessIcon] = useState(false)

  function showSuccessIcon () {
    setIsShowSuccessIcon(true)
    setTimeout(() => setIsShowSuccessIcon(false), 1000)
  }

  return {
    isShowSuccessIcon,
    showSuccessIcon
  }
}
