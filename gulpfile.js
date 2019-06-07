var gulp = require('gulp');

gulp.task('css', function () {
    gulp.src(['assets/css/*.css', '!assets/css/*.min.css'])
        .pipe(require('gulp-cssnano')())
        .pipe(require('gulp-rename')({extname: '.min.css'}))
        .pipe(gulp.dest('assets/css'));
});

gulp.task('js', function () {
    gulp.src(['assets/js/*.js', '!assets/js/*.min.js'])
        .pipe(require('gulp-minify')({
            ext: {
                min: '.min.js'
            }
        }))
        .pipe(gulp.dest('assets/js'));
});


gulp.task('pot', function () {
    return gulp.src(['inc/*.php', 'sendbox-newsletter.php'])
        .pipe(require('gulp-wp-pot')({
            domain: 'sendbox-email-marketing-newsletter',
            package: 'Sendbox Email Marketing Newsletter'
        }))
        .pipe(gulp.dest('languages/sendbox-email-marketing-newsletter.pot'));
});

gulp.task('svn', function () {
    gulp.src(['**/*', '!node_modules', '!node_modules/**'], {base: "."})
        .pipe(gulp.dest('../../svn/sendbox-email-marketing-newsletter/trunk'));

});

gulp.task('zip', function () {
    gulp.src(['../sendbox-email-marketing-newsletter/**/*', '!node_modules', '!node_modules/**', '!tests', '!tests/**', '!.travis.yml', '!phpcs.ruleset.xml', '!phpunit.xml.dist', '!bin', '!bin/**'], {base: "../"})
        .pipe(require('gulp-zip')('sendbox-email-marketing-newsletter.zip'))
        .pipe(gulp.dest('../../dist'));

});

gulp.task('watch', function () {
    gulp.watch('assets/css/*.css', ['css']);
    gulp.watch('assets/js/*.js', ['js']);
});

gulp.task('prod', ['css', 'js', 'pot']);