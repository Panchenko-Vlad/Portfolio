<?php include ROOT . '/views/layouts/header.php'; ?>

<div class="browserTabNoActive">
    <h2>Нажмите на окно!</h2>
</div>

<section>
    <div class="container-fluid lottery">
        <div class="row">
            <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2 deposit">
                <a href="#">
                    <div class="btn-deposit"><h4>Депозит</h4></div>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <h1 id="timer">
<!--                    <span class="days"></span>-->
<!--                    <span class="hours"></span>-->
                    <span class="minutes"></span>:<span class="seconds"></span>
                </h1>
            </div>

            <div class="col-xs-12 winner-wrapper">
                <div id="winner">
                    <h1></h1>
                </div>
            </div>

            <div class="col-xs-12 col-md-10 col-md-offset-1 main-line-wrapper">
                <h5 class="sum-gamers">Количество игроков: 0</h5>
            </div>

            <div class="col-xs-12 col-md-10 col-md-offset-1 sum-price-wrapper">
                <hr class="hr-top">

                <h2>Общая сумма:
                    <span class="sumPrice">
                        <span class="before">0</span>.<span class="after">00</span>
                    </span>
                </h2>

                <hr class="hr-bottom">
            </div>
        </div>
    </div>
</section>

<section>
    <div class="container-fluid list-gamers">
        <div class="row">

            <div id="loader_in_lottery" class="col-xs-12 col-md-10 col-md-offset-1">
                <div id="circularG">
                    <div id="circularG_1" class="circularG"></div>
                    <div id="circularG_2" class="circularG"></div>
                    <div id="circularG_3" class="circularG"></div>
                    <div id="circularG_4" class="circularG"></div>
                    <div id="circularG_5" class="circularG"></div>
                    <div id="circularG_6" class="circularG"></div>
                    <div id="circularG_7" class="circularG"></div>
                    <div id="circularG_8" class="circularG"></div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include ROOT . '/views/layouts/footer.php'; ?>
