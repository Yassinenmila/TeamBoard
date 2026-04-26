# Cahier des Charges Fonctionnel - TeamBoard

## 1. Présentation du Projet
TeamBoard est une plateforme de communication et de gestion collaborative. Son objectif est de structurer le flux de travail, de centraliser les échanges administratifs et d'organiser la coordination des équipes au sein d'un environnement numérique unique.

---

## 2. Rôles et Permissions
Le système s'articule autour de trois types de comptes :
* **Administrateur** : Gestion totale du système, des utilisateurs et de l'ensemble des ressources.
* **Responsable** : Gestion opérationnelle des tâches, des réunions, des annonces et validation des demandes.
* **Membre** : Consultation des missions affectées, participation aux réunions et soumission de demandes.

---

## 3. Spécifications Fonctionnelles

### 3.1 Authentification et Sécurité
* **Connexion** : Accès sécurisé via un identifiant (email) et un mot de passe avec génération de jetons d'authentification.
* **Redirection Intelligente** : Après connexion, l'utilisateur est automatiquement dirigé vers le tableau de bord correspondant à ses permissions.
* **Contrôle d'Accès** : Sécurisation des pages côté client pour empêcher l'accès aux interfaces non autorisées.
* **Déconnexion** : Fermeture de session avec suppression complète des accès actifs.

### 3.2 Gestion des Tâches
* **Création et Suivi** : Définition de tâches incluant un titre, une description, un statut (en attente, en cours, terminée) et une date d'échéance.
* **Affectation** : Possibilité d'assigner une tâche à un ou plusieurs collaborateurs simultanément.
* **Visibilité Sélective** : 
    * L'administrateur visualise l'intégralité des tâches.
    * Le responsable gère les tâches qu'il a créées.
    * Le membre consulte exclusivement les missions qui lui sont confiées.
* **Édition** : Les modifications et suppressions sont restreintes à l'auteur de la tâche ou à l'administrateur.

### 3.3 Gestion des Demandes
* **Soumission** : Les membres peuvent créer des demandes par catégorie (temps de travail, administrative, contractuelle ou liée au poste).
* **Commentaires** : Possibilité d'ajouter des précisions lors de l'envoi et lors de la réponse du responsable.
* **Cycle de Vie** : Une demande ne peut être modifiée par le membre que si elle n'a pas encore été traitée.
* **Validation** : Les responsables et administrateurs acceptent ou refusent les demandes avec une justification obligatoire.

### 3.4 Gestion des Réunions
* **Planification** : Organisation de sessions de travail avec précision de la date, de l'heure, du lieu et de l'objet.
* **Invitations** : Envoi automatique d'invitations aux membres sélectionnés lors de la création.
* **Suivi des Présences** : Gestion de l'état des invitations (en attente, accepté, refusé).
* **Modification** : Ajustement des détails de la réunion entraînant une mise à jour immédiate pour tous les participants.

### 3.5 Gestion des Annonces
* **Publication** : Diffusion d'informations générales ou urgentes par les administrateurs et responsables.
* **Diffusion** : Visibilité immédiate pour tous les membres de l'organisation sur leur fil d'actualité.
* **Modération** : Possibilité de corriger ou de supprimer une annonce par son auteur.

### 3.6 Système de Notifications
* **Alertes Automatiques** : Information en temps réel pour toute assignation, invitation, nouvelle annonce ou décision sur une demande.
* **Organisation** : Affichage chronologique des alertes.
* **Interaction** : Options pour marquer une notification comme lue, tout marquer comme lu ou supprimer les alertes traitées.

### 3.7 Tableaux de Bord et Administration
* **Vue Administrateur** : Statistiques globales sur l'activité de l'équipe et gestion complète des comptes utilisateurs (création, modification, suppression).
* **Vue Responsable** : Supervision de la charge de travail par membre et suivi des prochaines échéances d'équipe.
* **Vue Membre** : Priorisation des tâches urgentes et calendrier des prochaines réunions.

---

## 4. Droits d'Accès par Rôle

### Administrateur
* Accès intégral à toutes les ressources de la plateforme.
* Gestion complète des comptes utilisateurs (création, modification, suppression).
* Attribution et modification des rôles.
* Supervision de toutes les tâches, réunions et demandes du système.
* Publication d'annonces générales ou urgentes.
* Suppression de n'importe quelle ressource jugée obsolète ou inappropriée.

### Responsable
* Accès au tableau de bord de supervision de l'équipe.
* Création, modification et suppression de ses propres tâches.
* Assignation de tâches aux membres de l'équipe.
* Planification de réunions et gestion des invitations.
* Traitement des demandes soumises par les membres (acceptation ou refus avec commentaire).
* Publication et modération de ses propres annonces.

### Membre
* Accès au tableau de bord personnel avec priorités et deadlines.
* Consultation des tâches qui lui sont personnellement affectées.
* Soumission de demandes administratives ou contractuelles.
* Modification de ses propres demandes tant qu'elles sont en attente.
* Consultation des réunions auxquelles il est invité et gestion de sa réponse (accepté/refusé).
* Consultation du fil d'annonces de l'entreprise.
* Gestion de ses propres notifications.