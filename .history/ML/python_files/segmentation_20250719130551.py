import os
import pandas as pd
import mysql.connector
from dotenv import load_dotenv
from pathlib import Path
from sklearn.cluster import KMeans

# Load .env for DB credentials
load_dotenv(dotenv_path=Path('../../.env'))

DB_HOST = os.getenv('DB_HOST', 'localhost')
DB_USER = os.getenv('DB_USERNAME', 'root')
DB_PASS = os.getenv('DB_PASSWORD', '')
DB_NAME = os.getenv('DB_DATABASE', 'optiwear')

# Connect to MySQL
def get_db_connection():
    return mysql.connector.connect(
        host=DB_HOST,
        user=DB_USER,
        password=DB_PASS,
        database=DB_NAME
    )

def load_data():
    df = pd.read_csv('../datasets/segmentation_dataset_expanded.csv')
    # Expect columns: CustomerID, Gender, Age, Product Category, Quantity
    return df

def segment_customers(df, n_segments=8):
    # Create age groups first
    bins = [0, 17, 25, 35, 50, 100]
    labels = ['0-17', '18-25', '26-35', '36-50', '51+']
    df['Age_Group'] = pd.cut(df['Age'], bins=bins, labels=labels, right=False)
    
    # Create natural segments based on Gender + Age Group combinations
    # This creates realistic customer segments for business analysis
    df['Segment_Label'] = df['Gender'] + ' ' + df['Age_Group'].astype(str)
    
    # Filter out any age groups that shouldn't exist (like 0-17 since our data is 18+)
    df = df[df['Age_Group'] != '0-17'].copy()
    
    return df

def summarize_segments(df):
    # For each segment, gender, age group, and shirt category, sum quantity and count customers
    summary = df.groupby(['Segment_Label', 'Gender', 'Age_Group', 'Product Category']).agg({
        'Quantity': 'sum',
        'Customer ID': 'nunique'  # Count unique customers
    }).reset_index()
    
    summary = summary.rename(columns={
        'Segment_Label': 'segment_label',
        'Gender': 'gender',
        'Age_Group': 'age_group',
        'Product Category': 'shirt_category',
        'Quantity': 'total_purchased',
        'Customer ID': 'customer_count'
    })
    print('Summary columns:', summary.columns.tolist())  # Debug print
    return summary

def clear_existing_segments():
    """Clear existing segmentation results before inserting new data"""
    conn = get_db_connection()
    cursor = conn.cursor()
    cursor.execute('DELETE FROM segmentation_results')
    conn.commit()
    cursor.close()
    conn.close()
    print('Cleared existing segmentation data.')

def save_segments(summary):
    conn = get_db_connection()
    cursor = conn.cursor()
    for _, row in summary.iterrows():
        cursor.execute('''
            INSERT INTO segmentation_results (segment_label, gender, age_group, shirt_category, total_purchased, created_at, updated_at)
            VALUES (%s, %s, %s, %s, %s, NOW(), NOW())
        ''', (
            row['segment_label'],
            row['gender'],
            row['age_group'],
            row['shirt_category'],
            int(row['total_purchased'])
        ))
    conn.commit()
    cursor.close()
    conn.close()

def main():
    print('Starting customer segmentation...')
    
    # Clear existing data
    clear_existing_segments()
    
    # Load and process data
    df = load_data()
    print(f'Loaded {len(df)} records from dataset')
    
    df = segment_customers(df)
    print(f'Created segments, remaining records: {len(df)}')
    
    # Show segment distribution before saving
    segment_counts = df['Segment_Label'].value_counts()
    print('\nSegment distribution:')
    for segment, count in segment_counts.items():
        print(f'  {segment}: {count} customers')
    
    summary = summarize_segments(df)
    print(f'Generated {len(summary)} summary records')
    
    save_segments(summary)
    print('Segmentation results saved to database.')
    
    # Show final summary
    total_records = summary['total_purchased'].sum()
    print(f'\nTotal quantity across all segments: {total_records}')
    print('Segmentation complete!')

if __name__ == '__main__':
    main()
