<?php include ROOT . '/views/layouts/header.php'; ?>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-12">
                    <div class="history">
                        <h2>Последние победители</h2>
                        <hr>
                        <table border="1">
                            <tbody>
                            <tr>
                                <th><i class="fa fa-trophy"></i></th>
                                <th>Имя игрока</th>
                            </tr>
                            <?php $count = 0; foreach ($winners as $winner): ?>
                                <tr>
                                    <td><?php echo ++$count; ?></td>
                                    <td><?php echo $winner['nick']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include ROOT . '/views/layouts/footer.php'; ?>