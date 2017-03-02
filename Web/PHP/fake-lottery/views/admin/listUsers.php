<?php include ROOT . '/views/layouts/header.php'; ?>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-12">
                    <div class="list">
                        <h2>Пользователи (<?php echo count($users); ?>)</h2>
                        <hr>
                        <table border="1">
                            <tbody>
                            <tr>
                                <th>ID</th>
                                <th>Nick</th>
                                <th>Wins</th>
                                <th>Доп.</th>
                            </tr>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo $user['nick']; ?></td>
                                    <td><?php echo $user['wins']; ?></td>
                                    <td>
                                        <form action="/admin/deleteUser" method="post">
                                            <input type="text" class="hidden" name="id" value="<?php echo $user['id']; ?>">
                                            <input type="submit" class="btn btn-link" value="Удалить">
                                        </form>
                                    </td>
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