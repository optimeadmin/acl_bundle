import React from 'react'
import { Route, Routes } from 'react-router-dom'
import Sidebar from './components/Sidebar'
import References from './pages/References'
import Resources from './pages/Resources'
import ResourcesRoles from './pages/ResourcesRoles'

export default function App() {
  return (
    <div className="d-flex">

      <Sidebar />

      <main className="flex-fill acl-page-content p-4">
        <Routes>
          <Route path="/" exact element={<ResourcesRoles />} />
          <Route path="/resources" exact element={<Resources />} />
          <Route path="/references" exact element={<References />} />
          <Route path="/*" exact element={<h4>Not found!!!</h4>} />
        </Routes>
      </main>
    </div>
  )
}
