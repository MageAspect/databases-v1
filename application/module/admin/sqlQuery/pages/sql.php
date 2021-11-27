<?php
/** @var array $PAGE_DATA */

/** @var SqlHistoryEntry[] $slqHistory */

use application\module\admin\sqlQuery\SqlHistoryEntry;


$slqHistory = $PAGE_DATA['sql-history'];
$slqError = $PAGE_DATA['sql-error'];
$slqResults = $PAGE_DATA['sql-results'];
?>

<div class="application-content">
    <div class="main-content-wrapper ui-scroll">
        <div class="main-content-header">
            <div class="main-content-header-title">
                <div class="main-content-header-title_icon">
                    <i class="fas fa-database"></i>
                </div>
                <div class="main-content-header-title_desc">SQL запрос</div>
            </div>
        </div>
        <form method="post" class="sql">
            <div class="sql-expression">
                    <textarea name="sql" rows="15"><?php echo $_POST['sql'] ?></textarea>
                <button class="button  sql-expression-button">Выполнить</button>
            </div>
            <div class="sql-output">
                <h2 class="sql-output-title">Результат исполнения</h2>
                <div class="sql-output-body">
                    <?php if (!isset($slqError) && !empty($slqResults)): ?>
                        <table>
                            <tr>
                                <?php foreach ($slqResults[0] as $colName => $v): ?>
                                    <td><?= $colName ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php foreach ($slqResults as $result): ?>
                                <tr>
                                    <?php foreach ($result as $column): ?>
                                        <td><?= $column ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <?= $slqError ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="sql-output">
                <h2 class="sql-output-title">Предыдущие запросы</h2>
                <div class="main-grid-table sql-history ui-scroll">
                    <div class="main-grid-table-row main-grid-table-row-head">
                        <div class="main-grid-table-row-column">id</div>
                        <div class="main-grid-table-row-column">SQL</div>
                        <div class="main-grid-table-row-column">Дата выполнения</div>
                    </div>
                    <div class="main-grid-table-row">
                        <?php foreach ($slqHistory as $entry): ?>
                            <div class="main-grid-table-row-column"><?= $entry->id ?></div>
                            <div class="main-grid-table-row-column"><?= $entry->sql ?></div>
                            <div class="main-grid-table-row-column"><?= $entry->executionDateTime->format('H:i:s d.m.Y') ?></div>
                        <?php endforeach; ?>
                    </div>


                </div>
            </div>
        </form>
    </div>
</div>
