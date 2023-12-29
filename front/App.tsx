import React from 'react';
import { View, Button, StyleSheet } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import HomeScreen from './screens/HomeScreen'; // Create this component
import EmployeeScreen from './screens/EmployeeScreen'; // Create this component
import VacationScreen from './screens/VacationScreen'; // Create this component

const Stack = createStackNavigator();

const App: React.FC = () => {
  return (
    <NavigationContainer>
      <Stack.Navigator initialRouteName="Home">
        <Stack.Screen name="Home" component={HomeScreen} />
        <Stack.Screen name="Employees" component={EmployeeScreen} />
        <Stack.Screen name="Vacations" component={VacationScreen} />
      </Stack.Navigator>
    </NavigationContainer>
  );
};

export default App;
