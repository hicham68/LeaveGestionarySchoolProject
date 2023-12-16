import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, Button } from 'react-native';
import NetInfo from "@react-native-community/netinfo";
import {Picker} from '@react-native-picker/picker';
export default function App() {
  const [employeeData, setEmployeeData] = useState([]);
  const [selectedEmployee, setSelectedEmployee] = useState(null);
  const [vacationData, setVacationData] = useState(null);

  useEffect(() => {
    const fetchEmployees = async () => {
      try {
        const apiUrl = `http://172.18.192.1:8000/api/employee`;
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
      const apiUrl = `http://172.18.192.1:8000/api/demande-conge/employee/${employeeId}`;
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

  return (
    <View style={styles.container}>
      <Text>Bienvenue dans votre application React Native!</Text>
      <View style={styles.dropdownContainer}>
        <Text>Sélectionnez un employé :</Text>
      <Picker
           style={{height: 50, width: 100}}
        selectedValue={selectedEmployee}
        onValueChange={(itemValue) => handleEmployeeChange(itemValue)}
      >
       <Picker.Item label="hello" value="key0" />
        { employeeData.map(employee=> <Picker.Item key={employee.id} label={employee.first_name} value={employee.id}/>)}
      </Picker>

      </View>
      {vacationData ? (
        <View>
          <Text>Congés de l'employé sélectionné :</Text>
          <Text>{JSON.stringify(vacationData)}</Text>
        </View>
      ) : null}
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  dropdownContainer: {
    marginVertical: 20,
  },
});
