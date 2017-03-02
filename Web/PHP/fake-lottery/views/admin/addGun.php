<?php include ROOT . '/views/layouts/header.php'; ?>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3
                            col-md-8 col-md-offset-2
                            col-xs-10 col-xs-offset-1">

                    <div class="add_user">
                        <h2>Добавить пользователя<i class="fa fa-user"></i></h2>

                        <hr>

                        <?php if (isset($errors) && is_array($errors)): ?>
                            <div class="text-center text-error">
                                <ul>
                                    <?php foreach ($errors as $error): ?>
                                        <li> - <?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($success) && !empty($success)): ?>
                            <div class="text-center text-success">
                                <ul>
                                    <li> - <?php echo $success; ?></li>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="#" method="post" enctype="multipart/form-data">
                            <input type="text" name="name" class="form-control" placeholder="Имя"/>
                            <input type="text" name="price" class="form-control" placeholder="Цена"/>
                            <input type="file" name="photo">
                            <input type="submit" name="submit" value="Добавить" class="btn btn-default form-submit" />
                        </form>
                    </div>
                </div>

            </div>
        </div>
        </div>
    </section>

<?php include ROOT . '/views/layouts/footer.php'; ?>