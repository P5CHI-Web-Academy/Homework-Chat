const gulp = require('gulp');
const env = require('gulp-env');
const gulpIf = require('gulp-if');
const concat = require('gulp-concat');
const sassLint = require('gulp-sass-lint');
const eslint = require('gulp-eslint');
const sass = require('gulp-sass');
const autoPrefixer = require('gulp-autoprefixer');
const minifyCss = require('gulp-csso');
const sourceMaps = require('gulp-sourcemaps');
const uglify = require('gulp-uglify');
const babel = require('gulp-babel');
const browserSync = require('browser-sync').create();
const clean = require('gulp-clean');

const paths = {
    publicFolder: './public/',
    manifestFile: './public/manifest.json',
    scssSource: './scss/',
    jsSource: './js/',
    vendorJs: [
        './node_modules/jquery/dist/jquery.js',
        './node_modules/toastr/build/toastr.min.js'
    ],
    vendorCss: [
        './node_modules/toastr/build/toastr.css'
    ]
};

gulp.task('set-env-dev', function(done) {
    env({
        vars: {
            GULP_ENV: 'dev',
            CSSO_OPTIONS: {
                restructure: false,
                sourceMap: true,
                debug: true
            }
        }
    });
    done();
});

gulp.task('set-env-prod', function(done) {
    env({
        vars: {
            GULP_ENV: 'prod',
            CSSO_OPTIONS: {}
        }
    });
    done();
});

gulp.task('lint-sass', function() {
    return gulp.src(paths.cssSource + '**/*.s+(a|c)ss')
        .pipe(sassLint({
            configFile: '.sass-lint.yml'
        }))
        .pipe(sassLint.format())
        .pipe(gulpIf(process.env.GULP_ENV === 'prod', sassLint.failOnError()));
});

gulp.task('sass-vendor', function() {
    return gulp.src(paths.vendorCss, {base: paths.scssSource})
        .pipe(concat('vendor.css'))
        .pipe(gulp.dest(paths.publicFolder));
});

gulp.task('sass-project', gulp.series('lint-sass', function() {
    return gulp.src([paths.scssSource + '*.scss'], {base: paths.scssSource})
        .pipe(gulpIf(process.env.GULP_ENV === 'dev', sourceMaps.init()))
        .pipe(gulpIf(process.env.GULP_ENV === 'dev', sass().on('error', sass.logError), sass()))
        .pipe(autoPrefixer())
        .pipe(minifyCss(process.env.CSSO_OPTIONS))
        .pipe(gulpIf(process.env.GULP_ENV === 'dev', sourceMaps.write('.')))
        .pipe(gulp.dest(paths.publicFolder))
        .pipe(browserSync.stream());
}));

gulp.task('js-vendor', function() {
    return gulp.src(paths.vendorJs, {base: paths.jsSource})
        .pipe(gulpIf(process.env.GULP_ENV === 'dev', sourceMaps.init()))
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(gulpIf(process.env.GULP_ENV === 'prod', eslint.failAfterError()))
        .pipe(babel({presets: ['env']}))
        .pipe(concat('vendor.js'))
        .pipe(gulpIf(process.env.GULP_ENV === 'prod', uglify()))
        .pipe(gulpIf(process.env.GULP_ENV === 'dev', sourceMaps.write('.')))
        .pipe(gulp.dest(paths.publicFolder));
});

gulp.task('js-project', function() {
    return gulp.src(paths.jsSource + '*.js', {base: paths.jsSource})
        .pipe(gulpIf(process.env.GULP_ENV === 'dev', sourceMaps.init()))
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(gulpIf(process.env.GULP_ENV === 'prod', eslint.failAfterError()))
        .pipe(babel({presets: ['env']}))
        .pipe(concat('scripts.js'))
        .pipe(gulpIf(process.env.GULP_ENV === 'prod', uglify()))
        .pipe(gulpIf(process.env.GULP_ENV === 'dev', sourceMaps.write('.')))
        .pipe(gulp.dest(paths.publicFolder));
});

gulp.task('clean-css', function() {
    return gulp.src([paths.publicFolder + '*.{css,css.map}'], {read: false})
        .pipe(clean());
});

gulp.task('clean-js', function() {
    return gulp.src(paths.publicFolder + '*.{js,js.map}', {read: false})
        .pipe(clean());
});

gulp.task('watch', function() {
    gulp.watch(paths.cssSource + '**/*.scss', gulp.series('clean-css', 'sass'));
    gulp.watch(paths.jsSource + '**/*.js', gulp.series('clean-js', 'js'));
});

gulp.task('clean', gulp.series('clean-css', 'clean-js'));
gulp.task('js', gulp.series('js-vendor', 'js-project'));
gulp.task('sass', gulp.series('sass-vendor', 'sass-project'));
gulp.task('dev', gulp.series('set-env-dev', 'clean', 'sass', 'js'));