<?php
/**
 * Created by PhpStorm.
 * User: sanzhikee
 * Date: 2018-12-09
 * Time: 22:07
 */

namespace App\models;


use App\components\interfaces\DatabaseModelInterface;
use App\components\Model;
use Predis\Client;

/**
 * Class Tournament
 * @package App\models
 *
 * @property integer $id
 * @property string $name
 * @property boolean $is_finished
 */
class Tournament extends Model implements DatabaseModelInterface
{
    const NOTSTARTED = 'Not started';
    const SIMULATING = 'Simulating';
    const FINISHED = 'Finished';
    /**
     * @var string
     */
    public $tableName = 'tournament';

    /**
     * @var Client
     */
    public static $redis;

    /**
     * Tournament constructor.
     */
    public function __construct()
    {
        $config = require(__DIR__ . '/../config/main.php');
        self::$redis = new Client([
            'scheme' => $config['redis']['scheme'],
            'host' => $config['redis']['host'],
            'port' => $config['redis']['port'],
        ]);

        parent::__construct();
    }

    /**
     * @param $id
     * @param $status
     * @return string
     */
    public static function getSimulationStatus($id, $status)
    {
        if (self::isTournamentSimulating($id)) {
            return self::SIMULATING;
        } else {
            if ($status) {
                return self::FINISHED;
            } else {
                return self::NOTSTARTED;
            }
        }
    }

    /**
     * @param $id
     * @return boolean
     */
    public static function isTournamentSimulating($id)
    {
        return self::$redis->get('tournament-lock:' . $id);
    }
}