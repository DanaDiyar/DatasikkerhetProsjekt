import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Register from './pages/Register';

function Home() {
  return <h1>Welcome to the Home Page</h1>;
}

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/register" element={<Register />} />
        {/* Flere ruter kan legges til her */}
      </Routes>
    </Router>
  );
}

export default App;
