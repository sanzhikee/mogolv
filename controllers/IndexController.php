<?php
/**
 * Created by PhpStorm.
 * User: sanzhikee
 * Date: 2018-12-09
 * Time: 21:01
 */

namespace App\controllers;

use App\components\Controller;
use App\models\Game;
use App\models\Tournament;
use Pheanstalk\Pheanstalk;
use Predis\Client;

/**
 * Class IndexController
 * @package App\controllers
 */
class IndexController extends Controller
{
    /**
     * @return false|string
     * @throws \Exception
     */
    public function actionIndex()
    {
        $tournaments = (new Tournament())->getAll();

        return $this->render('index', [
            'tournaments' => $tournaments
        ]);
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function actionRemoveTournament($id)
    {
        (new Tournament())->delete('id', $id);

        $this->redirect('/');
    }

    /**
     * @throws \Exception
     */
    public function actionCreateTournament()
    {
        (new Tournament())->insert($_POST);

        $this->redirect('/');
    }

    /**
     * @param integer $id
     * @param Client $redis
     * @param Pheanstalk $queue
     */
    public function actionSimulateTournament($id, $redis, $queue)
    {
        $orderId = $queue
            ->useTube('tournament-simulate')
            ->put(json_encode($id));
        $redis->set('tournament-lock:' . $id, $orderId);

        $this->redirect('/');
    }

    /**
     * @param $id
     * @return false|string
     * @throws \Exception
     */
    public function actionTournamentResults($id)
    {
        $tournament = (new Tournament())->getOne('id', $id);
        $games = (new Game())->getAll('tournament_id', $id);

        return $this->render('games', [
            'tournament' => $tournament,
            'games' => $games
        ]);
    }

    /**
     * @param $status
     * @param $code
     * @return false|string
     */
    public function actionError($status, $message)
    {
        return $this->render('error', [
            'status' => $status,
            'message' => $message
        ]);
    }
}