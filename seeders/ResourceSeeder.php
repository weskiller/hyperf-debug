<?php


use App\Command\HeroImage;
use Hyperf\Database\Seeders\Seeder;

class ResourceSeeder extends Seeder
{
    public function run() :void
    {
        HeroImage::import();
    }
}