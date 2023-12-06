import { useMutation, useQueryClient } from '@tanstack/react-query'
import { cleanUnusedResources } from '../../api/endpoints'

const useCleaner = () => {
  const queryClient = useQueryClient()

  const { mutateAsync, isPending } = useMutation({
    mutationFn: cleanUnusedResources,
    async onSuccess () {
      await queryClient.invalidateQueries(['config'])
    }
  })

  return {
    cleanResources: mutateAsync,
    isCleaning: isPending
  }
}

export default useCleaner
