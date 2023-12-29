import React from 'react';
import { View, Button, StyleSheet } from 'react-native';

const HomeScreen: React.FC = ({ navigation }) => {
  return (
    <View style={styles.container}>
      <Button
        title="modifier congé"
        onPress={() => navigation.navigate('Employees')}
      />
      <Button
        title="créer congé"
        onPress={() => navigation.navigate('Vacations')}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
});

export default HomeScreen;
