/*global module:false*/
module.exports = function (grunt) {

    // Helper methods
    function sub(str) {
        return str.replace(/%s/g, LIBRARY_NAME);
    }

    function wrapModule(module, version) {
        var result = [
            sub('src/%s.intro.js'),
            sub('src/%s.const.js'),
            sub('src/%s.core.js'),
            sub('src/%s.init.js'),
            sub('src/%s.outro.js')
        ];

        version = version ? version : '';
        
        if (module) {
            result.splice(2, 0, sub('src/%s.defaults.' + module + '.js'));
            result.splice(3, 0, sub('src/%s.const.' + module + version + '.js'));
            result.splice(5, 0, sub('src/%s.' + module + '.js'));
        }

        return result;
    }

    var LIBRARY_NAME = 'yc-tracking';

    // Gets inserted at the top of the generated files in dist/.
    var BANNER = [
        '/*! <%= pkg.name %> - v<%= pkg.version %> - ',
        '<%= grunt.template.today("yyyy-mm-dd") %> - <%= pkg.author %> */\n'
    ].join('');

    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-qunit');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-replace');

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        concat: {
            dev_mg: {
                options: {
                    banner: BANNER
                },
                src: wrapModule('magento'),
                dest: sub('dist/%s.js')
            },
            dev_sw: {
                options: {
                    banner: BANNER
                },
                src: wrapModule('shopware'),
                dest: sub('dist/%s.js')
            },
            dev_oxid: {
                options: {
                    banner: BANNER
                },
                src: wrapModule('oxid'),
                dest: sub('dist/%s.js')
            },
            dev_wc: {
                options: {
                    banner: BANNER
                },
                src: wrapModule('woocommerce'),
                dest: sub('dist/%s.js')
            },
            dev_sy: {
                options: {
                    banner: BANNER
                },
                src: wrapModule('shopify'),
                dest: sub('dist/%s.js')
            },
            dev_pm: {
                options: {
                    banner: BANNER
                },
                src: wrapModule('plentymarkets'),
                dest: sub('dist/%s.js')
            },
            dev_pm7: {
                options: {
                    banner: BANNER
                },
                src: wrapModule('plentymarkets7'),
                dest: sub('dist/%s.js')
            },
            dev_mg2: {
                options: {
                    banner: BANNER
                },
                src: wrapModule('magento2'),
                dest: sub('dist/%s.js')
            },
            dev_sw5: {
                options: {
                    banner: BANNER
                },
                src: wrapModule('shopware', 5),
                dest: sub('dist/%s.js')
            },
            dev_mg_vojin: {
                src: wrapModule('magento'),
                dest: 'c:/xampp/htdocs/v1/903/tracking.js'
            },
            dev_mg2_vojin: {
                src: wrapModule('magento2'),
                dest: '/tmp/gruntGen/v1/909/tracking.js'
            },
            dev_wc_vojin: {
                src: wrapModule('woocommerce'),
                dest: 'c:/xampp/htdocs/v1/904/tracking.js'
            },
            dev_sy_vojin: {
                src: wrapModule('shopify'),
                dest: 'c:/xampp/htdocs/yc-tracking-sy.js'
            },
            dev_pm_vojin: {
                src: wrapModule('plentymarkets'),
                dest: 'c:/xampp/htdocs/yc-tracking-pm.js'
            },
            dev_pm7_vojin: {
                src: wrapModule('plentymarkets7'),
                dest: '/var/www/html/v1/906/tracking.js'
            },
            dev_sw_vojin: {
                src: wrapModule('shopware'),
                dest: 'c:/xampp/htdocs/v1/907/tracking.js'
            },
            dev_sw5_vojin: {
                src: wrapModule('shopware', 5),
                dest: '/var/www/html/v1/907/tracking.js'
            },
            dev_oxid_vojin: {
                src: wrapModule('oxid'),
                dest: '/var/www/html/v1/908/tracking.js'
            }
        },
        uglify: {
            dist: {
                files: (function () {
                    // Using an IIFE so that the destination property name can be
                    // created dynamically with sub().
                    var obj = {};
                    obj[sub('dist/%s.min.js')] = [sub('dist/%s.js')];
                    return obj;
                }())
            },
            options: {
                banner: BANNER
            }
        },
        qunit: {
            files: ['test/qunit*.html']
        },
        jshint: {
            all_files: [
                'grunt.js',
                sub('src/%s.!(intro|outro|const)*.js')
            ],
            options: {
                jshintrc: '.jshintrc'
            }
        }
    });

    grunt.registerTask('default', [
        'jshint',
        'build',
        'qunit'
    ]);
    grunt.registerTask('build-shopware', [
        'concat:dev_sw',
        'uglify:dist'
    ]);
    grunt.registerTask('build-magento', [
        'concat:dev_mg',
        'uglify:dist'
    ]);
    grunt.registerTask('build-magento2', [
        'concat:dev_mg2',
        'uglify:dist'
    ]);
    grunt.registerTask('build-shopify', [
        'concat:dev_sy',
        'uglify:dist'
    ]);
    grunt.registerTask('build-oxid', [
        'concat:dev_oxid',
        'uglify:dist'
    ]);
    grunt.registerTask('build-woocommerce', [
        'concat:dev_wc',
        'uglify:dist'
    ]);
    grunt.registerTask('build-plenty', [
        'concat:dev_pm',
        'uglify:dist'
    ]);
    grunt.registerTask('build-plenty', [
        'concat:dev_pm7',
        'uglify:dist'
    ]);
    grunt.registerTask('build-shopware5', [
        'concat:dev_sw5',
        'uglify:dist'
    ]);
    grunt.registerTask('build-magento-vojin', [
        'concat:dev_mg_vojin'
    ]);
    grunt.registerTask('build-magento2-vojin', [
        'concat:dev_mg2_vojin'
    ]);
    grunt.registerTask('build-woocommerce-vojin', [
        'concat:dev_wc_vojin'
    ]);
    grunt.registerTask('build-shopify-vojin', [
        'concat:dev_sy_vojin'
    ]);
    grunt.registerTask('build-plenty-vojin', [
        'concat:dev_pm_vojin'
    ]);
    grunt.registerTask('build-plenty7-vojin', [
        'concat:dev_pm7_vojin'
    ]);
    grunt.registerTask('build-shopware-vojin', [
        'concat:dev_sw_vojin'
    ]);
    grunt.registerTask('build-shopware5-vojin', [
        'concat:dev_sw5_vojin'
    ]);
    grunt.registerTask('build-oxid-vojin', [
        'concat:dev_oxid_vojin'
    ]);
};
