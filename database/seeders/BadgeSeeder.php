<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'slug' => 'top-rated',
                'name' => 'Top Rated',
                'description' => 'Maintains a superior average rating above 4.8 stars from completed student gigs.',
                'icon_class' => 'bg-amber-500 border-amber-600 text-white'
            ],
            [
                'slug' => 'fast-responder',
                'name' => 'Fast Responder',
                'description' => 'Typically responds to peer messages or delivery updates within 30 minutes.',
                'icon_class' => 'bg-emerald-500 border-emerald-600 text-white'
            ],
            [
                'slug' => 'campus-legend',
                'name' => 'Campus Legend',
                'description' => 'Successfully fulfills and closes over 50 transaction orders on the platform.',
                'icon_class' => 'bg-purple-500 border-purple-600 text-white'
            ],
            [
                'slug' => 'academic-elite',
                'name' => 'Academic Elite',
                'description' => 'Delivers 15 or more verified, high-tier peer tutoring sessions.',
                'icon_class' => 'bg-blue-500 border-blue-600 text-white'
            ],
            [
                'slug' => 'speed-demon',
                'name' => 'Speed Demon',
                'description' => 'Consistently finishes and marks service deliveries within 24 hours of ordering.',
                'icon_class' => 'bg-rose-500 border-rose-600 text-white'
            ]
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(['slug' => $badge['slug']], $badge);
        }
    }
}