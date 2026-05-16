import React, { useState } from 'react';
import axios from 'axios';
import { TextField, Button, Typography, Container, Box } from '@mui/material';
import { Link } from 'react-router-dom';

const Register = () => {
  const [formData, setFormData] = useState({ email: '', password: '' });

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await axios.post('http://localhost:5000/api/auth/register', formData);
      alert('Compte créé ! Connectez-vous maintenant.');
      window.location.href = '/login';
    } catch (err) {
      alert('Erreur: ' + (err.response?.data?.message || 'Échec de l\'inscription'));
    }
  };

  return (
    <Container maxWidth="xs">
      <Box sx={{ mt: 8, textAlign: 'center' }}>
        <Typography variant="h4" gutterBottom>📚 Bibliotech</Typography>
        <Typography variant="h5">Inscription</Typography>
        <form onSubmit={handleSubmit}>
          <TextField
            label="Email"
            fullWidth
            margin="normal"
            required
            type="email"
            value={formData.email}
            onChange={(e) => setFormData({ ...formData, email: e.target.value })}
          />
          <TextField
            label="Mot de passe"
            fullWidth
            margin="normal"
            required
            type="password"
            value={formData.password}
            onChange={(e) => setFormData({ ...formData, password: e.target.value })}
          />
          <Button
            type="submit"
            fullWidth
            variant="contained"
            sx={{ mt: 3, mb: 2 }}
          >
            S'inscrire
          </Button>
          <Link to="/login">Déjà un compte ? Se connecter</Link>
        </form>
      </Box>
    </Container>
  );
};

export default Register;