var gulp = require('gulp');
var uglify = require('gulp-uglify');
var pump = require('pump');
var cleanCSS = require('gulp-clean-css');
var concat = require('gulp-concat');

gulp.task('minify-js', function (cb) {
    pump([
            gulp.src('test_gulp/*.js'),
            concat('mawei.min.js'),
            uglify(),
            gulp.dest('test_gulp/dist')
        ],
        cb
    );
});

gulp.task('minify-css', (cb) => {
    pump([
             gulp.src('test_gulp/*.css'),
             concat('mawei.min.css'),
             cleanCSS(),
             gulp.dest('test_gulp/dist')
         ],
         cb
    );
});

gulp.task('default', ['minify-js', 'minify-css']);