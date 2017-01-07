
/**
 * В данном файле пишем свой js код.
 */

// Все эффекты, какие происходят при скролле
 $(function() {
   // Аргументы: входной эффект | выходной эффект | время задержки перед появлением
   $('.top_text h1').animated('fadeInDown', 'fadeOutUp', 500);
   $('.top_text p, .section_header').animated('fadeInUp', 'fadeOutDown', 1000);

   $('.animation2').animated('flipInY', 'flipOutY');
   $('.animation1').animated('fadeInLeft', 'fadeOutDown');
   $('.animation3').animated('fadeInRight', 'fadeOutDown');

   $('.left .resume_item').animated('fadeInLeft', 'fadeOutDown');
   $('.right .resume_item').animated('fadeInRight', 'fadeOutDown');
 });

// Для снятия значка загрузки, когда сайт уже загрузился
 $(function() {
 	// Когда сайт загружается значок загрузки пропадает и высвечивается сам сайт
 	$(window).load(function() {
 		$(".loader_inner").fadeOut();
 		$(".loader").delay(400).fadeOut("slow");
 	});
 });

// Для корректного отображения заднего фона
$(function() {
	// Изменяем высоту header, когда изменяется размер окна
	function heightDetect() {
		$('.main_head').css('height', $(window).height());
	}
	// Вызываем для установки
	heightDetect();
	// И в последующие разы вызываем, когда размер окна изменяется
	$(window).resize(function() {
		heightDetect();
	});
});

// Меню
$(function() {
	// Для открытия окна меню и доп. эффекты для него
	$('.toggle_menu').click(function() { // Клик на кнопку меню
  /* Функции toggleClass, fadeToggle - не используются в данном
  случае, из-за некорректной работы при многократном нажатии
  на кнопку меню */
    if ($(".top_menu").is(":visible")) {
      // Указываем кнопке состояние "no active"
      $("#sandwich").removeClass("active");
      // Убираем прозрачность задних элементов при исчезании меню
      $('.top_text').removeClass('h_opacify');
      // Плавное исчезание окна меню
      $(".top_menu").fadeOut(400);
      // Удаление и добавление новых эффектов при появлении элементов меню
      $(".top_menu li a").removeClass("fadeInUp animated").addClass('fadeOutDown animated');

      // Убираем плавное появление для каждого элемента меню
      $('.top_menu').removeClass('open_menu');
    } else {
      // Указываем кнопке состояние "active"
      $("#sandwich").addClass("active");
      // Добавляем прозрачность задних элементов при активном меню
      $('.top_text').addClass('h_opacify');
      // Плавное появление окна меню
      $(".top_menu").fadeIn(600);
      // Удаление и добавление новых эффектов при появлении элементов меню
      $(".top_menu li a").removeClass("fadeOutDown animated").addClass("fadeInUp animated");

      // Добавляем плавное появление для каждого элемента меню
      $('.top_menu').addClass('open_menu');
    };
	});

	// При клике на элемент меню прячем окно меню
	$('.top_menu ul a').click(function() { // Клик на элемент меню
    // Указываем кнопке состояние "no active"
    $('#sandwich').removeClass('active');
    // Плавное исчезание
		$('.top_menu').fadeOut(400);
    // Убираем прозрачность задних элементов при неактивном меню
    $('.top_text').removeClass('h_opacify');
    // Убираем эффект для каждого элемента меню при появлении
    $('.top_menu li a').removeClass('fadeInUp animated').addClass('fadeOutDown animated');

    // Убираем плавное появление для каждого элемента меню
    $('.top_menu').removeClass('open_menu');
	});
});

// ПОРТФОЛИО
$(function() {
  // При клике на один из элементов псевдо-меню, устанавливаем active на нажатый элемент
  $('.s_portfolio li').click(function() {
    $('.s_portfolio li').removeClass('active');
    $(this).addClass('active');
  });

  // Устанавливаем через цикл всем проектам уникальный id, для popup
  $('.portfolio_item').each(function(i) {
    $(this).find('a').attr('href', '#work' + i);
    $(this).find('.portfolio_item_description').attr('id', 'work' + i);
  });
});

// Доп. библиотеки
 $(function() {
   // MixitUp. Для анимированной сортировки проектов в секции портфолио
   $('#portfolio_grid').mixItUp();

   // Bootstrap validation. Для валидации в секции контакты
   $("input,select,textarea").jqBootstrapValidation();

   // Page scroll to id. Для плавной навигации по странице
   // Подробнее: http://manos.malihu.gr/page-scroll-to-id/2/
   $(".top_menu ul a[href*='#']").mPageScroll2id({
     offset: -50 // смещение -50px (то есть вниз), для всех секций
   });
 });

// Popup менеджер
$(function() {
  // Для отображения фото разработчика
  $('.popup').magnificPopup({type: 'image'});
  // Для отображения работы из портфолио
  $('.popup_content').magnificPopup({type: "inline", midClick: true});
});
