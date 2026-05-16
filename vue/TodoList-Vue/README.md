# 📝 Vue.js Todo List Application

Une application de gestion de tâches moderne et élégante construite avec Vue.js 3, offrant une expérience utilisateur intuitive pour organiser vos tâches quotidiennes.

![Vue.js](https://img.shields.io/badge/Vue.js-3.2.13-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)
![Build](https://img.shields.io/badge/Build-Passing-success?style=for-the-badge)

## 🌟 Fonctionnalités

- ✅ **Ajouter des tâches** - Créez rapidement de nouvelles tâches
- ✏️ **Édition en ligne** - Double-cliquez pour modifier une tâche
- ✔️ **Marquer comme complété** - Cochez les tâches terminées
- 🗑️ **Supprimer des tâches** - Supprimez les tâches non désirées
- 🔍 **Filtrage intelligent** - Filtrez par : Toutes, Actives, ou Complétées
- 📊 **Compteur de tâches** - Visualisez le nombre de tâches actives
- 🧹 **Nettoyage rapide** - Supprimez toutes les tâches complétées en un clic
- ✨ **Marquer tout** - Marquez toutes les tâches comme complétées/actives
- 🎨 **Interface élégante** - Design moderne et responsive inspiré de TodoMVC
- 🔐 **Page de connexion** - Système d'authentification simple

## 🚀 Démonstration

L'application offre une interface utilisateur claire et intuitive :

- **Interface principale** : Zone d'ajout en haut, liste de tâches au centre, filtres et statistiques en bas
- **Édition rapide** : Double-cliquez sur une tâche pour la modifier directement
- **Feedback visuel** : Les tâches complétées sont barrées et grisées
- **Navigation fluide** : Passage entre les différentes vues de filtrage

## 🛠️ Technologies utilisées

- **Vue.js 3.2.13** - Framework JavaScript progressif
- **Vue Router** - Gestion des routes
- **Vue CLI 5.0** - Outils de développement
- **ES6+** - JavaScript moderne
- **CSS3** - Styles et animations

## 📋 Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- [Node.js](https://nodejs.org/) (version 12.x ou supérieure)
- [npm](https://www.npmjs.com/) (généralement installé avec Node.js)

## 🔧 Installation

1. **Clonez le dépôt**
   ```bash
   git clone https://github.com/votre-username/vue-todolist.git
   cd vue-todolist
   ```

2. **Installez les dépendances**
   ```bash
   npm install
   ```

3. **Lancez le serveur de développement**
   ```bash
   npm run serve
   ```

4. **Accédez à l'application**
   
   Ouvrez votre navigateur et visitez : `http://localhost:8080`

## 📦 Scripts disponibles

```bash
# Démarrer le serveur de développement
npm run serve

# Compiler pour la production
npm run build

# Linter et corriger les fichiers
npm run lint
```

## 📁 Structure du projet

```
vue-todolist/
├── public/
│   └── index.html
├── src/
│   ├── components/
│   │   ├── Info.vue          # Composant footer avec filtres et stats
│   │   ├── Login.vue         # Page de connexion
│   │   ├── TodoApp.vue       # Composant item de tâche
│   │   └── TodoList.vue      # Composant liste de tâches
│   ├── App.vue               # Composant principal
│   ├── main.js               # Point d'entrée de l'application
│   └── router.js             # Configuration du routeur
├── babel.config.js
├── jsconfig.json
├── package.json
├── vue.config.js
└── README.md
```

## 🎯 Architecture des composants

### App.vue
Composant racine qui gère :
- L'état global des tâches
- L'ajout de nouvelles tâches
- La logique de filtrage
- Les actions globales (tout marquer, nettoyer)

### TodoList.vue
Affiche la liste des tâches avec filtrage :
- Gère le filtrage des tâches (Toutes/Actives/Complétées)
- Propage les événements vers le composant parent

### TodoApp.vue
Représente une tâche individuelle :
- Affichage et édition de tâche
- Toggle de statut (complété/actif)
- Suppression de tâche

### Info.vue
Footer informatif avec :
- Compteur de tâches actives
- Filtres de visualisation
- Bouton de nettoyage des tâches complétées

### Login.vue
Page d'authentification simple
- Formulaire de connexion
- Validation basique

## 💡 Utilisation

### Ajouter une tâche
1. Tapez votre tâche dans le champ "What needs to be done?"
2. Appuyez sur `Enter` pour l'ajouter

### Modifier une tâche
1. Double-cliquez sur le texte de la tâche
2. Modifiez le texte
3. Appuyez sur `Enter` pour sauvegarder

### Marquer comme complétée
- Cliquez sur la case à cocher à gauche de la tâche

### Supprimer une tâche
- Survolez la tâche et cliquez sur le `×` qui apparaît à droite

### Filtrer les tâches
Utilisez les filtres en bas :
- **All** - Affiche toutes les tâches
- **Active** - Affiche uniquement les tâches actives
- **Completed** - Affiche uniquement les tâches complétées

## 🔒 Authentification

L'application inclut une page de connexion basique. Pour accéder à l'application :
1. Visitez la page d'accueil (`/`)
2. Entrez n'importe quel nom d'utilisateur et mot de passe
3. Cliquez sur "Login" pour accéder à la liste de tâches

> **Note** : L'authentification actuelle est une simulation. Pour une application en production, implémentez un système d'authentification sécurisé avec backend.

## 🎨 Personnalisation

### Modifier les couleurs
Les couleurs principales se trouvent dans `App.vue` :
- Couleur d'accentuation : `#b83f45`
- Couleur de validation : `#5dc2af`
- Couleur de suppression : `#cc9a9a`

### Ajouter des fonctionnalités
1. Sauvegarde dans localStorage
2. Synchronisation avec une API backend
3. Catégories de tâches
4. Dates d'échéance
5. Priorités de tâches

## 🌐 Build pour la production

```bash
npm run build
```

Les fichiers optimisés seront générés dans le dossier `dist/` et prêts à être déployés sur un serveur web.

## 🚢 Déploiement

### Netlify
1. Connectez votre dépôt GitHub à Netlify
2. Configuration de build :
   - Build command: `npm run build`
   - Publish directory: `dist`

### Vercel
```bash
npm install -g vercel
vercel
```

### GitHub Pages
```bash
# Ajoutez à vue.config.js
module.exports = {
  publicPath: process.env.NODE_ENV === 'production'
    ? '/nom-de-votre-repo/'
    : '/'
}

# Puis déployez
npm run build
git add dist -f
git commit -m "Deploy"
git subtree push --prefix dist origin gh-pages
```

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Forkez le projet
2. Créez une branche pour votre fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Poussez vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 📝 Roadmap

- [ ] Persistance des données avec localStorage
- [ ] Intégration d'une API backend
- [ ] Tests unitaires et d'intégration
- [ ] Mode sombre/clair
- [ ] Drag & drop pour réorganiser les tâches
- [ ] Catégories et tags
- [ ] Dates d'échéance avec rappels
- [ ] Authentification sécurisée
- [ ] Export/Import de tâches
- [ ] Application mobile (React Native / Capacitor)

## 🐛 Problèmes connus

- L'authentification est actuellement simulée (pas de vraie vérification)
- Les données sont perdues lors du rafraîchissement de la page
- Pas de support pour le drag & drop

## 👨‍💻 Auteur

**Ahmed Ezzine Ailaoui**
- LinkedIn : [Ahmed ezzine Ailaoui]([https://linkedin.com/in/votre-profil](https://www.linkedin.com/in/ahmed-ezzine-ailaoui-a40380254/))
- Email : vahmedailaoui2002@gmail.com


## 📚 Ressources

- [Documentation Vue.js](https://vuejs.org/)
- [Vue Router Documentation](https://router.vuejs.org/)
- [Vue CLI Documentation](https://cli.vuejs.org/)

---

⭐️ Si ce projet vous a aidé, n'hésitez pas à lui donner une étoile !
