(function ($) {
    'use strict';
    $(document).ready(function () {
        hideAll();

        var activeSection = $('h2.nav-tab-wrapper > a.nav-tab-active');
        if (activeSection.length != 1) {
            setActiveOnly('submissions');
            activeSection = $('h2.nav-tab-wrapper > a.nav-tab-active');
        }

        showContent($(getSectionId(activeSection)));

        $('h2.nav-tab-wrapper > a').click(function () {
            hideAll();
            showContent($(getSectionId($(this))));
            setActiveOnly($(this).attr('data'));
        });
    });

    function setActiveOnly(section) {
        $('h2.nav-tab-wrapper > a').each(function () {
            $(this).removeClass('nav-tab-active');
            if ($(this).attr('data') == section) {
                $(this).addClass('nav-tab-active');
            }
        });
    }

    function getSectionId(link) {
        return '#' + link.attr('data');
    }

    function showContent(div) {
        div.show();
    }

    function hideAll() {
        $('#content > div').hide();
    }

})(jQuery);
