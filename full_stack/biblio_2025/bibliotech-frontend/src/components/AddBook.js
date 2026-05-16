// bibliotech-frontend/src/components/AddBook.js
import React, { useState } from 'react';
import axios from 'axios';
import { TextField, Button, Typography, Container } from '@mui/material';

const AddBook = () => {
  const [formData, setFormData] = useState({
    title: '',
    author: '',
    genre: '',
    publicationYear: ''
  });

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const token = localStorage.getItem('token');
      await axios.post('http://localhost:5000/api/books', formData, {
        headers: { 'x-auth-token': token }
      });
      alert('Livre ajouté avec succès!');
    } catch (err) {
      alert('Erreur lors de l\'ajout');
    }
  };

  return (
    <Container>
      <Typography variant="h5" gutterBottom>📖 Ajouter un livre</Typography>
      <form onSubmit={handleSubmit}>
        <TextField
          label="Titre"
          fullWidth
          margin="normal"
          value={formData.title}
          onChange={(e) => setFormData({ ...formData, title: e.target.value })}
        />
        {/* Ajoutez les autres champs ici */}
        <Button type="submit" variant="contained" color="primary">
          Enregistrer
        </Button>
      </form>
    </Container>
  );
};

export default AddBook;