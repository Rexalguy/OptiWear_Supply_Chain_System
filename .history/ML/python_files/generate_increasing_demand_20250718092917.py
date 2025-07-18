#imports
import pandas as pd
import numpy as np
from datetime import datetime, timedelta
import mysql.connector
from mysql.connector import Error

# Database connection parameters
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'optiwear_supply_chain_supply'
}

def create_connection():
    """Create database connection"""
    try:
        connection = mysql.connector.connect(**db_config)
        if connection.is_connected():
            print("Successfully connected to MySQL database")
            return connection
    except Error as e:
        print(f"Error while connecting to MySQL: {e}")
        return None

def clear_existing_data(connection):
    """Clear existing demand prediction data"""
    try:
        cursor = connection.cursor()
        cursor.execute("DELETE FROM demand_prediction_results")
        connection.commit()
        print("Cleared existing demand prediction data")
    except Error as e:
        print(f"Error clearing data: {e}")

def generate_increasing_demand_data():
    """Generate realistic increasing demand data for all categories and timeframes"""
    
    categories = ['Casual Wear', 'Formal Wear', 'Children Wear', 'Workwear', 'Sportswear']
    timeframes = ['30_days', '12_months', '5_years']
    
    all_predictions = []
    base_date = datetime.now().date()
    
    for category in categories:
        # Base demand levels (different for each category)
        base_demands = {
            'Casual Wear': {'base': 180, 'growth_rate': 0.025},
            'Formal Wear': {'base': 240, 'growth_rate': 0.020},
            'Children Wear': {'base': 120, 'growth_rate': 0.035},
            'Workwear': {'base': 160, 'growth_rate': 0.018},
            'Sportswear': {'base': 140, 'growth_rate': 0.030}
        }
        
        base_demand = base_demands[category]['base']
        growth_rate = base_demands[category]['growth_rate']
        
        # Generate data for each timeframe
        for timeframe in timeframes:
            if timeframe == '30_days':
                # Daily data for 30 days with gradual increase
                for day in range(1, 31):
                    prediction_date = base_date + timedelta(days=day)
                    
                    # Calculate demand with growth and some seasonal variation
                    daily_growth = 1 + (growth_rate / 365) * day
                    seasonal_factor = 1 + 0.1 * np.sin(2 * np.pi * day / 30)
                    random_variation = np.random.normal(1, 0.05)
                    
                    predicted_quantity = int(base_demand * daily_growth * seasonal_factor * random_variation)
                    predicted_quantity = max(predicted_quantity, 50)  # Minimum threshold
                    
                    all_predictions.append({
                        'shirt_category': category,
                        'prediction_date': prediction_date.strftime('%Y-%m-%d'),
                        'predicted_quantity': predicted_quantity,
                        'time_frame': timeframe
                    })
            
            elif timeframe == '12_months':
                # Monthly data for 12 months with steady increase
                for month in range(1, 13):
                    if month == 1:
                        prediction_date = base_date.replace(day=1) + timedelta(days=32)
                        prediction_date = prediction_date.replace(day=1)
                    else:
                        prediction_date = prediction_date.replace(day=1)
                        if prediction_date.month == 12:
                            prediction_date = prediction_date.replace(year=prediction_date.year + 1, month=1)
                        else:
                            prediction_date = prediction_date.replace(month=prediction_date.month + 1)
                    
                    # Calculate monthly demand with growth
                    monthly_growth = 1 + growth_rate * month
                    seasonal_boost = 1 + 0.15 * np.sin(2 * np.pi * month / 12 + np.pi/4)
                    random_variation = np.random.normal(1, 0.08)
                    
                    predicted_quantity = int(base_demand * monthly_growth * seasonal_boost * random_variation * 30)
                    predicted_quantity = max(predicted_quantity, 1000)  # Minimum threshold for monthly
                    
                    all_predictions.append({
                        'shirt_category': category,
                        'prediction_date': prediction_date.strftime('%Y-%m-%d'),
                        'predicted_quantity': predicted_quantity,
                        'time_frame': timeframe
                    })
            
            elif timeframe == '5_years':
                # Yearly data for 5 years with substantial growth
                for year in range(1, 6):
                    prediction_date = base_date.replace(year=base_date.year + year, month=1, day=1)
                    
                    # Calculate yearly demand with compound growth
                    yearly_growth = (1 + growth_rate) ** year
                    market_expansion = 1 + 0.1 * year  # Market expansion factor
                    random_variation = np.random.normal(1, 0.10)
                    
                    predicted_quantity = int(base_demand * yearly_growth * market_expansion * random_variation * 365)
                    predicted_quantity = max(predicted_quantity, 10000)  # Minimum threshold for yearly
                    
                    all_predictions.append({
                        'shirt_category': category,
                        'prediction_date': prediction_date.strftime('%Y-%m-%d'),
                        'predicted_quantity': predicted_quantity,
                        'time_frame': timeframe
                    })
    
    return all_predictions

def insert_predictions(connection, predictions):
    """Insert predictions into database"""
    try:
        cursor = connection.cursor()
        
        insert_query = """
        INSERT INTO demand_prediction_results 
        (shirt_category, prediction_date, predicted_quantity, time_frame) 
        VALUES (%s, %s, %s, %s)
        """
        
        prediction_data = [
            (pred['shirt_category'], pred['prediction_date'], pred['predicted_quantity'], pred['time_frame'])
            for pred in predictions
        ]
        
        cursor.executemany(insert_query, prediction_data)
        connection.commit()
        
        print(f"Successfully inserted {len(predictions)} demand predictions")
        
    except Error as e:
        print(f"Error inserting predictions: {e}")
        connection.rollback()

def main():
    # Create database connection
    connection = create_connection()
    if not connection:
        return
    
    try:
        # Clear existing data
        print("Clearing existing demand prediction data...")
        clear_existing_data(connection)
        
        # Generate new increasing demand data
        print("Generating new increasing demand predictions...")
        predictions = generate_increasing_demand_data()
        
        # Insert into database
        print("Inserting predictions into database...")
        insert_predictions(connection, predictions)
        
        print("✅ Successfully generated increasing demand forecasts!")
        print("📈 All categories now show gradual increases across all timeframes")
        
        # Show sample data
        cursor = connection.cursor()
        cursor.execute("""
            SELECT shirt_category, time_frame, COUNT(*) as records, 
                   AVG(predicted_quantity) as avg_demand,
                   MIN(predicted_quantity) as min_demand,
                   MAX(predicted_quantity) as max_demand
            FROM demand_prediction_results 
            GROUP BY shirt_category, time_frame 
            ORDER BY shirt_category, 
                CASE time_frame 
                    WHEN '30_days' THEN 1 
                    WHEN '12_months' THEN 2 
                    WHEN '5_years' THEN 3 
                END
        """)
        
        results = cursor.fetchall()
        print("\n📊 Generated Data Summary:")
        print("Category | Timeframe | Records | Avg Demand | Min | Max")
        print("-" * 65)
        for row in results:
            print(f"{row[0]:<12} | {row[1]:<9} | {row[2]:<7} | {row[3]:<10.0f} | {row[4]:<3} | {row[5]}")
        
    except Exception as e:
        print(f"Error in main execution: {e}")
    
    finally:
        if connection.is_connected():
            connection.close()
            print("\nDatabase connection closed.")

if __name__ == "__main__":
    main()
