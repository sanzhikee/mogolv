<?php
/**
 * Created by PhpStorm.
 * User: sanzhikee
 * Date: 2018-12-10
 * Time: 00:16
 */

namespace App\models;


use App\components\interfaces\DatabaseModelInterface;
use App\components\Model;

/**
 * Class TournamentTeam
 * @package App\models
 *
 * @property int $tournament_id
 * @property int $team_id
 * @property string $group
 */
class TournamentTeam extends Model implements DatabaseModelInterface
{
    public $tableName = 'tournament_team';
}