const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');
const swaggerUI = require('swagger-ui-express');
const swaggerDoc = require('./swagger.json');
require('dotenv').config();

const app = express();

// Middlewares
app.use(cors());
app.use(express.json());

// Connexion MongoDB
mongoose.connect(process.env.MONGO_URI)
  .then(() => console.log('Connecté à MongoDB'))
  .catch(err => console.error('Erreur MongoDB:', err));

// Routes
app.use('/api/auth', require('./routes/auth'));
app.use('/api/books', require('./routes/books'));
app.use('/api/authors', require('./routes/authors'));
app.use('/api/borrows', require('./routes/borrows'));

// Documentation Swagger
app.use('/api-docs', swaggerUI.serve, swaggerUI.setup(swaggerDoc));

// Port
const PORT = process.env.PORT || 5000;
app.listen(PORT, () => console.log(`Serveur démarré sur le port ${PORT}`));