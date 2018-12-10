<?php
/**
 * Created by PhpStorm.
 * User: sanzhikee
 * Date: 2018-12-10
 * Time: 00:01
 */

namespace App\models;


use App\components\interfaces\DatabaseModelInterface;
use App\components\Model;
use App\services\TournamentSimulateService;

/**
 * Class Team
 * @package App\models
 *
 * @property int $id
 * @property string $name
 */
class Team extends Model implements DatabaseModelInterface
{
    /**
     * @var string
     */
    public $tableName = 'team';

    /**
     * @param $tournamentId
     * @return mixed
     * @throws \Exception
     */
    public function getTournamentWinner($tournamentId)
    {
        $final = $this->db->rawQuery("SELECT * FROM team LEFT JOIN game g on team.id = g.team_id WHERE g.tournament_id=".$tournamentId." AND g.stage=".TournamentSimulateService::FINAL_STAGE." AND first_team_score > second_team_score");

        if(empty($final)){
            $final = $this->db->rawQuery("SELECT * FROM team LEFT JOIN game g on team.id = g.versus_team_id WHERE g.tournament_id=".$tournamentId." AND g.stage=".TournamentSimulateService::FINAL_STAGE." AND second_team_score > first_team_score");
        }

        return $final[0];
    }
}