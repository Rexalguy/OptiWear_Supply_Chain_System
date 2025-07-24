

import mysql.connector
import pandas as pd
import numpy as np
from datetime import datetime, timedelta
import random
import math

# Database connection configuration
DB_CONFIG = {
    'host': '127.0.0.1',
    'user': 'root',
    'password': '',
    'database': 'Optiwear'
}

def get_db_connection():
    """Create database connection"""
    return mysql.connector.connect(**DB_CONFIG)

def clear_existing_demand_data():
    """Clear existing demand prediction data"""
    conn = get_db_connection()
    cursor = conn.cursor()
    
    try:
        cursor.execute("DELETE FROM demand_prediction_results")
        conn.commit()
        print(" Cleared existing demand prediction data")
    except Exception as e:
        print(f" Error clearing data: {e}")
    finally:
        cursor.close()
        conn.close()

def generate_seasonal_multiplier(date, category):
    """Generate seasonal demand multipliers based on category and date"""
    month = date.month
    day_of_year = date.timetuple().tm_yday
    
    # Base seasonal patterns for each category
    seasonal_patterns = {
        'Casual Wear': {
            # Higher in spring/summer, lower in winter
            'pattern': lambda m: 1.0 + 0.3 * math.sin((m - 3) * math.pi / 6)
        },
        'Formal Wear': {
            # Higher in Q1 (business season) and Q4 (holiday season)
            'pattern': lambda m: 1.0 + 0.4 * (math.sin((m - 1) * math.pi / 6) + math.sin((m - 10) * math.pi / 6))
        },
        'Children Wear': {
            # Strong spikes for back-to-school (August) and holidays (December)
            'pattern': lambda m: 1.0 + 0.6 * (math.exp(-((m - 8) ** 2) / 8) + math.exp(-((m - 12) ** 2) / 8))
        },
        'Sportswear': {
            # Peak in spring/summer, lower in winter
            'pattern': lambda m: 1.0 + 0.5 * math.sin((m - 2) * math.pi / 6)
        },
        'Workwear': {
            # Steady demand with slight increase in construction season (spring/summer)
            'pattern': lambda m: 1.0 + 0.2 * math.sin((m - 4) * math.pi / 6)
        }
    }
    
    base_multiplier = seasonal_patterns[category]['pattern'](month)
    
    # Add day-of-week variation (lower on weekends)
    weekday = date.weekday()
    weekend_factor = 0.7 if weekday >= 5 else 1.0
    
    return max(0.3, base_multiplier * weekend_factor)

def generate_trend_multiplier(date, start_date, category):
    """Generate long-term trend multipliers"""
    days_elapsed = (date - start_date).days
    
    # Different growth trends for each category
    trend_patterns = {
        'Casual Wear': 0.0001,      # Slight growth
        'Formal Wear': -0.00005,    # Slight decline (remote work trend)
        'Children Wear': 0.00015,   # Growth (population increase)
        'Sportswear': 0.0002,       # Strong growth (fitness trend)
        'Workwear': 0.00008        # Moderate growth
    }
    
    trend_rate = trend_patterns[category]
    return 1.0 + (trend_rate * days_elapsed)

def generate_base_demand(category):
    """Generate base daily demand for each category"""
    base_demands = {
        'Casual Wear': 85,      # Highest base demand
        'Formal Wear': 45,      # Lower due to remote work
        'Children Wear': 35,    # Moderate demand
        'Sportswear': 55,       # Growing segment
        'Workwear': 40         # Steady industrial demand
    }
    return base_demands[category]

def add_random_variation(base_value, variation_pct=0.25):
    """Add realistic random variation to demand values"""
    variation = random.uniform(-variation_pct, variation_pct)
    result = base_value * (1 + variation)
    return max(1, int(round(result)))  # Minimum 1, rounded to integer

def generate_demand_data_for_timeframe(categories, timeframe):
    """Generate demand data for a specific timeframe"""
    data = []
    today = datetime(2025, 7, 19)  # Current date
    
    if timeframe == '30_days':
        start_date = today + timedelta(days=1)
        end_date = today + timedelta(days=30)
        date_range = pd.date_range(start_date, end_date, freq='D')
    elif timeframe == '12_months':
        start_date = today + timedelta(days=1)
        end_date = today + timedelta(days=365)
        date_range = pd.date_range(start_date, end_date, freq='D')
    else:  # 5_years
        start_date = today + timedelta(days=1)
        end_date = today + timedelta(days=5*365)
        date_range = pd.date_range(start_date, end_date, freq='D')
    
    print(f" Generating data for {timeframe}: {len(date_range)} days from {start_date.date()} to {end_date.date()}")
    
    for category in categories:
        base_demand = generate_base_demand(category)
        
        for date in date_range:
            # Calculate demand multipliers
            seasonal_mult = generate_seasonal_multiplier(date, category)
            trend_mult = generate_trend_multiplier(date, start_date, category)
            
            # Calculate base predicted quantity
            predicted_quantity = base_demand * seasonal_mult * trend_mult
            
            # Add random variation
            final_quantity = add_random_variation(predicted_quantity)
            
            data.append({
                'shirt_category': category,
                'prediction_date': date.strftime('%Y-%m-%d'),
                'predicted_quantity': final_quantity,
                'time_frame': timeframe
            })
    
    return data

def insert_demand_data(data):
    """Insert demand data into database"""
    conn = get_db_connection()
    cursor = conn.cursor()
    
    try:
        # Prepare bulk insert query
        insert_query = """
        INSERT INTO demand_prediction_results 
        (shirt_category, prediction_date, predicted_quantity, time_frame, created_at, updated_at)
        VALUES (%s, %s, %s, %s, NOW(), NOW())
        """
        
        # Prepare data for bulk insert
        insert_data = [
            (
                item['shirt_category'],
                item['prediction_date'],
                item['predicted_quantity'],
                item['time_frame']
            )
            for item in data
        ]
        
        # Execute bulk insert
        cursor.executemany(insert_query, insert_data)
        conn.commit()
        
        print(f" Successfully inserted {len(data)} demand prediction records")
        
    except Exception as e:
        print(f" Error inserting demand data: {e}")
        conn.rollback()
    finally:
        cursor.close()
        conn.close()

def generate_summary_stats(categories, timeframes):
    """Generate and display summary statistics"""
    conn = get_db_connection()
    cursor = conn.cursor()
    
    try:
        print("\n DEMAND PREDICTION DATA SUMMARY")
        print("=" * 50)
        
        # Total records by timeframe
        for timeframe in timeframes:
            cursor.execute("""
                SELECT COUNT(*) as count, 
                       AVG(predicted_quantity) as avg_demand,
                       MIN(predicted_quantity) as min_demand,
                       MAX(predicted_quantity) as max_demand
                FROM demand_prediction_results 
                WHERE time_frame = %s
            """, (timeframe,))
            
            result = cursor.fetchone()
            print(f"\n{timeframe.upper()}:")
            print(f"   Total Records: {result[0]:,}")
            print(f"   Average Demand: {result[1]:.1f}")
            print(f"   Min Demand: {result[2]}")
            print(f"   Max Demand: {result[3]}")
        
        # Records by category
        print(f"\n RECORDS BY CATEGORY:")
        for category in categories:
            cursor.execute("""
                SELECT COUNT(*) as count, AVG(predicted_quantity) as avg_demand
                FROM demand_prediction_results 
                WHERE shirt_category = %s
            """, (category,))
            
            result = cursor.fetchone()
            print(f"  {category}: {result[0]:,} records (avg: {result[1]:.1f})")
            
    except Exception as e:
        print(f" Error generating summary: {e}")
    finally:
        cursor.close()
        conn.close()

def main():
    """Main execution function"""
    print(" Starting Realistic Demand Prediction Data Generation")
    print("=" * 60)
    
    # Categories (matching existing database structure)
    categories = [
        'Casual Wear',
        'Children Wear', 
        'Formal Wear',
        'Sportswear',
        'Workwear'
    ]
    
    timeframes = ['30_days', '12_months', '5_years']
    
    # Clear existing data
    print(" Clearing existing demand prediction data...")
    clear_existing_demand_data()
    
    # Generate new realistic data for each timeframe
    all_data = []
    
    for timeframe in timeframes:
        print(f"\n Generating realistic data for {timeframe}...")
        timeframe_data = generate_demand_data_for_timeframe(categories, timeframe)
        all_data.extend(timeframe_data)
    
    # Insert all data into database
    print(f"\n Inserting {len(all_data):,} total records into database...")
    insert_demand_data(all_data)
    
    # Generate summary statistics
    generate_summary_stats(categories, timeframes)
    
    print("\n Realistic demand prediction data generation completed!")
    print(" Your demand forecasting charts should now show varied, realistic patterns!")

if __name__ == "__main__":
    main()
