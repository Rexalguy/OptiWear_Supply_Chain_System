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
    df = pd.read_csv('..')
    # Expect columns: CustomerID, Gender, Age, Product Category, Quantity
    return df

def segment_customers(df, n_segments=4):
    # Encode Gender
    df['Gender_Code'] = df['Gender'].map({'Male': 0, 'Female': 1})
    # Age groups for summary
    bins = [0, 17, 25, 35, 50, 100]
    labels = ['0-17', '18-25', '26-35', '36-50', '51+']
    df['Age_Group'] = pd.cut(df['Age'], bins=bins, labels=labels, right=False)
    # KMeans clustering on Age and Gender
    X = df[['Age', 'Gender_Code']]
    kmeans = KMeans(n_clusters=n_segments, random_state=42, n_init=10)
    df['Segment'] = kmeans.fit_predict(X)
    df['Segment_Label'] = df['Gender'] + ' ' + df['Age_Group'].astype(str)
    return df

def summarize_segments(df):
    # For each segment, gender, age group, and shirt category, sum quantity
    summary = df.groupby(['Segment_Label', 'Gender', 'Age_Group', 'Product Category'])['Quantity'].sum().reset_index()
    summary = summary.rename(columns={
        'Segment_Label': 'segment_label',
        'Gender': 'gender',
        'Age_Group': 'age_group',
        'Product Category': 'shirt_category',
        'Quantity': 'total_purchased'
    })
    print('Summary columns:', summary.columns.tolist())  # Debug print
    return summary

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
    df = load_data()
    df = segment_customers(df)
    summary = summarize_segments(df)
    save_segments(summary)
    print('Segmentation results saved to database.')

if __name__ == '__main__':
    main()
