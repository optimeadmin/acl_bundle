import { useMutation, useQueryClient } from 'react-query'
import { cleanUnusedResources } from '../api/endpoints'

const useCleaner = () => {
  const queryClient = useQueryClient()

  const { mutateAsync, isLoading } = useMutation({
    mutationFn: cleanUnusedResources,
    async onSuccess () {
      await queryClient.invalidateQueries(['config'])
    }
  })

  return {
    cleanResources: mutateAsync,
    isCleaning: isLoading
  }
}

export default useCleaner
