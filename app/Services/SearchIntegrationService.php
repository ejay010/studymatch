<?php

namespace App\Services;

use App\Models\EducatorProfile;

class SearchIntegrationService
{
    /**
     * Update the search index for a specific educator profile.
     */
    public function indexProfile(EducatorProfile $profile)
    {
        // Laravel Scout automatically syncs when records are updated,
        // but we can manually trigger it here if needed.
        $profile->searchable();
    }

    /**
     * Configure Meilisearch settings for the EducatorProfile index.
     * This sets up filterable and sortable attributes for the Discovery Agent.
     */
    public function setupIndex()
    {
        // Example of how we might configure Meilisearch settings via Scout
        // Typically run via a console command during deployment.
        
        /*
        $client = new \Meilisearch\Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $index = $client->index('educator_profiles');
        
        $index->updateFilterableAttributes([
            'grade_level',
            'hourly_rate',
            'is_verified'
        ]);
        */
    }
}
