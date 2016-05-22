/* jshint esversion: 6 */

const gulp   = require('gulp');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');

gulp.task( 'scripts', function () {
  return gulp.src(['assets/js/src/**/*.js'])
    .pipe(uglify())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('assets/js/dist'));
});

gulp.task('default', [ 'scripts' ]);
