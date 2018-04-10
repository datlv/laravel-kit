// Vendor
require('twbs-pagination');
require('bootstrap-validator');
require('../../../../../node_modules/jasny-bootstrap/dist/js/jasny-bootstrap.min.js');
window.bootbox = require('bootbox');
window.toastr = require('toastr');
require('datatables.net');
require('datatables.net-bs');
require('selectize');
require('bootstrap-switch');
require('jquery-datetimepicker');
require('metismenu');
require('jquery-slimscroll');
require('jquery-json');
require('jquery.waitforimages');
require('nestable');
require('bootstrap-filestyle');
require('lightbox2');
window.Holder = require('holderjs');
window.Dropzone = require('dropzone');
window.Tour = require('bootstrap-tour');

// Jquery UI sortable (dependencies: core, widget, mouse)
require('../../../../../node_modules/jquery-ui/ui/core.js');
require('../../../../../node_modules/jquery-ui/ui/widget.js');
require('../../../../../node_modules/jquery-ui/ui/widgets/mouse.js');
require('../../../../../node_modules/jquery-ui/ui/widgets/sortable.js');
require('../../../../../node_modules/jquery-ui/ui/widgets/draggable.js');

// Froala Editor
window.CodeMirror = require('../../../../../node_modules/codemirror/lib/codemirror.js');
require('../../../../../node_modules/codemirror/mode/xml/xml.js');
require('../../../../../node_modules/mb-jss/froala_editor.min.js');
require('../../../../../node_modules/froala-editor/js/languages/vi.js');
require('../../../../../node_modules/mb-jss/froala_editor.manage.js');
// Froala Editor plugins
require('../../../../../node_modules/froala-editor/js/plugins/align.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/code_beautifier.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/code_view.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/colors.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/draggable.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/entities.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/font_family.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/font_size.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/fullscreen.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/help.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/image.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/inline_style.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/line_breaker.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/link.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/lists.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/paragraph_format.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/paragraph_style.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/quick_insert.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/quote.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/special_characters.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/table.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/url.min.js');
require('../../../../../node_modules/froala-editor/js/plugins/word_paste.min.js');

// MB's Scripts
require('../../../../../node_modules/mb-jss/jquery.ajaxFileUpload.js');
require('../../../../../node_modules/mb-jss/jquery.datatables.manage.js');
require('../../../../../node_modules/mb-jss/jquery.mbHelpers.js');
require('../../../../../node_modules/mb-jss/jquery.selectize_tree.js');
require('../../../../../node_modules/mb-jss/jquery.mbSlug.js');
require('../../../../../node_modules/mb-jss/jquery.nestable.manage.js');
require('../../../../../node_modules/mb-jss/jquery.quickUpdate.js');
require('../../../../../node_modules/mb-jss/jquery.selectize_user.js');
require('../../../../../node_modules/mb-jss/jquery.selectize_tags.js');
require('../../../../../node_modules/mb-jss/jquery.selectize_status.js');
require('../../../../../node_modules/mb-jss/jquery.select_btngroup.js');
require('../../../../../node_modules/mb-jss/jquery.table.row_reorder.js');

require('../../../laravel-image/assets/js/jquery.dropzone.manage.js');
require('../../../laravel-image/assets/js/jquery.image_browse.js');

// My Scripts
$(function () {
    // Add body-small class if window less than 768px
    if ($(this).width() < 769) {
        $('body').addClass('body-small')
    } else {
        $('body').removeClass('body-small')
    }

    // MetisMenu
    $('#side-menu').metisMenu();

    // Collapse ibox function
    $('.collapse-link').on('click', function () {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        var content = ibox.children('.ibox-content');
        content.slideToggle(200);
        button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        ibox.toggleClass('').toggleClass('border-bottom');
        setTimeout(function () {
            ibox.resize();
            ibox.find('[id^=map-]').resize();
        }, 50);
    });

    // Close ibox function
    $('.close-link').on('click', function () {
        $(this).closest('div.ibox').remove();
    });

    // Fullscreen ibox function
    $('.fullscreen-link').on('click', function () {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        $('body').toggleClass('fullscreen-ibox-mode');
        button.toggleClass('fa-expand').toggleClass('fa-compress');
        ibox.toggleClass('fullscreen');
        setTimeout(function () {
            $(window).trigger('resize');
        }, 100);
    });

    // Close menu in canvas mode
    $('.close-canvas-menu').on('click', function () {
        $("body").toggleClass("mini-navbar");
        SmoothlyMenu();
    });

    // Run menu of canvas
    $('body.canvas-menu .sidebar-collapse').slimScroll({
        height: '100%',
        railOpacity: 0.9
    });

    // Open close right sidebar
    $('.right-sidebar-toggle').on('click', function () {
        $('#right-sidebar').toggleClass('sidebar-open');
    });

    // Initialize slimscroll for right sidebar
    $('.sidebar-container').slimScroll({
        height: '100%',
        railOpacity: 0.4,
        wheelStep: 10
    });

    // Open close small chat
    $('.open-small-chat').on('click', function () {
        $(this).children().toggleClass('fa-comments').toggleClass('fa-remove');
        $('.small-chat-box').toggleClass('active');
    });

    // Initialize slimscroll for small chat
    $('.small-chat-box .content').slimScroll({
        height: '234px',
        railOpacity: 0.4
    });

    // Small handler
    $('.check-link').on('click', function () {
        var button = $(this).find('i');
        var label = $(this).next('span');
        button.toggleClass('fa-check-square').toggleClass('fa-square-o');
        label.toggleClass('todo-completed');
        return false;
    });

    // Minimalize menu
    $('.navbar-minimalize').on('click', function () {
        $("body").toggleClass("mini-navbar");
        SmoothlyMenu();

    });


    // Full height of sidebar
    function fix_height() {
        var heightWithoutNavbar = $("body > #wrapper").height() - 61;
        $(".sidebar-panel").css("min-height", heightWithoutNavbar + "px");

        var navbarheight = $('nav.navbar-default').height();
        var page_wrapper = $('#page-wrapper');
        var wrapperHeight = page_wrapper.height();

        if (navbarheight > wrapperHeight) {
            page_wrapper.css("min-height", navbarheight + "px");
        }

        if (navbarheight < wrapperHeight) {
            page_wrapper.css("min-height", $(window).height() + "px");
        }

        if ($('body').hasClass('fixed-nav')) {
            if (navbarheight > wrapperHeight) {
                page_wrapper.css("min-height", navbarheight + "px");
            } else {
                page_wrapper.css("min-height", $(window).height() - 60 + "px");
            }
        }

    }

    fix_height();

    // Fixed Sidebar
    $(window).bind("load", function () {
        if ($("body").hasClass('fixed-sidebar')) {
            $('.sidebar-collapse').slimScroll({
                height: '100%',
                railOpacity: 0.9
            });
        }
    });

    // Move right sidebar top after scroll
    $(window).scroll(function () {
        if ($(window).scrollTop() > 0 && !$('body').hasClass('fixed-nav')) {
            $('#right-sidebar').addClass('sidebar-top');
        } else {
            $('#right-sidebar').removeClass('sidebar-top');
        }
    });

    $(window).bind("load resize scroll", function () {
        if (!$("body").hasClass('body-small')) {
            fix_height();
        }
    });

    $("[data-toggle=popover]")
        .popover();

    // Add slimscroll to element
    $('.full-height-scroll').slimscroll({
        height: '100%'
    })
});


// Minimalize menu when screen is less than 768px
$(window).bind("resize", function () {
    if ($(this).width() < 769) {
        $('body').addClass('body-small')
    } else {
        $('body').removeClass('body-small')
    }
});

// Local Storage functions
// Set proper body class and plugins based on user configuration
$(document).ready(function () {
    if (localStorageSupport()) {
        var collapse = localStorage.getItem("collapse_menu");
        var fixedsidebar = localStorage.getItem("fixedsidebar");
        var fixednavbar = localStorage.getItem("fixednavbar");
        var boxedlayout = localStorage.getItem("boxedlayout");
        var fixedfooter = localStorage.getItem("fixedfooter");

        var body = $('body');

        if (fixedsidebar === 'on') {
            body.addClass('fixed-sidebar');
            $('.sidebar-collapse').slimScroll({
                height: '100%',
                railOpacity: 0.9
            });
        }

        if (collapse === 'on') {
            if (body.hasClass('fixed-sidebar')) {
                if (!body.hasClass('body-small')) {
                    body.addClass('mini-navbar');
                }
            } else {
                if (!body.hasClass('body-small')) {
                    body.addClass('mini-navbar');
                }

            }
        }

        if (fixednavbar === 'on') {
            $(".navbar-static-top").removeClass('navbar-static-top').addClass('navbar-fixed-top');
            body.addClass('fixed-nav');
        }

        if (boxedlayout === 'on') {
            body.addClass('boxed-layout');
        }

        if (fixedfooter === 'on') {
            $(".footer").addClass('fixed');
        }
    }
});

// check if browser support HTML5 local storage
function localStorageSupport() {
    return (('localStorage' in window) && window['localStorage'] !== null)
}

function SmoothlyMenu() {
    var body = $('body');
    var side_menu = $('#side-menu');
    if (!body.hasClass('mini-navbar') || body.hasClass('body-small')) {
        // Hide menu in order to smoothly turn on when maximize menu
        side_menu.hide();
        // For smoothly turn on menu
        setTimeout(
            function () {
                side_menu.fadeIn(400);
            }, 200);
    } else if (body.hasClass('fixed-sidebar')) {
        side_menu.hide();
        setTimeout(
            function () {
                side_menu.fadeIn(400);
            }, 100);
    } else {
        // Remove all inline style from jquery fadeIn function to reset menu state
        side_menu.removeAttr('style');
    }
}

// Dragable panels
/*function WinMove() {
 var element = "[class*=col]";
 var handle = ".ibox-title";
 var connect = "[class*=col]";
 $(element).sortable(
 {
 handle: handle,
 connectWith: connect,
 tolerance: 'pointer',
 forcePlaceholderSize: true,
 opacity: 0.8
 })
 .disableSelection();
 }*/

// My Scripts
// ---------------------------------
$(document).ready(function () {
    // SELECTIZE
    $('.selectize').each(function () {
        var _this = $(this),
            creatable = _this.data('creatable') === 'on',
            cmd_create = _this.data('cmd_create') || '_cmd_create:';
        _this.selectize({
            plugins: ['remove_button'],
            delimiter: ',',
            persist: false,
            create: creatable ? function (input) {
                return {value: cmd_create + input, text: input};
            } : false,
            createOnBlur: creatable,
            render: {
                option_create: function (data, escape) {
                    return '<div class="create">+<strong>' + escape(data.input) + '</strong>&hellip;</div>';
                }
            }
        });
    });
    $('.selectize-tags').selectize_tags();
    $('.selectize-tree').selectize_tree();

    // DATE TIME
    var datetimepicker_config = window.settings.datetimepicker || {};
    $.datetimepicker.setLocale(datetimepicker_config.lang || 'en');
    $('div.input-daterange').each(function () {
        var start = $('input:first', this),
            end = $('input:last', this),
            config = $(this).data('config') || {};
        config = $.extend(true, datetimepicker_config, config);

        function getDate(v) {
            if (v) {
                var m = v.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
                return m ? m[3] + '/' + m[2] + '/' + m[1] : v;
            } else {
                return false;
            }
        }

        start.datetimepicker(
            $.extend(true, config, {
                onShow: function () {
                    this.setOptions({
                        maxDate: getDate(end.val())
                    });
                }
            })
        );
        end.datetimepicker(
            $.extend(true, config, {
                onShow: function () {
                    this.setOptions({
                        minDate: getDate(start.val())
                    });
                }
            })
        );
    });
    $('input.datepicker').each(function () {
        var config = $(this).data('config') || {};
        config = $.extend(true, datetimepicker_config, config);
        $(this).datetimepicker(config);
    });

    $('input.datetimepicker').each(function () {
        var config = $(this).data('config') || {};
        config = $.extend(true, datetimepicker_config, config, {timepicker: true, format: 'd/m/Y H:i'});
        $(this).datetimepicker(config);
    });


    $('[data-toggle=tooltip]').tooltip({'container': 'body'});
    $('input[type="checkbox"].switch').bootstrapSwitch();
    $('input.has-slug').each(function () {
        $(this).mbSlug({'target': $(this).data('slug_target')});
    });

    $('.navbar .dropdown').hover(function () {
        $(this).find('.dropdown-menu').first().stop(true, true).slideDown(150);
    }, function () {
        $(this).find('.dropdown-menu').first().stop(true, true).slideUp(105);
    });

    if (typeof window.message !== 'undefined') {
        $.fn.mbHelpers.showMessage(window.message.type, window.message.content);
    }
});