import React from 'react'
import { NavLink } from 'react-router-dom'

export default function Sidebar() {
  return (
    <nav className="bg-light sidebar">
      <div className="position-sticky pt-3">
        <ul className="nav flex-column">
          <li className="nav-item">
            <NavLink to="/" className="nav-link">
              Access Control
            </NavLink>
          </li>
          <li className="nav-item">
            <NavLink to="/resources" className="nav-link">
              Resources Config
            </NavLink>
          </li>
          <li className="nav-item">
            <NavLink to="/references" className="nav-link">
              Controllers Config
            </NavLink>
          </li>
        </ul>
      </div>
    </nav>
  )
}
