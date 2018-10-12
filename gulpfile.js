var gulp = require('gulp'),
  uglify = require('gulp-uglify'),
  cleanCSS = require('gulp-clean-css'),
  rename = require('gulp-rename'),
  wpPot = require('gulp-wp-pot'),
  watch = require('gulp-watch')

/* Minify our CSS */
gulp.task('minify', function () {
  return gulp.src(['assets/**/css/*.css', '!assets/**/css/*.min.css'])
    .pipe(cleanCSS({rebaseTo: 'assets/**/css/'}))
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest('assets'))
})

/* Minify our JS */
gulp.task('compress', function () {
  return gulp.src(['assets/**/js/*.js', '!assets/**/js/*.min.js'])
    .pipe(uglify())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest('assets'))
})

/* Generate the latest language files */
gulp.task('language', function () {
  return gulp.src(['src/**/*.php', '*.php'])
    .pipe(wpPot({
      domain: 'gravity-pdf-core-booster',
      package: 'Gravity PDF Core Booster'
    }))
    .pipe(gulp.dest('languages/gravity-pdf-core-booster.pot'))
})

gulp.task('watch', function () {
  watch(['assets/**/js/*.js', '!assets/**/js/*.min.js'], function () { gulp.start('compress') })
  watch(['assets/**/css/*.css', '!assets/**/css/*.min.css'], function () { gulp.start('minify') })
})

gulp.task('default', ['language', 'minify', 'compress'])