import { useState } from 'react'

const useSuccessIcon = () => {
  const [isShowSuccessIcon, setIsShowSuccessIcon] = useState(false)

  const showSuccessIcon = () => {
    setIsShowSuccessIcon(true)
    setTimeout(() => setIsShowSuccessIcon(false), 1000)
  }

  return {
    isShowSuccessIcon,
    showSuccessIcon
  }
}

export default useSuccessIcon
