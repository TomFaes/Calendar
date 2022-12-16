<?php

namespace Tests\Unit\Service;

use App\Models\GroupUser;
use Tests\TestCase;

use App\Models\Season;
use App\Models\User;
use App\Services\GroupUserService;
use App\Services\SeasonGeneratorService\GeneratorFactory;
use App\Services\SeasonService;
use Carbon\Carbon;
use Database\Seeders\GeneratorSeeder;

class SeasonTest extends TestCase
{
    protected $seasonService;
    protected $newSeason;

    public function setUp() : void
    {
        parent::setUp();
        $this->seed(GeneratorSeeder::class);

        $this->seasonService = new SeasonService();
    }

    protected function createGeneratedSeason($public = 0){
        $seasonService = new SeasonService();
        $user = User::first();
        //create new season
        $seasonData = Season::factory()->make(['begin' => Carbon::now()->addDays(1)->format('Y-m-d')])->toArray();
        
        $seasonData['public'] = $public;
        $seasonData['admin_id'] = $user->id;
        $seasonData['day'] = $seasonService->getDutchDay($seasonData['begin']);
        $this->newSeason = Season::create($seasonData);

        //add a registered user to a group
        $groupUserService = new GroupUserService();
        $groupUser = $groupUserService->regenerateGroupUserCode(GroupUser::first());
        
        $groupUser = $groupUserService->joinGroup($groupUser->code, $user->id);
        
        $seasonGenerator = GeneratorFactory::generate($this->newSeason->type);
        $generatedSeason = $seasonGenerator->generateSeason($this->newSeason);
        $seasonGenerator->saveSeason(json_encode($generatedSeason));
    }

    public function test_get_active_seasons()
    {
        $userId = User::all()->first()->id;
        $this->createGeneratedSeason();
        $seasons = $this->seasonService->getActiveSeasons($userId);
        $this->assertEquals(count($seasons), 1);
    }

    public function test_check_if_season_is_started()
    {
        $season = Season::first();
        $this->assertTrue($this->seasonService->checkIfSeasonIsStarted($season));
        
        $season->begin = Carbon::now()->addDays(2)->format('Y-m-d');
        $season->save();
        $this->assertFalse($this->seasonService->checkIfSeasonIsStarted($season));
    }

    public function test_get_dutch_day()
    {
        $this->assertEquals($this->seasonService->getDutchDay("Monday"), "ma");
        $this->assertEquals($this->seasonService->getDutchDay("Tuesday"), "di");
        $this->assertEquals($this->seasonService->getDutchDay("Wednesday"), "wo");
        $this->assertEquals($this->seasonService->getDutchDay("Thursday"), "do");
        $this->assertEquals($this->seasonService->getDutchDay("Friday"), "vr");
        $this->assertEquals($this->seasonService->getDutchDay("Saturday"), "za");
        $this->assertEquals($this->seasonService->getDutchDay("Sunday"), "zo");
    }
    

    
}