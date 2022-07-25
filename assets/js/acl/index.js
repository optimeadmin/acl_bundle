import React from 'react'
import { createRoot } from 'react-dom/client'
import App from './App'
import { BrowserRouter } from 'react-router-dom'
import { QueryClient, QueryClientProvider } from 'react-query'
import { ReactQueryDevtools } from 'react-query/devtools'

const $container = document.getElementById('acl_root')
const basename = $container.dataset.basename ?? '/'
export const endpointApi = $container.dataset.endpointApi ?? '/'
const root = createRoot($container)

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      refetchOnWindowFocus: false
    }
  }
})

root.render(
  <React.StrictMode>
    <QueryClientProvider client={queryClient}>
      <BrowserRouter basename={basename}>
        <App/>
      </BrowserRouter>
      <ReactQueryDevtools/>
    </QueryClientProvider>
  </React.StrictMode>
)
