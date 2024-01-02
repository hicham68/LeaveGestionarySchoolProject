import AsyncStorage from '@react-native-async-storage/async-storage';
import NetInfo from '@react-native-community/netinfo';

const AsyncStorageFunctions = {
  storeData: async (key, value) => {
    try {
      await AsyncStorage.setItem(key, value);
      console.log('Données enregistrées avec succès.');
    } catch (error) {
      console.log('Erreur lors de l\'enregistrement des données : ', error);
    }
  },

  getData: async (key) => {
    try {
      const value = await AsyncStorage.getItem(key);
      if (value !== null) {
        return JSON.parse(value);
      } else {
        console.log('Aucune valeur trouvée pour la clé spécifiée.');
        return null;
      }
    } catch (error) {
      console.log('Erreur lors de la récupération des données : ', error);
      return null;
    }
  },

  removeData: async (key) => {
    try {
      await AsyncStorage.removeItem(key);
      console.log('Données supprimées avec succès.');
    } catch (error) {
      console.log('Erreur lors de la suppression des données : ', error);
    }
  },

  // Nouvelles fonctions pour la gestion des mises à jour en attente
  queueVacationUpdate: async (updateDetails) => {
    try {
      // Récupérer les mises à jour en attente actuelles
      const pendingUpdates = await AsyncStorageFunctions.getData('pendingUpdates') || [];
      // Ajouter la nouvelle mise à jour à la file d'attente
      pendingUpdates.push(updateDetails);
      // Stocker la file d'attente mise à jour
      await AsyncStorageFunctions.storeData('pendingUpdates', JSON.stringify(pendingUpdates));
    } catch (error) {
      console.error('Erreur lors de la mise en file d\'attente de la mise à jour :', error);
    }
  },

  syncPendingVacationUpdates: async () => {
    try {
      // Récupérer les mises à jour en attente
      const pendingUpdates = await AsyncStorageFunctions.getData('pendingUpdates');
      if (pendingUpdates && pendingUpdates.length > 0) {
        // Synchroniser chaque mise à jour en attente avec la base de données
        for (const update of pendingUpdates) {
          const apiUrl = `http://172.21.64.1:8000/api/demande-conge/${update.id}`;
          const response = await fetch(apiUrl, {
            method: 'PATCH',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              start_date: update.start_date,
              end_date: update.end_date,
            }),
          });
          const updatedData = await response.json();
          console.log('Vacation request updated:', updatedData);
        }
        // Effacer les mises à jour en attente après la synchronisation réussie
        await AsyncStorageFunctions.removeData('pendingUpdates');
      }
    } catch (error) {
      console.error('Erreur lors de la synchronisation des mises à jour en attente :', error);
    }
  },

 checkInternetConnectivity: async () => {
   try {
     const apiUrl = `http://172.21.64.1:8000/api/vacation-type`;
     const response = await fetch(apiUrl);

     if (response.ok) {
       console.log('Connexion à l\'API réussie');
       return true;
     } else {
       console.log('Erreur lors de la connexion à l\'API:', response.status);
       return false;
     }
   } catch (error) {
     console.error('Erreur lors de la récupération de la connexion à l\'API', error);
     return false;
   }
 }

};

export default AsyncStorageFunctions;
