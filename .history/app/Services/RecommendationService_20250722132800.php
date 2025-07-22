<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

class RecommendationService
{
    /**
     * Get personalized product recommendations for a user
     */
    public function getRecommendationsForUser(User $user, int $limit = 8): array
    {
        $age = $this->calculateAge($user->date_of_birth);
        $gender = strtolower($user->gender ?? 'male');
        
        // Get category preferences based on demographics
        $categoryPreferences = $this->getCategoryPreferences($age, $gender);
        
        // Get products for each preferred category
        $recommendations = [];
        
        foreach ($categoryPreferences as $categoryName => $weight) {
            $products = Product::whereHas('shirtCategory', function($query) use ($categoryName) {
                $query->where('category', 'LIKE', "%{$categoryName}%");
            })
            ->where('quantity_available', '>', 0)
            ->inRandomOrder()
            ->limit(ceil($limit * $weight))
            ->get();
            
            foreach ($products as $product) {
                $recommendations[] = [
                    'product' => $product,
                    'weight' => $weight,
                    'reason' => $this->getRecommendationReason($categoryName, $age, $gender)
                ];
            }
        }
        
        // Sort by weight and limit results
        usort($recommendations, fn($a, $b) => $b['weight'] <=> $a['weight']);
        
        return array_slice($recommendations, 0, $limit);
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
    
    /**
     * Get recommendation reason text
     */
    private function getRecommendationReason(string $category, int $age, string $gender): string
    {
        $reasons = [
            'Casual' => "Perfect for your lifestyle and age group",
            'Sport' => "Great for active individuals like you", 
            'Formal' => "Professional attire suitable for your demographic",
            'Work' => "Ideal workwear for professionals",
            'Children' => "Popular choice for parents and family-oriented shoppers"
        ];
        
        return $reasons[$category] ?? "Recommended for you";
    }
}
