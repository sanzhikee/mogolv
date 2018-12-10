<?php
/**
 * Created by PhpStorm.
 * User: sanzhikee
 * Date: 2018-12-10
 * Time: 08:46
 *
 * @var \App\models\Tournament $tournament
 * @var \App\models\Game[] $games
 */
?>

<div class="container">
    <h1><?= $tournament->name ?></h1>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Home Team</th>
            <th>Score</th>
            <th>Guest Team</th>
            <th>Stage</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($games as $game) { ?>
            <tr>
                <td>
                    <?= $game->id ?>
                </td>
                <td>
                    <?= (new \App\models\Team())->getOne('id', $game->team_id)->name ?>
                </td>
                <td>
                    <?= $game->first_team_score . " : " . $game->second_team_score ?>
                </td>
                <td>
                    <?= (new \App\models\Team())->getOne('id', $game->versus_team_id)->name ?>
                </td>
                <td>
                    <?= \App\models\Game::getStageName($game->stage) ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
