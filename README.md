# Projet Deefy

## Développé par
Ce projet a été développé par Noé Franoux et Nathan Ouder, étudiants de l'IUT Nancy-Charlemagne, dans le cadre du cours de Développelment web de S3.

## Fonctionnalités
L'application Deefy propose les fonctionnalités suivantes :

### Gestion des utilisateurs
- **Inscription** : Les utilisateurs peuvent créer un compte avec le rôle `USER`.
- **Connexion** : Les utilisateurs peuvent s'authentifier en utilisant leurs identifiants.
- **Déconnexion** : Les utilisateurs peuvent se déconnecter de leur compte.

### Gestion des playlists
- **Mes Playlists** : Affiche la liste des playlists de l'utilisateur authentifié. Chaque playlist est cliquable et peut être définie comme la playlist courante, stockée en session.
- **Créer une Playlist** : Un formulaire permet de créer une nouvelle playlist vide. À la validation, la playlist est créée dans la base de données et définie comme la playlist courante.
- **Afficher la Playlist Courante** : Affiche la playlist stockée en session.

### Gestion des pistes
- **Ajouter une Piste à la Playlist** : Les utilisateurs peuvent ajouter une nouvelle piste à la playlist courante. Un formulaire permet de saisir les détails de la piste, qui sont ensuite enregistrés dans la base de données et ajoutés à la playlist.

### Panneau d'administration
- **Accès Admin** : Les utilisateurs avec le rôle `ADMIN` peuvent accéder à un panneau d'administration pour gérer les utilisateurs et les playlists. Il n'y a pas de bouton sur le site pour y accéder, il faut manuellement ajouter `?action=admin` à l'URL.

## Sécurité
- Les mots de passe sont stockés de manière sécurisée en utilisant le hachage.
- Des mesures sont en place pour prévenir les attaques XSS et les injections SQL.

## HTML et CSS
- Le code HTML généré est valide.
- Un framework CSS est utilisé pour la mise en page et le style.

## Base de données
Le projet inclut un script pour créer et remplir la base de données. Le schéma de la base de données comprend des tables pour les utilisateurs, les playlists, les pistes et les permissions.

## Utilisation de l'application
1. **Inscription** : Un utilisateur peut s'inscrire en fournissant un nom d'utilisateur, un email et un mot de passe.
2. **Connexion** : Après l'inscription, l'utilisateur peut se connecter en utilisant son email et son mot de passe.
3. **Créer une Playlist** : Une fois connecté, l'utilisateur peut créer une nouvelle playlist en fournissant un nom.
4. **Ajouter des Pistes** : L'utilisateur peut ajouter des pistes à la playlist courante en fournissant les détails de la piste via un formulaire.
5. **Afficher les Playlists** : L'utilisateur peut afficher ses playlists et sélectionner l'une d'elles pour la définir comme playlist courante.
6. **Déconnexion** : L'utilisateur peut se déconnecter à tout moment.

## Soumission
Le projet est soumis sous forme d'une archive zip contenant :
- Lien vers le dépôt git : [https://github.com/vraiSlophil/iutnc_2024_deefy.git](https://github.com/vraiSlophil/iutnc_2024_deefy.git)
- Un script pour créer et remplir la base de données. Les scripts SQL pour créer les tables et y insérer des données sont fournis dans les fichiers `TABLES_mariadb.sql` et `TABLES_oracle.sql`.
- Un utilisateur est créé par défaut avec les identifiants suivants : 
    - Login : `ADMINISTRATEUR@example.com`
    - Mot de passe : `P@ssw0rd`
- Un fichier `README.md` décrivant le projet, les fonctionnalités, la sécurité, l'utilisation et la soumission.