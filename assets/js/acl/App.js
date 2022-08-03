import React from 'react'
import { Route, Routes } from 'react-router-dom'
import Sidebar from './components/Sidebar'
import ResourcesRoles from './pages/ResourcesRoles'
import Resources from './pages/Resources'
import References from './pages/References'
import PageAnimation from './pages/PageAnimation'

const App = () => {
  return (
    <div className="d-flex">

      <Sidebar />

      <main className="flex-fill acl-page-content p-4">
        <PageAnimation>
          <Routes>
            <Route path="/" exact element={<ResourcesRoles />} />
            <Route path="/resources" exact element={<Resources />} />
            <Route path="/references" exact element={<References />} />
            <Route path="/*" exact element={<h4>Not found!!!</h4>} />
          </Routes>
        </PageAnimation>
      </main>
    </div>
  )
}

export default App
