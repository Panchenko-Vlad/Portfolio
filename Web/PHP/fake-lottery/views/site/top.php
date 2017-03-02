<?php include ROOT . '/views/layouts/header.php'; ?>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-12">
                    <div class="topGamers">
                        <h2>Лучшие игроки</h2>
                        <hr>
                        <table border="1">
                            <tbody>
                            <tr>
                                <th><i class="fa fa-trophy"></i></th>
                                <th>Игрок</th>
                                <th>Кол-во побед</th>
                            </tr>
                            <?php $count = 0; foreach ($gamers as $gamer): ?>
                                <tr>
                                    <td><?php echo ++$count; ?></td>
                                    <td><?php echo $gamer['nick']; ?></td>
                                    <td><?php echo $gamer['wins']; ?></td>
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