'use strict';

(function ($) {
    $(document).ready(function () {
        new $.SDUPSAdminCommon().getSubmissionsTable($('table[id="submissions"]'));
    });
})(jQuery);
