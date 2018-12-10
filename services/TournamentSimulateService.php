<?php
/**
 * Created by PhpStorm.
 * User: sanzhikee
 * Date: 2018-12-09
 * Time: 23:57
 */

namespace App\services;

use App\models\Game;
use App\models\Team;
use App\models\TournamentTeam;
use Faker\Factory;
use Predis\Client;

/**
 * Class TournamentSimulateService
 * @package App\services
 */
class TournamentSimulateService
{
    const GROUP_STAGE = 1;
    const QUARTER_FINAL_STAGE = 2;
    const SEMI_FINAL_STAGE = 3;
    const FINAL_STAGE = 4;

    /**
     * @var \MysqliDb
     */
    public $database;
    /**
     * @var Client
     */
    public $redis;
    /**
     * @var integer
     */
    public $tournamentId;

    public function __construct($database, $redis, $tournamentId)
    {
        $this->database = $database;
        $this->redis = $redis;
        $this->tournamentId = $tournamentId;
    }

    public function initTeams()
    {
        for ($i = 0; $i < 16; $i++) {
            $faker = Factory::create();
            $team = new Team();
            $team_id = $team->insert(['name' => $faker->city]);

            $tournament_team = new TournamentTeam();
            $group = $i < 8 ? "A" : "B";

            $tournament_team->insert(['tournament_id' => $this->tournamentId, 'team_id' => $team_id, 'group' => $group]);
        }
    }

    public function play()
    {
        $this->initTeams();

        $teams = (new TournamentTeam())->getAll('tournament_id', $this->tournamentId);
        foreach ($teams as $first_team) {
            foreach ($teams as $second_team) {
                if ($first_team->team_id == $second_team->team_id) {
                    continue;
                }

                if ($first_team->group != $second_team->group) {
                    continue;
                }

                $this->playGame($first_team->team_id, $second_team->team_id, self::GROUP_STAGE);
            }
        }

        $groupAWinners = [];
        $groupBWinners = [];

        foreach ($teams as $team) {
            $team->points = 0;
            $games = (new Game())->getTeamGroupGames($this->tournamentId, $team->team_id);
            foreach ($games as $game) {
                if ($game['team_id'] == $team->team_id) {
                    $team->points += $this->countPoints($game['first_team_score'], $game['second_team_score']);
                }

                if ($game['versus_team_id'] == $team->team_id) {
                    $team->points += $this->countPoints($game['second_team_score'], $game['first_team_score']);
                }
            }

            if ($team->group == 'A') {
                $groupAWinners[] = $team;
            }

            if ($team->group == 'B') {
                $groupBWinners[] = $team;
            }
        }

        usort($groupAWinners, [$this, 'comparePoints']);
        usort($groupBWinners, [$this, 'comparePoints']);

        $groupAWinners = array_reverse($groupAWinners);
        $groupBWinners = array_reverse($groupBWinners);

        for ($i = 0; $i < 4; $i++) {
            $this->playGame($groupAWinners[$i]->team_id, $groupBWinners[(4 - $i)]->team_id, self::QUARTER_FINAL_STAGE);
        }

        $games = (new Game())->getQuarterFinalGames($this->tournamentId);
        $semiFinales = [];
        foreach ($games as $game) {
            $semiFinales[] = $this->getDuelWinner($game);
        }

        $this->playGame($semiFinales[0], $semiFinales[1], self::SEMI_FINAL_STAGE);
        $this->playGame($semiFinales[2], $semiFinales[3], self::SEMI_FINAL_STAGE);

        $games = (new Game())->getSemiFinalGames($this->tournamentId);
        $finales = [];
        foreach ($games as $game) {
            $finales[] = $this->getDuelWinner($game);
        }

        $this->playGame($finales[0], $finales[1], self::FINAL_STAGE);
    }

    /**
     * @param $team_id
     * @param $versus_team_id
     * @param $stage
     * @return bool
     * @throws \Exception
     */
    public function playGame($team_id, $versus_team_id, $stage)
    {
        $faker = Factory::create();

        $game = new Game();
        return $game->insert(['tournament_id' => $this->tournamentId, 'team_id' => $team_id, 'versus_team_id' => $versus_team_id, 'first_team_score' => $faker->unique()->randomDigitNotNull, 'second_team_score' => $faker->unique()->randomDigitNotNull, 'stage' => $stage]);
    }

    /**
     * @param $score_first
     * @param $score_second
     * @return int
     */
    public function countPoints($score_first, $score_second)
    {
        if ($score_first > $score_second) {
            return 3;
        }

        if ($score_first == $score_second) {
            return 1;
        }

        if ($score_first < $score_second) {
            return 0;
        }
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    public function comparePoints($a, $b)
    {
        if ($a->points == $b->points) {
            return 0;
        }

        return ($a->points < $b->points) ? -1 : 1;
    }

    /**
     * @param $game
     * @return mixed
     */
    public function getDuelWinner($game)
    {
        if ($game['first_team_score'] > $game['second_team_score']) {
            return $game['team_id'];
        } else {
            return $game['versus_team_id'];
        }
    }
}