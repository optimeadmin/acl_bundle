import React from 'react';
import {Route, Routes} from "react-router-dom";
import Sidebar from "./components/Sidebar";
import ResourcesRoles from "./pages/ResourcesRoles";

const App = () => {
    return (
        <div className="row">

            <Sidebar/>

            <main className="col acl-page-content p-4">
                <Routes>
                    <Route path="/" element={<ResourcesRoles/>}/>
                </Routes>
            </main>
        </div>
    );
};

export default App;
