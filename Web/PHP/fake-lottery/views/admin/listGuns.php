<?php include ROOT . '/views/layouts/header.php'; ?>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-12">
                    <div class="list">
                        <h2>Оружие (<?php echo count($guns); ?>)</h2>
                        <hr>
                        <table border="1">
                            <tbody>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Доп.</th>
                            </tr>
                            <?php foreach ($guns as $gun): ?>
                                <tr>
                                    <td><?php echo $gun['id']; ?></td>
                                    <td><?php echo $gun['name']; ?></td>
                                    <td><?php echo $gun['price']; ?></td>
                                    <td>
                                        <form action="/admin/deleteGun" method="post">
                                            <input type="text" class="hidden" name="id" value="<?php echo $gun['id']; ?>">
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