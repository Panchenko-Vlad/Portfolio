/* Данные требования (require) мы берем из файла package.json. Библиотеки, какие мы подключаем для проекта */
var gulp           	   = require('gulp'),
		gutil          = require('gulp-util' ), // Полезные функции для gulp плагинов
		sass           = require('gulp-sass'), // Для генерации css файлов из данных sass файлов
		browserSync    = require('browser-sync'), // Сервер
		concat         = require('gulp-concat'), // Для конкатинации нескольких файлов в один
		uglify         = require('gulp-uglify'), // Для сжатия файлов
		cleanCSS       = require('gulp-clean-css'), // Для уменьшения размера css файлов
		rename         = require('gulp-rename'), // Для переименования файла. Полезен, чтобы отличить сжатый файл он не сжатого
		del            = require('del'), // Удаление файлов и папок
		imagemin       = require('gulp-imagemin'), // Автоматическая оптимизация изображений
		cache          = require('gulp-cache'), // Для кеширования изображений
		autoprefixer   = require('gulp-autoprefixer'), // Для автоматической вставки префиксов для разных браузеров (moz, webkit, o и т.д.)
		bourbon        = require('node-bourbon'), // Библиотека обертка для sass
		ftp            = require('vinyl-ftp'), // Молниеносный адаптер для FTP. Поддержка параллельных переводов, условных трансферов, буферизованных или потоковых файлов, и многое другое. Часто работает лучше, чем ваш любимый рабочий FTP клиент.
		notify         = require('gulp-notify');

// Скрипты проекта
gulp.task('scripts', function() {
	return gulp.src([
		'app/libs/jquery/dist/jquery.min.js',
		'app/libs/parallax/parallax.js', // Параллакс
		'app/libs/waypoints.min.js', // Триггер для скролла
		'app/libs/animate/animate-css.js', // Самописный скрипт, для эффектов при скролле
		'app/libs/magnific-popup/jquery.magnific-popup.min.js', // Popup менеджер
		'app/libs/mixitup.min.js', // Сортабельные анимированные плитки в портфолио
		'app/libs/jqBootstrapValidation.js', // Bootstrap validation, для формы
		'node_modules/page-scroll-to-id/jquery.malihu.PageScroll2id.js', // Для плавной навигации по странице
		'app/js/common.js' // Всегда в конце
		])
	.pipe(concat('scripts.min.js')) // Все перечисленные файлы конкатинируем в один указанный файл
	.pipe(uglify()) // Сжимаем файл
	.pipe(gulp.dest('app/js')) // Вставляем в указанное место
	.pipe(browserSync.reload({stream: true}));
});

gulp.task('browser-sync', function() {
	browserSync({
		server: {
			baseDir: 'app'
		},
		notify: false
	});
});

/* Таск, какой генерирует из файлов формата sass, файлы формата css */
gulp.task('sass', function() {
	return gulp.src('app/sass/**/*.sass')
	.pipe(sass({
		includePaths: bourbon.includePaths
	}).on("error", notify.onError()))
	.pipe(rename({suffix: '.min', prefix: ''}))
	.pipe(autoprefixer(['last 15 versions']))
	.pipe(cleanCSS())
	.pipe(gulp.dest('app/css'))
	.pipe(browserSync.reload({stream: true}));
});

/* Данный таск, для отслеживания изменений файлов указанных форматов */
gulp.task('watch', ['sass', 'scripts', 'browser-sync'], function() {
	gulp.watch('app/sass/**/*.sass', ['sass']);
	gulp.watch(['libs/**/*.js', 'app/js/common.js'], ['scripts']);
	gulp.watch('app/*.html', browserSync.reload);
});

/* Таск, для оптимизации, кеширования и сжатия картинок */
gulp.task('imagemin', function() {
	return gulp.src('app/img/**/*')
	.pipe(cache(imagemin()))
	.pipe(gulp.dest('dist/img'));
});

/* Таск, для генерации всех файлов в файлы для продакшина (в папку dist) */
/* Удаляет старую папку dist | сжимает картинки | генерирует css | генерирует js */
gulp.task('build', ['removedist', 'imagemin', 'sass', 'scripts'], function() {

	var buildFiles = gulp.src([
		'app/*.html',
		'app/.htaccess'
		]).pipe(gulp.dest('dist'));

	var buildCss = gulp.src([
		'app/css/main.min.css',
		]).pipe(gulp.dest('dist/css'));

	var buildJs = gulp.src([
		'app/js/scripts.min.js'
		]).pipe(gulp.dest('dist/js'));

	var buildFonts = gulp.src([
		'app/fonts/**/*']
		).pipe(gulp.dest('dist/fonts'));

// Выполняем доп. функции

	var buildMainColor = gulp.src([

	]).pipe(gulp.dest('dist/css/main_color/'));

	// Перемещаем файлы font-awesome
	var buildFont_awesome = gulp.src([
		'app/libs/font-awesome/**/*',
	]).pipe(gulp.dest('dist/libs/font-awesome'));

	// Перемещаем файлы linea
	var buildLinea = gulp.src([
		'app/libs/linea/**/*',
	]).pipe(gulp.dest('dist/libs/linea'));

});

gulp.task('deploy', function() {

	var conn = ftp.create({
		host:      'hostname.com',
		user:      'username',
		password:  'userpassword',
		parallel:  10,
		log: gutil.log
	});

	var globs = [
	'dist/**',
	'dist/.htaccess',
	];
	return gulp.src(globs, {buffer: false})
	.pipe(conn.dest('/path/to/folder/on/server'));

});

gulp.task('removedist', function() { return del.sync('dist'); });
/* Таск, для очистки кеша */
gulp.task('clearcache', function () { return cache.clearAll(); });

/* Главный дефолтный таск, с помощью какого всё запускается */
gulp.task('default', ['watch']); // Запускается по команде gulp, так как он дефолтный
