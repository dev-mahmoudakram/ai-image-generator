<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'title'       => 'Cinematic Portrait',
                'slug'        => 'cinematic-portrait',
                'description' => 'Transform your photo into a dramatic cinematic portrait.',
                'prompt_hint' => 'Dramatic cinematic lighting, film grain, shallow depth of field, anamorphic lens look, rich shadows.',
                'sort_order'  => 1,
                'is_active'   => true,
            ],
            [
                'title'       => 'Studio Fashion',
                'slug'        => 'studio-fashion',
                'description' => 'Professional high-fashion studio shoot.',
                'prompt_hint' => 'High-fashion editorial, clean white studio backdrop, professional lighting, elegant styling.',
                'sort_order'  => 2,
                'is_active'   => true,
            ],
            [
                'title'       => 'Neon City Night',
                'slug'        => 'neon-city-night',
                'description' => 'Vibrant neon-lit urban night scene.',
                'prompt_hint' => 'Neon signs reflecting on wet streets, cyberpunk atmosphere, vibrant magenta and cyan lighting.',
                'sort_order'  => 3,
                'is_active'   => true,
            ],
        ];

        foreach ($templates as $data) {
            Template::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'disk'       => 'public',
                    'image_path' => 'templates/placeholder.jpg',
                ])
            );
        }
    }
}
