<?php

use App\Models\BuoyApp;
use Illuminate\Database\Seeder;
use App\Contracts\BuoyServiceContract as BuoyService;

class BuoySeeder extends Seeder
{
    private $buoyService;

    /**
     * BuoySeeder constructor.
     * @param \App\Services\Buoys\BuoyService | BuoyService $buoyService
     */
    public function __construct(BuoyService $buoyService)
    {
        $this->buoyService = $buoyService;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->buoyService->getBuoyClasses() as $buoyClass => $buoyData) {
            $buoyApp = BuoyApp::firstOrNew([
                'buoy_class' => $buoyClass,
            ]);

            $buoyApp->fill([
                'title' => $buoyData['title'],
                'ports' => $buoyData['ports'],
                'options' => $buoyData['options'],
            ]);

            if(empty($buoyApp->description)) {
               $buoyApp->description = $buoyData['description'];
            }

            $buoyApp->save();

            if(empty($buoyApp->categories)) {
                $category = \App\Models\Category::where('name', $buoyData['category'])->firstOrFail();

                $buoyApp->categories()->save($category);
            }

        }
    }
}
