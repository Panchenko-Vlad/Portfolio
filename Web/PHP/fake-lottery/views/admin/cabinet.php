<?php include ROOT . '/views/layouts/header.php'; ?>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3
                            col-md-8 col-md-offset-2
                            col-xs-10 col-xs-offset-1">

                    <div class="cabinet">
                        <h2>Кабинет админа<i class="fa fa-pencil-square"></i></h2>

                        <hr>

                        <ul>
                            <li><a href="/admin/addUser">Добавить пользователя</a></li>
                            <li><a href="/admin/addGun">Добавить оружие</a></li>
                            <li><a href="/admin/listUsers">Список пользователей</a></li>
                            <li><a href="/admin/listGuns">Список оружия</a></li>
                            <li><a href="/admin/sign-out">Выйти</a></li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </section>

<?php include ROOT . '/views/layouts/footer.php'; ?>