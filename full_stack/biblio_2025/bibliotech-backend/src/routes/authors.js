const router = require('express').Router();
const Author = require('../models/Author');

// GET tous les auteurs
router.get('/', async (req, res) => {
  try {
    const authors = await Author.find();
    res.json(authors);
  } catch (err) {
    res.status(500).json({ message: 'Erreur serveur' });
  }
});

// POST ajouter un auteur
router.post('/', async (req, res) => {
  try {
    const author = new Author(req.body);
    await author.save();
    res.status(201).json(author);
  } catch (err) {
    res.status(400).json({ message: 'Données invalides' });
  }
});

module.exports = router;