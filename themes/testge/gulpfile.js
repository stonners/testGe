//
// Base modules
//
const gulp = require('gulp');
const source = require('vinyl-source-stream');
const browserSync = require('browser-sync').create();
const fs = require("fs");
const del = require("del");

//
// Others
//
const sass = require('gulp-sass');
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const sourcemaps = require('gulp-sourcemaps');
const cssnano = require('cssnano');
const browserify = require('browserify');
const babelify = require('babelify');
const imagemin = require('gulp-imagemin');
const favicons = require('gulp-favicons');
const extname = require('gulp-extname');
const terser = require('gulp-terser');

//
// Clean assets folder
//
gulp.task('clean', function () {
    return del('assets');
});

//
// Copy fonts assets
//
gulp.task('copy-assets', function () {
    let assets = JSON.parse(fs.readFileSync('gulpfile.assets.json'));
    gulp.src(assets.fonts)
        .pipe(gulp.dest('assets/fonts'))
    gulp.src(assets.images)
        .pipe(gulp.dest('assets/img'))
    gulp.src(assets.js)
        .pipe(gulp.dest('assets/js'))
    gulp.src('src/js/vendors/**/*.js')
        .pipe(gulp.dest('assets/js'));
});

//
// Compile scss files
//
gulp.task('css', function () {
    del('assets/css');
    return gulp.src('src/sass/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss([autoprefixer]))
        .pipe(gulp.dest('assets/css'))
        .pipe(browserSync.stream());
});

//
// Compile js files
//
gulp.task('js', function () {
    del('assets/js');
    return browserify({
        entries: 'src/js/app.js',
        debug: false
    })
        .transform(babelify, {
            presets: ['@babel/preset-env']
        })
        .bundle()
        .pipe(source('app.js'))
        .pipe(gulp.dest('assets/js'));
});

//
// Minify css files
//
gulp.task('css-min', ['css'], function () {
    return gulp.src('assets/css/**/*.css')
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(postcss([cssnano]))
        .pipe(extname('.min.css'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('assets/css'));
});

//
// Minify js files
//
gulp.task('js-min', ['js'], function () {
    return gulp.src('assets/js/**/*.js')
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(terser())
        .pipe(extname('.min.js'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('assets/js'));
});

//
// Minify images files
//
gulp.task('image-min', ['copy-assets'], function () {
    return gulp.src('assets/img/**/*')
        .pipe(imagemin([
            imagemin.gifsicle({ interlaced: true }),
            imagemin.jpegtran({ progressive: true }),
            imagemin.optipng({ optimizationLevel: 5 }),
            imagemin.svgo({
                plugins: [
                    { removeViewBox: true },
                    { cleanupIDs: false }
                ]
            })
        ]))
        .pipe(gulp.dest('assets/img'))
});

//
// Generate favicons
//
gulp.task("favicons", function () {
    return gulp.src("src/assets/favicon/favicon.png")
        .pipe(favicons({
            background: "#ffffff",
            path: "./favicons/",
            replace: true,
            icons: {
                android: false,              // Create Android homescreen icon. `boolean`
                appleIcon: false,            // Create Apple touch icons. `boolean`
                appleStartup: false,         // Create Apple startup images. `boolean`
                coast: false,                // Create Opera Coast icon. `boolean`
                favicons: true,              // Create regular favicons. `boolean`
                firefox: false,              // Create Firefox OS icons. `boolean`
                opengraph: false,            // Create Facebook OpenGraph image. `boolean`
                twitter: false,              // Create Twitter Summary Card image. `boolean`
                windows: false,              // Create Windows 8 tile icons. `boolean`
                yandex: false                // Create Yandex browser icon. `boolean`
            }
        }))
        .pipe(gulp.dest('assets/favicons/'));
});

//
// Serve the app
//
gulp.task('serve', ['clean'], function () {
    gulp.start('css', 'js', 'copy-assets', 'favicons');

    browserSync.init({
        proxy: "192.168.33.10",
        notify: true
    });

    gulp.watch("src/sass/**/*.scss", ['css']);
    gulp.watch("src/js/**/*.js", ['js']).on('change', browserSync.reload);
    gulp.watch("src/assets/img/**/*", ['copy-assets']).on('change', browserSync.reload);
    gulp.watch("src/assets/fonts/**/*", ['copy-assets']).on('change', browserSync.reload);
    gulp.watch("src/assets/favicon/**/*", ['favicons']).on('change', browserSync.reload);
    gulp.watch("gulpfile.assets.json", ['copy-assets']).on('change', browserSync.reload);
});

//
// Production build
//
gulp.task('build', ['clean'], function () {
    gulp.start('css-min', 'js-min', 'image-min', 'favicons');
});
