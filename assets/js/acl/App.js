import React from 'react'
import { Route, Routes } from 'react-router-dom'
import Sidebar from './components/Sidebar'
import ResourcesRoles from './pages/ResourcesRoles'
import Resources from './pages/Resources'
import References from './pages/References'

const App = () => {
    return (
        <div className="row">

            <Sidebar/>

            <main className="col-md-9 col-lg-10 acl-page-content p-4">
                <Routes>
                    <Route path="/" exact element={<ResourcesRoles/>}/>
                    <Route path="/resources" exact element={<Resources/>}/>
                    <Route path="/references" exact element={<References/>}/>
                    <Route path="/*" exact element={<h4>Not found!!!</h4>}/>
                </Routes>
            </main>
        </div>
    )
}

export default App
