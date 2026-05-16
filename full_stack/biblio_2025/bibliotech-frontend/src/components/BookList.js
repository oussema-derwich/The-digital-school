// bibliotech-frontend/src/components/BookList.js
import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { List, ListItem, Typography, IconButton } from '@mui/material';
import DeleteIcon from '@mui/icons-material/Delete';

const BookList = () => {
  const [books, setBooks] = useState([]);

  const fetchBooks = async () => {
    try {
      const res = await axios.get('http://localhost:5000/api/books');
      setBooks(res.data);
    } catch (err) {
      console.error(err);
    }
  };

  const deleteBook = async (id) => {
    try {
      await axios.delete(`http://localhost:5000/api/books/${id}`, {
        headers: { 'x-auth-token': localStorage.getItem('token') }
      });
      fetchBooks();
    } catch (err) {
      alert('Suppression échouée');
    }
  };

  useEffect(() => {
    fetchBooks();
  }, []);

  return (
    <div>
      <Typography variant="h4" gutterBottom>📚 Liste des livres</Typography>
      <List>
        {books.map(book => (
          <ListItem 
            key={book._id}
            secondaryAction={
              <IconButton edge="end" onClick={() => deleteBook(book._id)}>
                <DeleteIcon />
              </IconButton>
            }
          >
            {book.title} - {book.author?.name} ({book.genre})
          </ListItem>
        ))}
      </List>
    </div>
  );
};

export default BookList;