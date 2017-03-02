<?php include ROOT . '/views/layouts/header.php'; ?>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3 
                            col-md-8 col-md-offset-2 
                            col-xs-10 col-xs-offset-1">
                    
                    <div class="login">
                        <h2>Админ-панель<i class="fa fa-lock"></i></h2>

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

                        <form action="#" method="post">
                            <input type="text" name="login" class="form-control" placeholder="Логин"/>
                            <input type="password" name="password" class="form-control" placeholder="Пароль"/>
                            <input type="submit" name="submit" value="Войти" class="btn btn-default form-submit" />
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>

<?php include ROOT . '/views/layouts/footer.php'; ?>