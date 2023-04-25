/**
 * Gulpfile
 */

const gulp = require('gulp');
const task = {};
const cssOutPutStyle = 'compressed'; // nested/compact/expanded/compressed
const fontName = 'jet-icons';
const runTimestamp = Math.round(Date.now() / 1000);
const minify = require('gulp-minify');

/**
 * Iconfont
 */
gulp.task('icons', function () {
  const iconfont = require('gulp-iconfont');
  const iconfontCss = require('gulp-iconfont-css');

  return gulp
    .src(['./theme/svg/*.svg'])
    .pipe(
      iconfontCss({
        fontName: fontName,
        path: './theme/sass',
        cssClass: 'icon',
        targetPath: './theme/sass/_icons.scss',
        fontPath: './docroot/fonts/jet-icons/',
        cacheBuster: '12345'
      })
    )
    .pipe(
      iconfont({
        fontName: fontName,
        prependUnicode: true, // recommended option
        formats: ['ttf', 'eot', 'woff', 'woff2'], // default, 'woff2' and 'svg' are available
        timestamp: runTimestamp, // recommended to get consistent builds when watching files
        normalize: true,
        fontHeight: 1001,
      })
    )
    .on('glyphs', function(glyphs, options) {
      console.log(glyphs, options); // Debug
    })
    .pipe(gulp.dest('./docroot/fonts/jet-icons'));
});

/**
 * JS Minify
 */

gulp.task('minify', function() {
  const notify = require('gulp-notify');
  return gulp.src(['./theme/js/*.js'])
    .pipe(minify({
        noSource: true,
        ext:{
            src:'.js',
            min:'.js'
        },
      })
    )
    .on('error', function(error) {
      gutil.log(error);
      this.emit('end');
    })
    .pipe(gulp.dest('./docroot/js/'))
    .pipe(
      notify({
        title: 'JS Minified',
        message: 'All JS files have been minified.',
        onLast: true,
      })
    );
});


/**
 * CSS
 * Style Lint
 */

gulp.task('lint-css', function () {
  const gulpStylelint = require('gulp-stylelint');
 
  return gulp
    .src('./docroot/styles/*.css')
    .pipe(gulpStylelint({
      failAfterError: true,
      reportOutputDir: 'reports/lint',
      reporters: [
        {formatter: 'verbose', console: true}
      ],
      debug: true
    }));
});

/**
 * Run SASS
 */
gulp.task('sass', function () {
  const autoprefixer = require('autoprefixer');
  const cssDeclarationSorter = require('css-declaration-sorter');
  const cssnano = require('gulp-cssnano');
  const notify = require('gulp-notify');
  const postcss = require('gulp-postcss');
  const sass = require('gulp-sass')(require('node-sass'));
  const sourcemaps = require('gulp-sourcemaps');

  return gulp
    .src('./theme/sass/*.scss')
    .pipe(sourcemaps.init())
    .pipe(
      sass({
        noCache: true,
        outputStyle: cssOutPutStyle,
        lineNumbers: false,
        loadPath: './docroot/styles/*',
        sourceMap: true,
      })
    )
    .on('error', function(error) {
      gutil.log(error);
      this.emit('end');
    })
    .pipe(
      postcss([
        autoprefixer({ grid: 'no-autoplace', }),
        cssDeclarationSorter({ order: 'smacss' }),
      ])
    )
    .pipe(sourcemaps.write('maps'))
    .pipe(gulp.dest('./docroot/styles'))
    .pipe(
      notify({
        title: 'SASS Compiled',
        message: 'All SASS files have been recompiled to CSS.',
        onLast: true,
      })
    );
});


/**
 * Watch for changes
 */
function watch() {
  const watch = require('gulp-watch');

  gulp
    .watch(['./theme/sass/*.scss'])
    .on('change', gulp.series('sass', 'lint-css'));

  gulp
    .watch(['./theme/js/*.js'])
    .on('change', gulp.series('minify'));

  gulp
    .watch(['./theme/svg/*.svg'])
    .on('change', gulp.series('icons'));
}


/**
 * Tasks
 */
gulp.task('build', gulp.series('icons', 'sass'));
gulp.task('default', gulp.series('icons', 'sass', watch));