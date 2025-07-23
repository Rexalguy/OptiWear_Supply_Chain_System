<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

class RecommendationService
{
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
     * Get recommendation reason text
     */
    private function getRecommendationReason($productName, $gender): string
    {
        $reasons = [
            'Tiny Tank' => 'Perfect for casual comfort and style',
            'Classic Dress Shirt' => 'Essential for professional and formal occasions',
            'Baby Romper Shirt' => 'Great for parents and gift-giving',
            'Performance Polo' => 'Versatile for both casual and semi-formal wear',
            'Athletic Shirt' => 'Ideal for workouts and active lifestyle',
            'Dry-Fit Shirt' => 'Perfect for sports and moisture management',
            'Compression Shirt' => 'Excellent for athletic performance and recovery'
        ];
        
        return $reasons[$productName] ?? "Recommended for {$gender} customers";
    }
}
