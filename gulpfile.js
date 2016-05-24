/* jshint esversion: 6 */

const gulp   = require('gulp');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');
const toc    = require('gulp-doctoc');

gulp.task( 'scripts', function () {
  return gulp.src(['assets/js/src/**/*.js'])
    .pipe(uglify())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('assets/js/dist'));
});

gulp.task( 'readme', function() {
  return gulp.src(['README.md'])
    .pipe(toc({
      mode: "github.com",
      title: "**Table of Contents**",
    }))
    .pipe(gulp.dest('.'));
});

gulp.task('default', [ 'scripts' ]);

gulp.task('build', [ 'default', 'readme' ]);
