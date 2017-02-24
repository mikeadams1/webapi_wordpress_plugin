/*global module:false*/
module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    // Metadata.
    pkg: grunt.file.readJSON('package.json'),
    banner: '/*! <%= pkg.title || pkg.name %> - v<%= pkg.version %> - ' +
      '<%= grunt.template.today("yyyy-mm-dd") %>\n' +
      '<%= pkg.homepage ? "* " + pkg.homepage + "\\n" : "" %>' +
      '* Copyright (c) <%= grunt.template.today("yyyy") %> <%= pkg.author.name %>;' +
      ' Licensed <%= _.pluck(pkg.licenses, "type").join(", ") %> */\n',
    // Task configuration.
    pot: {
      options:{
        text_domain: 'nwa', //Your text domain. Produces my-text-domain.pot
        dest: 'languages/', //directory to place the pot file
        keywords: [ //WordPress localisation functions
          '__:1',
          '_e:1',
          '_x:1,2c',
          'esc_html__:1',
          'esc_html_e:1',
          'esc_html_x:1,2c',
          'esc_attr__:1',
          'esc_attr_e:1',
          'esc_attr_x:1,2c',
          '_ex:1,2c',
          '_n:1,2',
          '_nx:1,2,4c',
          '_n_noop:1,2',
          '_nx_noop:1,2,3c'
        ]
      },
      files:{
        src:  [ '**/*.php' ], //Parse all php files
        expand: true
      }
    },
    sass: {                              // Task
      debug: {                            // Target
        options: {                       // Target options
          style: 'expanded'
        },
        files: {                         // Dictionary of files
          'admin/css/nwa-admin.css':   'admin/css/nwa-admin.scss',       // 'destination': 'source'
          'public/css/nwa-public.css': 'public/css/nwa-public.scss'
        }
      },
      dist: {                            // Target
        options: {                       // Target options
          style: 'compressed'
        },
        files: {                         // Dictionary of files
          'admin/css/nwa-admin.min.css': 'admin/css/nwa-admin.scss',       // 'destination': 'source'
          'public/css/nwa-public.min.css': 'public/css/nwa-public.scss'
        }
      }
    },
    concat: {
      options: {
        banner: '<%= banner %>',
        stripBanners: true
      }
    },
    replace: {
      readme: {
        options: {
          patterns: [
            {
              match: /(Stable tag: \d+\.\d+\.\d+)/,
              replacement: 'Stable tag: <%= pkg.version %>'
            }
          ]
        },
        files: [
          {expand: true, flatten: true, src: ['README.txt'], dest: './'}
        ]
      },
      vercode: {
        options: {
          patterns: [
            {
              match: /( \* Version:           \d+\.\d+\.\d+)/,
              replacement: ' * Version:           <%= pkg.version %>'
            }
          ]
        },
        files: [
          {expand: true, flatten: true, src: ['nwa.php'], dest: './'}
        ]
      },
      vercode2: {
        options: {
          patterns: [
            {
              match: /('NWA_VERSION', '\d+\.\d+\.\d+')/,
              replacement: '\'NWA_VERSION\', \'<%= pkg.version %>\''
            }
          ]
        },
        files: [
          {expand: true, flatten: true, src: ['nwa.php'], dest: './'}
        ]
      }
    },
    uglify: {
      options: {
        banner: '<%= banner %>'
      },
      dist: {
        src: 'public/js/nwa-public.js',
        dest: 'public/js/nwa-public.min.js'
      },
      dist_admin: {
        src: 'admin/js/nwa-admin.js',
        dest: 'admin/js/nwa-admin.min.js'
      }
    },
    watch: {
      sass:{
        files: [
          'admin/css/nwa-admin.scss',
          'public/css/nwa-public.scss'
        ],
        tasks: ['sass']
      },
      coffee:{
        files: ['admin/js/**/*.js','public/js/**/*.js'],
        tasks: ['uglify']
      },
      sync: {
        files: [
          '**',
          '!node_modules/**/*',
          '!.sass-cache',
          '!.git*',
          '!**/*.scss',
          '!.idea'
        ],
        tasks: 'sync'
      }
    },
    sync: {
      hotdeploy: {
        files: [
          {
            cwd: '.',
            src: [
              '**',
              '!node_modules/**/*',
              '!.sass-cache',
              '!.git*',
              '!**/*.scss',
              '!.idea'
            ],
            dest: '<%= pkg.sandbox.dir %>'
          }
        ],
        verbose: true,
        pretend: false,
        updateAndDelete: true
      }
    },
    browserSync: {
      dev: {
        bsFiles: {
          src: [
            '<%= pkg.sandbox.dir %>/**/*'
          ]
        },
        options: {
          watchTask: true,
          debugInfo: true,
          logConnections: true,
          notify: true,
          proxy: "localhost",
          ghostMode: {
            scroll: true,
            links: true,
            forms: true
          }
        }
      }
    }
  });

  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-replace');
  grunt.loadNpmTasks('grunt-pot');
  grunt.loadNpmTasks('grunt-sync');
  grunt.loadNpmTasks('grunt-browser-sync');



  // Default task.
  grunt.registerTask('default', ['build']);
  grunt.registerTask('finalize', ['replace', 'build']);
  grunt.registerTask('dev', ['build', 'sync', 'browserSync', 'watch']);
  grunt.registerTask('build', ['sass', 'concat', 'uglify', 'replace']);

};
