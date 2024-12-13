# TP Symfony

## Routes Disponibles

### Authentification
- `POST /api/register` : Inscription d'un nouvel utilisateur.
- `POST /api/login` : Connexion d'un utilisateur, renvoi un jwt.

### Utilisateurs
- `GET /api/admin/user` : Liste des utilisateurs (admin).
- `POST /api/admin/user` : Création d'un utilisateur (admin).
- `PUT /api/admin/user/{id}` : Mise à jour d'un utilisateur (admin).
- `DELETE /api/admin/user/{id}` : Suppression d'un utilisateur (admin).

### Réservations
- `GET /api/reservation` : Liste des réservations de l'utilisateur.
- `POST /api/reservation` : Création d'une réservation.
- `GET /api/admin/reservation` : Liste de toutes les réservations (admin).
- `GET /api/admin/reservation/{id}` : Obtenir une réservation par ID (admin).
- `DELETE /api/admin/reservation/{id}` : Suppression d'une réservation (admin).
- `PUT /api/admin/reservation/{id}` : Mise à jour d'une réservation (admin).
- `POST /api/admin/reservation` : Création d'une réservation pour un utilisateur spécifique (admin).

## Exemples de Requêtes

### Connexion

```json
POST /api/register
{
  "email": "user@example.com",
  "password": "password",
  "name": "user",
  "phoneNumber": "010203040506"
}
```

```json
POST /api/login
{
    "email": "user@example.com",
    "password": "password"
}

Renvoi un jwt
```

### Utilisateurs

```json
GET /api/admin/user
Headers: { Authorization: Bearer jwt }
```

```json
POST /api/admin/user
Headers: { Authorization: Bearer jwt }
{
    "email": "user@example.com",
    "password": "password",
    "name": "user",
    "phoneNumber": "010203040506",
    "roles": ["ROLE_ADMIN"]
}
```

```json
PUT /api/admin/user/{id}
Headers: { Authorization: Bearer jwt }
{
    "email": "user@example.com",
    "name": "user",
    "phoneNumber": "010203040506",
    "roles": ["ROLE_ADMIN"]
}
```

```json
DELETE /api/admin/user/{id}
Headers: { Authorization: Bearer jwt }

(Ne fonctionne que si l'user n'as pas de relation)
```

### Réservations

```json
POST /api/reservation
Headers: { Authorization: Bearer jwt }
{
  "date": "2023-12-31",
  "timeSlot": "PT1H",
  "eventName": "Événement"
}
```

```json 
GET /api/reservation
Headers: { Authorization: Bearer jwt }
```

```json
GET /api/admin/reservation
Headers: { Authorization: Bearer jwt }
```

```json
GET /api/admin/reservation/{id}
Headers: { Authorization: Bearer jwt }
```

```json
DELETE /api/admin/reservation/{id}
Headers: { Authorization: Bearer jwt }
```

```json 
PUT /api/admin/reservation/{id}
Headers: { Authorization: Bearer jwt }
{
  "date": "2023-12-31",
  "timeSlot": "PT1H",
  "eventName": "Événement"
}
```

```json
POST /api/admin/reservation
Headers: { Authorization: Bearer jwt }
{
  "date": "2023-12-31",
  "timeSlot": "PT1H",
  "eventName": "Événement",
  "user_id": 1
}
```

## Installation et Lancement du Projet Symfony

### Étapes d'Installation

1. **Cloner le dépôt**
  ```bash
  git clone <url-du-dépôt>
  cd <nom-du-dossier>
  ```

2. **Installer les dépendances**
  ```bash
  composer install
  ```

3. **Créer la base de données**
  ```bash
  php bin/console doctrine:database:create
  ```

4. **Exécuter les migrations**
  ```bash
  php bin/console doctrine:migrations:migrate
  ```

5. **Lancer le serveur de développement**
  ```bash
  symfony server:start
  ```

Le projet sera accessible à l'adresse `http://localhost:8000`.