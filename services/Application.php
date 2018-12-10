<?php
/**
 * Created by PhpStorm.
 * User: sanzhar
 * Date: 06.12.18
 * Time: 10:28
 */

namespace App\services;

use App\controllers\IndexController;
use Dotenv\Dotenv;
use MysqliDb;
use Pheanstalk\Pheanstalk;
use Predis\Client;

/**
 * Class Application
 * @package services
 */
class Application
{
    /**
     * @var \MysqliDb $database
     */
    public $database;
    /**
     * @var \Pheanstalk\Pheanstalk
     */
    public $queue;
    /**
     * @var Client
     */
    public $redis;

    /**
     * Application constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->database = new MysqliDb($config['db']['host'], $config['db']['username'], $config['db']['password'], $config['db']['databaseName']);
        $this->queue = new Pheanstalk($config['queue']['host'], $config['queue']['port']);
        $this->redis = new Client([
            'scheme' => $config['redis']['scheme'],
            'host' => $config['redis']['host'],
            'port' => $config['redis']['port'],
        ]);
    }

    /**
     * @throws \Exception
     */
    public function run()
    {
        if ($_SERVER['REQUEST_URI'] == '/') {
            echo (new IndexController())->actionIndex();
            exit;
        }

        if (strpos($_SERVER['REQUEST_URI'], '/tournament/remove/') !== false) {
            $id = str_replace('/tournament/remove/', '', $_SERVER['REQUEST_URI']);
            echo (new IndexController())->actionRemoveTournament($id);
            exit;
        }

        if (strpos($_SERVER['REQUEST_URI'], '/tournament/create/') !== false) {
            echo (new IndexController())->actionCreateTournament();
            exit;
        }

        if (strpos($_SERVER['REQUEST_URI'], '/tournament/simulate/') !== false) {
            $id = str_replace('/tournament/simulate/', '', $_SERVER['REQUEST_URI']);
            echo (new IndexController())->actionSimulateTournament($id, $this->redis, $this->queue);
            exit;
        }

        if (strpos($_SERVER['REQUEST_URI'], '/tournament/results/') !== false) {
            $id = str_replace('/tournament/results/', '', $_SERVER['REQUEST_URI']);
            echo (new IndexController())->actionTournamentResults($id);
            exit;
        }

        echo (new IndexController())->actionError(404, 'Page not found');
        exit;
    }

    /**
     * @throws \Exception
     */
    public function migrate()
    {
        $hasTables = $this->database->query('SHOW TABLES;');
        if (empty($hasTables)) {
            $this->database->query("CREATE TABLE `tournament` (
              `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `name` varchar(255) NOT NULL,
              `is_finished` int(1) default 0 not null 
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB");

            $this->database->query("CREATE TABLE `team` (
              `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `name` varchar(255) NOT NULL
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB");

            $this->database->query("CREATE TABLE `tournament_team` (
              `tournament_id` int(11) NOT NULL,
              `team_id` int(11) NOT NULL,
              `group` varchar(1) default 'A'
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB");

            $this->database->query("ALTER TABLE `tournament_team` ADD CONSTRAINT `fk-tournament-to-team` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT");
            $this->database->query("ALTER TABLE `tournament_team` ADD CONSTRAINT `fk-team-to-tournament` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT");

            $this->database->query("CREATE TABLE `game` (
              `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `tournament_id` int(11) NOT NULL,
              `team_id` int(11) NOT NULL,
              `versus_team_id` int(11) NOT NULL,
              `first_team_score` int(11) NULL DEFAULT NULL,
              `second_team_score` int(11) NULL DEFAULT NULL,
              `stage` int(11) NOT NULL
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB");

            $this->database->query("ALTER TABLE `game` ADD CONSTRAINT `fk-game-to-tournament` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT");
            $this->database->query("ALTER TABLE `game` ADD CONSTRAINT `fk-game-to-first-team` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT");
            $this->database->query("ALTER TABLE `game` ADD CONSTRAINT `fk-game-to-second-team` FOREIGN KEY (`versus_team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT");

        } else {
            throw new \Exception('Migration already set');
        }
    }

    public function queue()
    {
        while (true) {
            if (!$this->queue->getConnection()->isServiceListening()) {
                echo "Connection failed, please wait... \n";
                sleep(5);

                continue;
            }

            $job = $this->queue
                ->watch('tournament-simulate')
                ->ignore('default')
                ->reserve();
            $tournamentId = json_decode($job->getData(), true);

            try {
                (new TournamentSimulateService($this->database, $this->redis, $tournamentId))->play();

                $this->queue->delete($job);
                $this->redis->del(['tournament-lock:'.$tournamentId]);
                $this->database->query("UPDATE tournament SET is_finished = 1 WHERE id = ".$tournamentId);
                echo "Tournament finished! \n";

            } catch (\Exception $e) {
                echo $e->getTraceAsString();
            }
        }
    }
}