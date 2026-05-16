import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import { AppBar, Toolbar, Button, Container, CssBaseline } from '@mui/material';
import BookList from './components/BookList';
import AddBook from './components/AddBook';
import Login from './components/Login';
import Register from './components/Register';

function App() {
  return (
    <Router>
      <CssBaseline />
      <AppBar position="static" color="primary">
        <Toolbar>
          <Button color="inherit" component={Link} to="/">🏠 Accueil</Button>
          <Button color="inherit" component={Link} to="/add">➕ Ajouter un livre</Button>
          <div style={{ flexGrow: 1 }} />
          <Button color="inherit" component={Link} to="/login">🔑 Connexion</Button>
          <Button color="inherit" component={Link} to="/register">📝 Inscription</Button>
        </Toolbar>
      </AppBar>

      <Container maxWidth="lg" sx={{ py: 4 }}>
        <Routes>
          <Route path="/" element={<BookList />} />
          <Route path="/add" element={<AddBook />} />
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
        </Routes>
      </Container>
    </Router>
  );
}

export default App;