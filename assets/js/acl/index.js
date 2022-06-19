import React from "react"
import {createRoot} from "react-dom/client"

const $container = document.getElementById('acl_root')
const root = createRoot($container);

root.render(
    <React.StrictMode>
        <h3>Ahora si, hola Mundo!</h3>
    </React.StrictMode>
)