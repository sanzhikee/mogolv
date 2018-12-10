<?php
/**
 * Created by PhpStorm.
 * User: sanzhikee
 * Date: 2018-12-10
 * Time: 00:28
 */

namespace App\models;

use App\components\interfaces\DatabaseModelInterface;
use App\components\Model;
use App\services\TournamentSimulateService;

/**
 * Class Game
 * @package App\models
 */
class Game extends Model implements DatabaseModelInterface
{
    public $tableName = 'game';

    /**
     * @param $tournamentId
     * @param $team_id
     * @return array
     * @throws \Exception
     */
    public function getTeamGroupGames($tournamentId, $team_id)
    {
        return $this->db->rawQuery("SELECT * FROM game WHERE tournament_id = " . $tournamentId . " AND (team_id = " . $team_id . " OR versus_team_id = " . $team_id . ") AND stage=1");
    }

    /**
     * @param $tournamentId
     * @return array
     * @throws \Exception
     */
    public function getQuarterFinalGames($tournamentId)
    {
        return $this->db->rawQuery("SELECT * FROM game WHERE tournament_id = " . $tournamentId . " AND stage=2");
    }

    /**
     * @param $tournamentId
     * @return array
     * @throws \Exception
     */
    public function getSemiFinalGames($tournamentId)
    {
        return $this->db->rawQuery("SELECT * FROM game WHERE tournament_id = " . $tournamentId . " AND stage=3");
    }

    public static function getStageName($stage)
    {
        $names = [
            TournamentSimulateService::GROUP_STAGE => 'Group',
            TournamentSimulateService::QUARTER_FINAL_STAGE => 'Quarter Final',
            TournamentSimulateService::SEMI_FINAL_STAGE => 'Semi Final',
            TournamentSimulateService::FINAL_STAGE => 'Final',
        ];

        return $names[$stage];
    }
}