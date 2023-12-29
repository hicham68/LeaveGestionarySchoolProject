import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, Button, TextInput } from 'react-native';
import { Picker } from '@react-native-picker/picker';
import AsyncStorageFunctions from '../services/AsyncStorageFunctions.tsx'; // Assurez-vous d'importer correctement votre service AsyncStorageFunctions

const EmployeeScreen = () => {
  const [employeeData, setEmployeeData] = useState([]);
  const [selectedEmployee, setSelectedEmployee] = useState(null);
  const [vacationData, setVacationData] = useState(null);
  const [startDate, setStartDate] = useState('');
  const [endDate, setEndDate] = useState('');
  const [selectedVacationId, setSelectedVacationId] = useState(null);

  useEffect(() => {
    const fetchEmployees = async () => {
      try {
        const apiUrl = `http://172.21.64.1:8000/api/employee`;
        const response = await fetch(apiUrl);
        const data = await response.json();
        setEmployeeData(data);
      } catch (error) {
        console.error('Erreur lors de la récupération des employés :', error);
      }
    };

    fetchEmployees();
  }, []);

  const fetchVacations = async (employeeId) => {
    try {
      const apiUrl = `http://172.21.64.1:8000/api/demande-conge/employee/${employeeId}`;
      const response = await fetch(apiUrl);
      const data = await response.json();
      setVacationData(data.vacations);
    } catch (error) {
      console.error('Erreur lors de la récupération des congés :', error);
    }
  };

  const fetchVacationDetails = async (vacationId) => {
    setSelectedVacationId(vacationId);

    try {
      const apiUrl = `http://172.21.64.1:8000/api/demande-conge/${vacationId}`;
      const response = await fetch(apiUrl);
      const data = await response.json();
      setStartDate(data.date_debut);
      setEndDate(data.date_fin);
    } catch (error) {
      console.error('Erreur lors de la récupération des détails du congé :', error);
    }
  };

  const handleEmployeeChange = (employeeId) => {
    setSelectedEmployee(employeeId);
    fetchVacations(employeeId);
  };

  const updateVacationRequest = async () => {
    try {
      const apiUrl = `http://172.21.64.1:8000/api/demande-conge/${selectedVacationId}`;
      const response = await fetch(apiUrl, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          start_date: startDate,
          end_date: endDate,
        }),
      });
      const updatedData = await response.json();
      console.log('Vacation request updated:', updatedData);
    } catch (error) {
      console.error('Erreur lors de la mise à jour de la demande de congé :', error);
      // En cas d'échec, ajouter la mise à jour en attente
      const updateDetails = {
        id: selectedVacationId,
        start_date: startDate,
        end_date: endDate,
      };
      await AsyncStorageFunctions.queueVacationUpdate(updateDetails);
    }
  };

  // Synchroniser les mises à jour en attente lors de la reconnexion
  useEffect(() => {
    const syncUpdates = async () => {
      const isConnected = await AsyncStorageFunctions.checkInternetConnectivity();
      if (isConnected) {
        await AsyncStorageFunctions.syncPendingVacationUpdates();
      }
    };

    syncUpdates();
  }, []);

  return (
    <View style={styles.container}>
      <Text>Bienvenue dans votre application React Native!</Text>
      <View style={styles.dropdownContainer}>
        <Text>Sélectionnez un employé :</Text>
        <Picker
          style={{ height: 50, width: 100 }}
          selectedValue={selectedEmployee}
          onValueChange={(itemValue) => handleEmployeeChange(itemValue)}
        >
          {employeeData.map((employee) => (
            <Picker.Item key={employee.id} label={employee.first_name} value={employee.id} />
          ))}
        </Picker>
      </View>
      <Text>Sélectionnez un congé à mettre à jour :</Text>
      <Picker
        style={{ height: 50, width: 200 }}
        selectedValue={selectedVacationId}
        onValueChange={(itemValue) => fetchVacationDetails(itemValue)}
      >
        {Array.isArray(vacationData)
          ? vacationData.map((vacation) => (
              <Picker.Item
                key={vacation.id}
                label={`${vacation.start_date} - ${vacation.end_date}`}
                value={vacation.id}
              />
            ))
          : null}
      </Picker>
      <Text>Date de début :</Text>
      <TextInput
        style={styles.input}
        placeholder="YYYY-MM-DD"
        onChangeText={setStartDate}
        value={startDate}
      />
      <Text>Date de fin :</Text>
      <TextInput
        style={styles.input}
        placeholder="YYYY-MM-DD"
        onChangeText={setEndDate}
        value={endDate}
      />
      <Button title="Mettre à jour la demande de congé" onPress={updateVacationRequest} />
      {vacationData ? (
        <View>
          <Text>Congés de l'employé sélectionné :</Text>
          <Text>{JSON.stringify(vacationData)}</Text>
        </View>
      ) : null}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  dropdownContainer: {
    marginVertical: 20,
  },
  input: {
    height: 40,
    borderColor: 'gray',
    borderWidth: 1,
    marginBottom: 10,
    padding: 10,
    width: 200,
  },
});

export default EmployeeScreen;
