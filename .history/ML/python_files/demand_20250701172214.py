#imports
import pandas as pd
import numpy as np

from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
from sklearn.model_selection import train_test_split
import matplotlib.pyplot as plt
import seaborn as sns

#loading the dataset
df = pd.read_csv('../datasets')

#show the first 5 rows
print(df.head())

#check for missing values
print("\n--- Missing Values ---")
print(df.isnull().sum())

#converting the Date column to datetime
df['Date'] = pd.to_datetime(df['Date'])

#extract important date features that will help us 
df['Month'] = df['Date'].dt.month
df['DayOfWeek'] = df['Date'].dt.dayofweek  # 0=Monday, 6=Sunday

#dropping columns that won't help us predict demand
df = df.drop(columns=['Transaction ID', 'Date', 'Customer ID', 'Total Amount'])

#changing columns with non-numeric data to have numeric data
df = pd.get_dummies(df, columns=['Gender', 'Product Category'], drop_first=True)


#defining features and target
X = df.drop('Quantity', axis=1)
y = df['Quantity']

#splitting the dataset into data for training and data for testing
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

#training the model
model = RandomForestRegressor(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

#predict
y_pred = model.predict(X_test)

#evaluating the model
mae = mean_absolute_error(y_test, y_pred)
mse = mean_squared_error(y_test, y_pred)
rmse = np.sqrt(mse)
r2 = r2_score(y_test, y_pred)

print("\n--- Model Performance ---")
print(f"MAE  : {mae:.2f}")
print(f"MSE  : {mse:.2f}")
print(f"RMSE : {rmse:.2f}")
print(f"R^2  : {r2:.2f}")

#visualization
plt.figure(figsize=(6, 4))
sns.scatterplot(x=y_test, y=y_pred)
plt.xlabel("Actual Quantity")
plt.ylabel("Predicted Quantity")
plt.title("Actual vs Predicted Quantity")
plt.grid(True)
plt.tight_layout()
plt.show()



