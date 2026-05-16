const router = require('express').Router();
const Book = require('../models/Book');
const auth = require('../middlewares/auth');

// GET tous les livres
router.get('/', async (req, res) => {
  try {
    const books = await Book.find().populate('author');
    res.json(books);
  } catch (err) {
    res.status(500).json({ message: 'Erreur serveur' });
  }
});

// POST ajouter un livre (protégé)
router.post('/', auth, async (req, res) => {
  try {
    const book = new Book(req.body);
    await book.save();
    res.status(201).json(book);
  } catch (err) {
    res.status(400).json({ message: 'Données invalides' });
  }
});

// DELETE supprimer un livre
router.delete('/:id', auth, async (req, res) => {
  try {
    await Book.findByIdAndDelete(req.params.id);
    res.json({ message: 'Livre supprimé' });
  } catch (err) {
    res.status(500).json({ message: 'Erreur serveur' });
  }
});

module.exports = router;