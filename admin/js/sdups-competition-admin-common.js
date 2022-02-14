'use strict';

(function ($) {
        $.SDUPSAdminCommon = function () {}
        $.SDUPSAdminCommon.prototype = {
            init: function (onAjaxSuccess) {
                // WP makes notices jump around :(
                $('.notice').show();

                $('form.ajax').on('submit', function (e) {
                    e.preventDefault();
                    var data = $(this).serializeArray().reduce(
                        function (o, kv) {
                            o[kv.name] = kv.value;
                            return o;
                        }, {});
                    var that = $(this);
                    $.ajax({
                        url: cpm_object.ajax_url,
                        type: "POST",
                        dataType: 'json',
                        data: {
                            action: 'process_ajax',
                            data: JSON.stringify(data)
                        }, success: function (response) {
                            that.find('.error-message').hide();

                            if ('status' in response && response.status === 302) {
                                window.location.replace(response.url);
                            }

                            if (onAjaxSuccess && typeof onAjaxSuccess === 'function') {
                                onAjaxSuccess(that, response);
                            }
                        }, error: function (response) {
                            this.handleAjaxError(that, response.responseJSON.data);
                        }
                    });
                });
            },

            handleAjaxError: function (form, data) {
                var div = form.find(".error-message");
                if (div) {
                    div.html($('<div/>', {
                        class: "notice notice-error",
                        style: "display: block;"
                    }).append($('<p/>').text(data.message)));
                    div.show();
                }
            }
        }
    }
)(jQuery);
