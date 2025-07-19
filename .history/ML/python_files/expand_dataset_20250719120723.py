import pandas as pd
import numpy as np
from datetime import datetime, timedelta
import random

# Set random seed for reproducibility
np.random.seed(42)
random.seed(42)

def load_existing_data():
    """Load the existing dataset to understand patterns"""
    df = pd.read_csv('../datasets/segmentation_dataset.csv')
    return df

def analyze_patterns(df):
    """Analyze existing patterns in the dataset"""
    patterns = {
        'gender_dist': df['Gender'].value_counts(normalize=True).to_dict(),
        'age_ranges': {
            'min': df['Age'].min(),
            'max': df['Age'].max(),
            'mean': df['Age'].mean(),
            'std': df['Age'].std()
        },
        'product_categories': df['Product Category'].unique().tolist(),
        'category_dist': df['Product Category'].value_counts(normalize=True).to_dict(),
        'quantity_patterns': {
            'min': df['Quantity'].min(),
            'max': df['Quantity'].max(),
            'mean': df['Quantity'].mean()
        },
        'price_patterns': df.groupby('Product Category')['Price per Unit'].agg(['mean', 'std', 'min', 'max']).to_dict(),
        'date_range': {
            'start': df['Date'].min(),
            'end': df['Date'].max()
        }
    }
    return patterns

def generate_realistic_age(gender):
    """Generate realistic age based on gender and shopping patterns"""
    if gender == 'Female':
        # Females tend to shop more across age ranges, with peaks at 25-35 and 45-55
        age_groups = [
            (18, 25, 0.15),  # Young adults
            (25, 35, 0.30),  # Primary shopping demographic
            (35, 45, 0.25),  # Family-focused
            (45, 55, 0.20),  # Mature professionals
            (55, 65, 0.10)   # Seniors
        ]
    else:  # Male
        # Males tend to have different shopping patterns
        age_groups = [
            (18, 25, 0.20),  # Young adults (more casual/sports)
            (25, 35, 0.25),  # Young professionals
            (35, 45, 0.30),  # Peak earning years
            (45, 55, 0.15),  # Mature professionals
            (55, 65, 0.10)   # Seniors
        ]
    
    # Choose age group based on weights
    ranges, weights = zip(*[(r[:2], r[2]) for r in age_groups])
    chosen_range = random.choices(ranges, weights=weights)[0]
    return random.randint(chosen_range[0], chosen_range[1])

def generate_product_category_by_demographics(age, gender):
    """Generate product category based on age and gender patterns"""
    
    # Define probability matrices for different demographics
    if gender == 'Female':
        if 18 <= age <= 25:
            categories = {
                'Casual Wear': 0.35,
                'Sportswear': 0.25,
                'Formal Wear': 0.15,
                'Children Wear': 0.05,
                'Workwear': 0.20
            }
        elif 26 <= age <= 35:
            categories = {
                'Casual Wear': 0.25,
                'Sportswear': 0.20,
                'Formal Wear': 0.25,
                'Children Wear': 0.15,
                'Workwear': 0.15
            }
        elif 36 <= age <= 45:
            categories = {
                'Casual Wear': 0.20,
                'Sportswear': 0.15,
                'Formal Wear': 0.20,
                'Children Wear': 0.30,
                'Workwear': 0.15
            }
        else:  # 46+
            categories = {
                'Casual Wear': 0.30,
                'Sportswear': 0.10,
                'Formal Wear': 0.25,
                'Children Wear': 0.25,
                'Workwear': 0.10
            }
    else:  # Male
        if 18 <= age <= 25:
            categories = {
                'Casual Wear': 0.40,
                'Sportswear': 0.35,
                'Formal Wear': 0.10,
                'Children Wear': 0.05,
                'Workwear': 0.10
            }
        elif 26 <= age <= 35:
            categories = {
                'Casual Wear': 0.25,
                'Sportswear': 0.25,
                'Formal Wear': 0.20,
                'Children Wear': 0.10,
                'Workwear': 0.20
            }
        elif 36 <= age <= 45:
            categories = {
                'Casual Wear': 0.20,
                'Sportswear': 0.20,
                'Formal Wear': 0.25,
                'Children Wear': 0.20,
                'Workwear': 0.15
            }
        else:  # 46+
            categories = {
                'Casual Wear': 0.35,
                'Sportswear': 0.15,
                'Formal Wear': 0.25,
                'Children Wear': 0.15,
                'Workwear': 0.10
            }
    
    return random.choices(list(categories.keys()), weights=list(categories.values()))[0]

def generate_quantity_by_category_and_age(category, age):
    """Generate realistic quantity based on product category and age"""
    base_quantities = {
        'Children Wear': [1, 2, 3, 4, 5],  # Parents often buy multiple items
        'Casual Wear': [1, 2, 3, 4],       # Moderate quantities
        'Formal Wear': [1, 2, 3],          # Usually fewer, more expensive items
        'Sportswear': [1, 2, 3, 4, 5],     # Can vary widely
        'Workwear': [1, 2, 3, 4]           # Professional needs
    }
    
    # Adjust quantities based on age (older people might buy more at once)
    if age >= 40:
        if category == 'Children Wear':
            return random.choices([2, 3, 4, 5, 6], weights=[0.1, 0.2, 0.3, 0.3, 0.1])[0]
        else:
            return random.choices([1, 2, 3, 4], weights=[0.2, 0.3, 0.3, 0.2])[0]
    else:
        return random.choice(base_quantities[category])

def generate_price_by_category(category):
    """Generate realistic prices based on product category"""
    price_ranges = {
        'Children Wear': [25, 30, 50, 75, 100],
        'Casual Wear': [25, 30, 50, 75, 100, 150],
        'Formal Wear': [50, 75, 100, 150, 200, 300, 500],
        'Sportswear': [25, 30, 50, 75, 100, 150],
        'Workwear': [50, 75, 100, 150, 200, 300, 500]
    }
    
    weights = {
        'Children Wear': [0.2, 0.3, 0.3, 0.15, 0.05],
        'Casual Wear': [0.2, 0.25, 0.25, 0.15, 0.1, 0.05],
        'Formal Wear': [0.05, 0.1, 0.2, 0.25, 0.2, 0.15, 0.05],
        'Sportswear': [0.15, 0.2, 0.3, 0.2, 0.1, 0.05],
        'Workwear': [0.05, 0.1, 0.15, 0.25, 0.25, 0.15, 0.05]
    }
    
    return random.choices(price_ranges[category], weights=weights[category])[0]

def generate_seasonal_date():
    """Generate dates with seasonal patterns for clothing purchases"""
    # Define seasonal weights (more shopping in certain months)
    seasonal_weights = {
        1: 0.08,   # January (New Year sales)
        2: 0.06,   # February
        3: 0.09,   # March (Spring preparation)
        4: 0.10,   # April (Spring shopping)
        5: 0.09,   # May
        6: 0.08,   # June
        7: 0.07,   # July
        8: 0.08,   # August (Back to school)
        9: 0.09,   # September (Fall preparation)
        10: 0.10,  # October (Fall shopping)
        11: 0.12,  # November (Black Friday, holiday prep)
        12: 0.04   # December (Holiday focused, less clothing)
    }
    
    # Choose month based on seasonal weights
    month = random.choices(list(seasonal_weights.keys()), weights=list(seasonal_weights.values()))[0]
    
    # Generate random day for that month
    if month in [1, 3, 5, 7, 8, 10, 12]:
        day = random.randint(1, 31)
    elif month in [4, 6, 9, 11]:
        day = random.randint(1, 30)
    else:  # February
        day = random.randint(1, 28)
    
    # Use 2023 as the year to match existing data
    return f"2023-{month:02d}-{day:02d}"

def validate_dataset_structure(df):
    """Validate that the dataset has the correct structure and field names"""
    required_columns = [
        'Transaction ID', 'Date', 'Customer ID', 'Gender', 'Age', 
        'Product Category', 'Quantity', 'Price per Unit', 'Total Amount'
    ]
    
    missing_columns = [col for col in required_columns if col not in df.columns]
    if missing_columns:
        raise ValueError(f"Missing required columns: {missing_columns}")
    
    # Validate data types and ranges
    if not df['Age'].between(18, 70).all():
        print("Warning: Some ages are outside expected range (18-70)")
    
    if not df['Quantity'].between(1, 10).all():
        print("Warning: Some quantities are outside expected range (1-10)")
    
    if df['Total Amount'].isnull().any():
        print("Warning: Some Total Amount values are null")
    
    # Check for required product categories
    expected_categories = ['Children Wear', 'Casual Wear', 'Formal Wear', 'Sportswear', 'Workwear']
    missing_categories = [cat for cat in expected_categories if cat not in df['Product Category'].unique()]
    if missing_categories:
        print(f"Warning: Missing product categories: {missing_categories}")
    
    print("Dataset structure validation completed.")
    return True
    """Expand the dataset to target number of records"""
    
    existing_records = len(original_df)
    new_records_needed = target_records - existing_records
    
    print(f"Current records: {existing_records}")
    print(f"Target records: {target_records}")
    print(f"New records to generate: {new_records_needed}")
    
    # Analyze existing patterns
    patterns = analyze_patterns(original_df)
    print("Analyzed existing patterns...")
    
    # Get the last customer ID and transaction ID
    last_customer_id = int(original_df['Customer ID'].str.replace('CUST', '').max())
    last_transaction_id = original_df['Transaction ID'].max()
    
    new_records = []
    
    for i in range(new_records_needed):
        # Generate customer demographics
        gender = random.choices(['Male', 'Female'], 
                              weights=[patterns['gender_dist'].get('Male', 0.5), 
                                     patterns['gender_dist'].get('Female', 0.5)])[0]
        
        age = generate_realistic_age(gender)
        category = generate_product_category_by_demographics(age, gender)
        quantity = generate_quantity_by_category_and_age(category, age)
        price_per_unit = generate_price_by_category(category)
        date = generate_seasonal_date()
        
        # Create new record
        new_record = {
            'Transaction ID': last_transaction_id + i + 1,
            'Date': date,
            'Customer ID': f'CUST{last_customer_id + i + 1:03d}',
            'Gender': gender,
            'Age': age,
            'Product Category': category,
            'Quantity': quantity,
            'Price per Unit': price_per_unit,
            'Total Amount': quantity * price_per_unit
        }
        
        new_records.append(new_record)
        
        # Progress indicator
        if (i + 1) % 100 == 0:
            print(f"Generated {i + 1}/{new_records_needed} records...")
    
    # Create new dataframe with new records
    new_df = pd.DataFrame(new_records)
    
    # Combine with original data
    expanded_df = pd.concat([original_df, new_df], ignore_index=True)
    
    # Shuffle the dataset to make it more realistic
    expanded_df = expanded_df.sample(frac=1, random_state=42).reset_index(drop=True)
    
    # Update Transaction IDs to be sequential after shuffling
    expanded_df['Transaction ID'] = range(1, len(expanded_df) + 1)
    
    return expanded_df

def main():
    print("Loading existing dataset...")
    original_df = load_existing_data()
    
    print("Expanding dataset...")
    expanded_df = expand_dataset(original_df, target_records=2100)  # Target 2100 for some buffer
    
    # Save the expanded dataset
    output_file = '../datasets/segmentation_dataset_expanded.csv'
    expanded_df.to_csv(output_file, index=False)
    
    print(f"\nDataset expansion complete!")
    print(f"Original records: {len(original_df)}")
    print(f"New records: {len(expanded_df)}")
    print(f"Saved to: {output_file}")
    
    # Print some statistics
    print("\n=== EXPANDED DATASET STATISTICS ===")
    print(f"Gender distribution:")
    print(expanded_df['Gender'].value_counts(normalize=True))
    print(f"\nAge distribution:")
    print(f"Mean age: {expanded_df['Age'].mean():.1f}")
    print(f"Age range: {expanded_df['Age'].min()} - {expanded_df['Age'].max()}")
    print(f"\nProduct category distribution:")
    print(expanded_df['Product Category'].value_counts(normalize=True))
    print(f"\nAverage quantity per transaction: {expanded_df['Quantity'].mean():.2f}")
    print(f"Average transaction value: ${expanded_df['Total Amount'].mean():.2f}")

if __name__ == '__main__':
    main()
