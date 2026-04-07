# 🎯 Qodex V2 - Application de Quiz Sécurisée (Espace Étudiant)

Qodex V2 est une application web développée en PHP Orienté Objet permettant aux étudiants de passer des quiz par catégories, consulter leurs résultats et suivre leur progression.

🎯 Objectif : implémenter une application sécurisée basée sur une architecture OOP avec gestion des tentatives et protection des données.

---

## 🚀 Aperçu

L’application permet aux étudiants de :

- Explorer les catégories
- Consulter les quiz actifs
- Passer des quiz
- Obtenir un score en temps réel
- Consulter leur historique

---

## 🛠️ Technologies utilisées

- PHP (POO)
- MySQL
- HTML5 / CSS3
- UML (Diagrammes)
- PDO (requêtes préparées)
- Git & GitHub

---

## 🏗️ Architecture

- Programmation Orientée Objet (OOP)
- Séparation logique métier / affichage
- Organisation en classes :
  - User
  - Category
  - Quiz
  - Question
  - Result
  - Attempt

---

## 🔐 Sécurité

- Protection CSRF sur tous les formulaires
- Requêtes préparées (PDO)
- Validation & sanitization des données
- Protection contre :
  - SQL Injection
  - XSS
  - Session Hijacking
- Gestion sécurisée des sessions :
  - Régénération d’ID
  - Expiration contrôlée
- Vérification des accès :
  - `Security::checkStudent()`

---

## ⚡ Fonctionnalités

### 🔑 Authentification

- Inscription sécurisée (email unique + hash password)
- Connexion sécurisée
- Gestion des sessions
- Redirection automatique vers espace étudiant

---

### 📂 Navigation

- Liste des catégories
- Liste des quiz actifs par catégorie
- Accès rapide et fluide

---

### 🧠 Passage de Quiz

- Démarrage d’un quiz
- Réponse à toutes les questions
- Soumission sécurisée
- Calcul du score côté serveur
- Résultat non modifiable

---

### 🔁 Gestion des Tentatives

- Une tentative par quiz *(configurable)*
- Suivi via entité `Attempt`
- Vérification d’une tentative active
- Historique des tentatives

---

### 📊 Résultats

- Score affiché immédiatement
- Accès uniquement aux résultats personnels
- Historique des quiz passés

---

## 🧩 Modèle de données

### 👤 User
- id
- nom
- email
- password_hash
- role (student)
- created_at

### 📂 Category
- id
- nom
- description

### 📝 Quiz
- id
- titre
- description
- categorie_id
- is_active
- created_at

### ❓ Question
- id
- quiz_id
- question
- options
- correct_option *(non exposée)*

### 📊 Result
- id
- quiz_id
- student_id
- score
- total_questions
- completed_at

### 🔁 Attempt
- id
- quiz_id
- student_id
- started_at
- completed_at
- is_finished

---

## 📂 Structure du projet
