module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sass: {
            dist: {
                options: {                       // Target options
                    /* loadPath: '../pad/sass/' */
                },
                files: {
                    'public/css/public-notices-public.css' : 'sass/public-notices-public.scss'
                }
            }
        },
        watch: {
            css: {
                files: '**/*.scss',
                tasks: ['sass']
            }
        },
        copy: {
            vendor: {
                files: [
                    {
                        expand: true,
                        cwd: 'node_modules/angular',
                        src: 'angular*.js',
                        dest: 'public/js'
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/angular-animate',
                        src: 'angular-animate.*',
                        dest: 'public/js'
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/angular-sanitize',
                        src: 'angular-sanitize.*',
                        dest: 'public/js'
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/angular-resource',
                        src: 'angular-resource.*',
                        dest: 'public/js'
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/angular-route',
                        src: 'angular-route.*',
                        dest: 'public/js'
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/angular-ui-bootstrap/dist',
                        src: 'ui-bootstrap*.js',
                        dest: 'public/js'
                    },
                    {
                        expand: true,
                        cwd: 'node_modules/angular-ui-router/release',
                        src: 'angular-ui-router.*',
                        dest: 'public/js'
                    },

                ]
            }



        }
    });
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.registerTask('default',['watch']);
}
