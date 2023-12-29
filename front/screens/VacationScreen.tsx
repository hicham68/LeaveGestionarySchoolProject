import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, Button, TextInput } from 'react-native';
import { Picker } from '@react-native-picker/picker';

const VacationScreen = () => {
  const [employeeData, setEmployeeData] = useState([]);
  const [selectedEmployee, setSelectedEmployee] = useState(null);
  const [vacationData, setVacationData] = useState(null);
  const [startDate, setStartDate] = useState('');
  const [endDate, setEndDate] = useState('');
  const [vacationTypes, setVacationTypes] = useState([]);
  const [reasons, setReasons] = useState([]);
  const [selectedVacationType, setSelectedVacationType] = useState(null);
  const [selectedReason, setSelectedReason] = useState(null);

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

    const fetchVacationTypes = async () => {
      try {
        const apiUrl = `http://172.21.64.1:8000/api/vacation-type`;
        const response = await fetch(apiUrl);
        const data = await response.json();
        setVacationTypes(data);
      } catch (error) {
        console.error('Erreur lors de la récupération des types de congé :', error);
      }
    };

    const fetchReasons = async () => {
      try {
        const apiUrl = `http://172.21.64.1:8000/api/reason`;
        const response = await fetch(apiUrl);
        const data = await response.json();
        setReasons(data);
      } catch (error) {
        console.error('Erreur lors de la récupération des raisons :', error);
      }
    };

    fetchEmployees();
    fetchVacationTypes();
    fetchReasons();
  }, []);

  const fetchVacations = async (employeeId) => {
    try {
      const apiUrl = `http://172.21.64.1:8000/api/demande-conge/employee/${employeeId}`;
      const response = await fetch(apiUrl);
      const data = await response.json();
      setVacationData(data);
    } catch (error) {
      console.error('Erreur lors de la récupération des congés :', error);
    }
  };

  const handleEmployeeChange = (employeeId) => {
    setSelectedEmployee(employeeId);
    fetchVacations(employeeId);
  };

  const createVacationRequest = async () => {
    try {
      const apiUrl = `http://172.21.64.1:8000/api/demande-conge`;
      const response = await fetch(apiUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          employee_id: selectedEmployee,
          start_date: startDate,
          end_date: endDate,
          vacation_type_id: selectedVacationType,
          reason_id: selectedReason,
        }),
      });
      console.log(startDate);
      const data = await response.json();
      console.log('Vacation request created:', data);
    } catch (error) {
      console.error('Erreur lors de la création de la demande de congé :', error);
    }
  };

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
      <Text>Sélectionnez le type de congé :</Text>
      <Picker
        style={{ height: 50, width: 200 }}
        selectedValue={selectedVacationType}
        onValueChange={(itemValue) => setSelectedVacationType(itemValue)}
      >
        {vacationTypes.map((type) => (
          <Picker.Item key={type.id} label={type.label} value={type.id} />
        ))}
      </Picker>
      <Text>Sélectionnez la raison :</Text>
      <Picker
        style={{ height: 50, width: 200 }}
        selectedValue={selectedReason}
        onValueChange={(itemValue) => setSelectedReason(itemValue)}
      >
        {reasons.map((reason) => (
          <Picker.Item key={reason.id} label={reason.label} value={reason.id} />
        ))}
      </Picker>
      <Button title="Créer une demande de congé" onPress={createVacationRequest} />
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

export default VacationScreen;
