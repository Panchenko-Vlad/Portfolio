var gulp           = require('gulp'),
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
    notify         = require("gulp-notify");

// Скрипты проекта
gulp.task('scripts', function() {
	return gulp.src([
		'template/libs/jquery/jquery-3.1.1.min.js', // Всегда в начале
        'template/libs/jquery-ui.min.js',
        'template/libs/bootstrap/bootstrap.min.js',
		'template/js/lottery.js',
		'template/js/common.js' // Всегда в конце
		])
	.pipe(concat('scripts.min.js')) // Все перечисленные файлы конкатинируем в один указанный файл
	.pipe(uglify()) // Сжимаем файл
	.pipe(gulp.dest('template/js')) // Вставляем в указанное место
	.pipe(browserSync.reload({stream: true}));
});

gulp.task('browser-sync', function() {
    browserSync({
        proxy: "lottery.lose",
        open: false,
        notify: false
    });
});

/* Таск, какой генерирует из файлов формата sass, файлы формата css */
gulp.task('sass', function() {
	return gulp.src('template/sass/**/*.sass')
	.pipe(sass({
		includePaths: bourbon.includePaths
	}).on("error", notify.onError()))
	.pipe(rename({suffix: '.min', prefix : ''}))
	.pipe(autoprefixer(['last 15 versions']))
	.pipe(cleanCSS()) // Минимизируем всё содержимое
	.pipe(gulp.dest('template/css'))
	.pipe(browserSync.reload({stream: true}));
});

/* Данный таск, для отслеживания изменений файлов указанных форматов */
gulp.task('watch', ['sass', 'scripts', 'browser-sync'], function() {
	gulp.watch('template/sass/**/*.sass', ['sass']);
	gulp.watch(['template/libs/**/*.js', 'template/js/**/*.js'], ['scripts']);
    gulp.watch('**/*.php', browserSync.reload);
	gulp.watch('**/*.html', browserSync.reload);
});

/* Таск, для оптимизации, кеширования и сжатия картинок */
gulp.task('imagemin', function() {
	return gulp.src('template/img/**/*')
	.pipe(cache(imagemin()))
	.pipe(gulp.dest('dist/img'));
});

/* Таск, для генерации всех файлов в файлы для продакшина (в папку dist) */
/* Удаляет старую папку dist | сжимает картинки | генерирует css | генерирует js */
gulp.task('build', ['removedist', 'imagemin', 'sass', 'scripts'], function() {

	var buildFiles = gulp.src([
        '**/*.php',
        '**/*.html',
		'.htaccess'
		]).pipe(gulp.dest('dist'));

	var buildCss = gulp.src([
		'template/css/main.min.css'
		]).pipe(gulp.dest('dist/css'));

	var buildJs = gulp.src([
		'template/js/scripts.min.js'
		]).pipe(gulp.dest('dist/js'));

	var buildFonts = gulp.src([
		'template/fonts/**/*']
		).pipe(gulp.dest('dist/fonts'));

});

gulp.task('removedist', function() { return del.sync('dist'); });
/* Таск, для очистки кеша */
gulp.task('clearcache', function () { return cache.clearAll(); });

/* Дефолтный таск, какой запускает слежение за файлами и их обновлением */
gulp.task('default', ['watch']); // Запускается по команде gulp, так как он дефолтный
