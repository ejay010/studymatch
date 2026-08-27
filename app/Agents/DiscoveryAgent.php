<?php

namespace App\Agents;

use App\Models\EducatorProfile;

class DiscoveryAgent
{
    /**
     * Perform a traditional search for EducatorProfiles based on explicit UI filters.
     * 
     * @param string $keyword
     * @param string|null $subject
     * @param int|null $gradeLevel
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search(string $keyword = '', ?string $subject = null, ?int $gradeLevel = null)
    {
        // Use Laravel Scout (Meilisearch) for the base keyword search
        $query = EducatorProfile::search($keyword);

        // In a fully configured Meilisearch setup, we would chain filters:
        // if ($subject) {
        //     $query->where('subject', $subject);
        // }
        // if ($gradeLevel) {
        //     $query->where('grade_level', $gradeLevel);
        // }
        
        return $query->get();
    }
}

