import React, { useState } from 'react';

function Register() {
  const [formData, setFormData] = useState({ name: '', email: '', password: '' });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!formData.name || !formData.email || !formData.password) {
      alert('Please fill out all fields!');
    } else {
      console.log('Form submitted:', formData);
      alert('Registration successful!');
    }
  };

  return (
    <div style={{ maxWidth: '400px', margin: '0 auto', textAlign: 'center' }}>
      <h1>Register Page</h1>
      <form onSubmit={handleSubmit}>
        <div style={{ marginBottom: '10px' }}>
          <label>
            Name:
            <input
              type="text"
              name="name"
              value={formData.name}
              onChange={handleChange}
              style={{ marginLeft: '10px' }}
            />
          </label>
        </div>
        <div style={{ marginBottom: '10px' }}>
          <label>
            Email:
            <input
              type="email"
              name="email"
              value={formData.email}
              onChange={handleChange}
              style={{ marginLeft: '10px' }}
            />
          </label>
        </div>
        <div style={{ marginBottom: '10px' }}>
          <label>
            Password:
            <input
              type="password"
              name="password"
              value={formData.password}
              onChange={handleChange}
              style={{ marginLeft: '10px' }}
            />
          </label>
        </div>
        <button type="submit" style={{ padding: '5px 10px', cursor: 'pointer' }}>
          Register
        </button>
      </form>
    </div>
  );
}

export default Register;
