<?php
/**
 * Created by PhpStorm.
 * User: sanzhikee
 * Date: 2018-12-09
 * Time: 21:17
 *
 * @var \App\models\Tournament[] $tournaments
 */
?>
<div class="container">
    <h1>Tournaments</h1>

    <p>
        <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#createModal">Create new
            Tournament
        </button>
    </p>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Status</th>
            <th>Tournament Winner team</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tournaments

                       as $tournament) { ?>
            <tr>
                <td><?= $tournament->id ?></td>
                <td><?= $tournament->name ?></td>
                <td>
                    <?= \App\models\Tournament::getSimulationStatus($tournament->id, $tournament->is_finished) ?>
                </td>
                <td>
                    <?php if (\App\models\Tournament::getSimulationStatus($tournament->id, $tournament->is_finished) == \App\models\Tournament::FINISHED) {
                        echo (new \App\models\Team)->getTournamentWinner($tournament->id)['name'];
                    } else {
                        echo 'Not set';
                    } ?>
                </td>
                <td>
                    <?php if ($orderId = \App\models\Tournament::isTournamentSimulating($tournament->id)) {
                        echo $orderId;
                    } else { ?>
                        <a href="/tournament/remove/<?= $tournament->id ?>">
                            Remove
                        </a>
                        <?php if (\App\models\Tournament::getSimulationStatus($tournament->id, $tournament->is_finished) == \App\models\Tournament::FINISHED) { ?>
                            <br>
                            <a href="/tournament/results/<?= $tournament->id ?>">
                                Watch games
                            </a>
                        <?php } else { ?>
                            <br>
                            <a href="/tournament/simulate/<?= $tournament->id ?>">
                                Simulate
                            </a>
                        <?php }
                    } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>


<div class="modal fade" id="createModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create new Tournament</h4>
            </div>
            <form action="/tournament/create/" method="post">
                <div class="modal-body">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>

    </div>
</div>
