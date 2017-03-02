<?php include ROOT . '/views/layouts/header.php'; ?>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-12">
                    <div class="settings">
                        <h2>Введите ссылку на обмен</h2>
                        <hr>
                        <h5>Нажмите <a href="/steam/settings" class="steam_link" target="_blank">сюда</a>, чтобы получить ссылку на обмен.</h5>
                        <form action="/steam/link" method="post">
                            <?php if (isset($_SESSION['link'])): ?>
                                <input type="url" id="link" class="form-control" value="<?php echo $_SESSION['link']; ?>">
                            <?php else: ?>
                                <input type="url" id="link" class="form-control" placeholder="Например:  https://steamcommunity.com/tradeoffer/new/?partner=201664212&token=cRBEbr3" autofocus>
                            <?php endif; ?>

                            <p class="message-error"></p>

                            <button type="button" id="saveLink" class="btn">Сохранить<i class="fa fa-save"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include ROOT . '/views/layouts/footer.php'; ?>