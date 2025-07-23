<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

class RecommendationService
{
    /**
     * Get gender-based product recommendations for a user
        private function getRecommendationReason($productName, $gender): string
    {
        // Gender-specific recommendations
        if ($gender === 'female') {
            if (stripos($productName, 'Tank') !== false) {
                return "Perfect for casual comfort and versatility";
            } elseif (stripos($productName, 'Dress Shirt') !== false) {
                return "Professional elegance for work and formal occasions";
            } elseif (stripos($productName, 'Romper') !== false) {
                return "Adorable and comfortable for little ones";
            } elseif (stripos($productName, 'Performance Polo') !== false) {
                return "Stylish performance wear for active lifestyles";
            }
        } else {
            if (stripos($productName, 'Athletic') !== false) {
                return "Built for peak performance and comfort";
            } elseif (stripos($productName, 'Dry-Fit') !== false) {
                return "Advanced moisture-wicking technology";
            } elseif (stripos($productName, 'Compression') !== false) {
                return "Enhanced support for athletic activities";
            } elseif (stripos($productName, 'Performance Polo') !== false) {
                return "Professional athletic wear for any occasion";
            }
        }
        
        return "Highly recommended for your style preferences";
    }
    
    /**
     * Get gender-based product recommendations for a user
     */
    public function getRecommendationsForUser(User $user, int $limit = 4): array
    {
        $gender = strtolower($user->gender ?? 'male');
        
        // Get specific product recommendations based on gender
        $recommendedProductNames = $this->getGenderBasedProductNames($gender);
        
        // Find products matching the recommended names
        $recommendations = [];
        
        foreach ($recommendedProductNames as $productName) {
            $product = Product::where('name', 'LIKE', "%{$productName}%")
                ->where('quantity_available', '>', 0)
                ->first();
            
            if ($product) {
                $recommendations[] = [
                    'product' => $product,
                    'reason' => $this->getRecommendationReason($productName, $gender)
                ];
            }
        }
        
        // If we don't have enough specific products, fill with similar category products
        if (count($recommendations) < 4) {
            $recommendations = $this->fillWithSimilarProducts($recommendations, $gender, 4);
        }
        
        return array_slice($recommendations, 0, $limit);
    }
    
    /**
     * Get product names based on gender
     */
    private function getGenderBasedProductNames($gender): array
    {
        if ($gender === 'female') {
            return [
                'Tiny Tank',
                'Classic Dress Shirt', 
                'Baby Romper Shirt',
                'Performance Polo'
            ];
        } else { // male or other defaults to male recommendations
            return [
                'Athletic Shirt',
                'Dry-Fit Shirt',
                'Compression Shirt', 
                'Performance Polo'
            ];
        }
    }
    
    /**
     * Fill recommendations with similar products if specific ones aren't available
     */
    private function fillWithSimilarProducts($existingRecommendations, $gender, $targetCount): array
    {
        $existingProductIds = collect($existingRecommendations)->pluck('product.id')->toArray();
        
        // Get fallback products based on gender preferences
        $fallbackQuery = Product::where('quantity_available', '>', 0)
            ->whereNotIn('id', $existingProductIds);
        
        if ($gender === 'female') {
            // For females, prefer casual, formal, and children wear
            $fallbackQuery->whereHas('shirtCategory', function($query) {
                $query->where('category', 'LIKE', '%Casual%')
                      ->orWhere('category', 'LIKE', '%Formal%')
                      ->orWhere('category', 'LIKE', '%Children%');
            });
        } else {
            // For males, prefer sports and casual wear
            $fallbackQuery->whereHas('shirtCategory', function($query) {
                $query->where('category', 'LIKE', '%Sport%')
                      ->orWhere('category', 'LIKE', '%Casual%')
                      ->orWhere('category', 'LIKE', '%Work%');
            });
        }
        
        $fallbackProducts = $fallbackQuery->limit($targetCount - count($existingRecommendations))->get();
        
        foreach ($fallbackProducts as $product) {
            $existingRecommendations[] = [
                'product' => $product,
                'reason' => "Popular choice for {$gender} customers"
            ];
        }
        
        return $existingRecommendations;
    }
    
    /**
     * Calculate age from date of birth
     */
    private function calculateAge($dateOfBirth): int
    {
        if (!$dateOfBirth) return 25; // Default age
        
        return Carbon::parse($dateOfBirth)->age;
    }
    
    /**
     * Get category preferences based on age and gender
     */
    private function getCategoryPreferences(int $age, string $gender): array
    {
        $preferences = [];
        
        if ($gender === 'female') {
            if ($age >= 18 && $age <= 25) {
                $preferences = [
                    'Casual' => 0.35,
                    'Sport' => 0.25,
                    'Work' => 0.20,
                    'Formal' => 0.15,
                    'Children' => 0.05
                ];
            } elseif ($age >= 26 && $age <= 35) {
                $preferences = [
                    'Casual' => 0.25,
                    'Formal' => 0.25,
                    'Sport' => 0.20,
                    'Children' => 0.15,
                    'Work' => 0.15
                ];
            } elseif ($age >= 36 && $age <= 50) {
                $preferences = [
                    'Children' => 0.30,
                    'Formal' => 0.20,
                    'Casual' => 0.20,
                    'Sport' => 0.15,
                    'Work' => 0.15
                ];
            } else { // 51+
                $preferences = [
                    'Casual' => 0.30,
                    'Formal' => 0.25,
                    'Children' => 0.25,
                    'Sport' => 0.10,
                    'Work' => 0.10
                ];
            }
        } else { // Male
            if ($age >= 18 && $age <= 25) {
                $preferences = [
                    'Casual' => 0.40,
                    'Sport' => 0.35,
                    'Work' => 0.10,
                    'Formal' => 0.10,
                    'Children' => 0.05
                ];
            } elseif ($age >= 26 && $age <= 35) {
                $preferences = [
                    'Casual' => 0.25,
                    'Sport' => 0.25,
                    'Formal' => 0.20,
                    'Work' => 0.20,
                    'Children' => 0.10
                ];
            } elseif ($age >= 36 && $age <= 50) {
                $preferences = [
                    'Formal' => 0.25,
                    'Sport' => 0.20,
                    'Children' => 0.20,
                    'Casual' => 0.20,
                    'Work' => 0.15
                ];
            } else { // 51+
                $preferences = [
                    'Casual' => 0.35,
                    'Formal' => 0.25,
                    'Children' => 0.15,
                    'Sport' => 0.15,
                    'Work' => 0.10
                ];
            }
        }
        
        return $preferences;
    }
}
