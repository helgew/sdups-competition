'use strict';

(function ($) {
        var previewTable = null;
        var fieldsForm = null;
        var submissionsTable = null;

        $(document).ready(function () {

            fieldsForm = $('#wpform-fields-form');
            submissionsTable = $('table[id="submissions"]');

            var onConfigTab = fieldsForm.length != 0;
            var onSubmissionsTab = submissionsTable.length != 0;

            var admin = new $.SDUPSAdminCommon();
            admin.init(overviewSuccess);

            if (onSubmissionsTab) {
                admin.submissionsTable(submissionsTable);
            } else if (onConfigTab) {
                fieldsForm.on('submit', function (e) {
                    e.preventDefault();
                    $('#confirmation-form-container').show();
                    if (previewTable === null) {
                        previewTable = $('#submission-data-preview').DataTable({
                            "ajax": {
                                "url": cpm_object.ajax_url,
                                "data": function (data) {
                                    data['action'] = "process_ajax";
                                    var formData = fieldsForm.serializeArray().reduce(
                                        function (o, kv) {
                                            o[kv.name] = kv.value;
                                            return o;
                                        }, {});
                                    data['data'] = JSON.stringify(formData);
                                },
                                "type": "POST",
                                "dataSrc": function (json) {
                                    fieldsForm.find('.error-message').hide();
                                    var form = $('#confirmation-form');
                                    Object.keys(json.meta).forEach(key => {
                                        if (key !== 'action') {
                                            var fields = form.find('input[name="' + key + '"');
                                            if (fields.length > 1) {
                                                fields.remove();
                                                fields = [];
                                            }
                                            if (fields.length === 0) {
                                                $('<input>').attr({
                                                    type: 'hidden',
                                                    name: key,
                                                    value: json.meta[key]
                                                }).appendTo(form);
                                            } else {
                                                fields[0].value = json.meta[key];
                                            }
                                        }
                                    });
                                    return json.data;
                                },
                                "error": function (response) {
                                    handleAjaxError(fieldsForm, response.responseJSON.data);
                                }
                            },
                            "columns": [
                                {"data": "name"},
                                {"data": "email"},
                                {"data": "date"},
                                {"data": "division"},
                                {"data": "category"},
                                {
                                    "data": "upload",
                                    "className": 'dt-body-center',
                                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                                        $(nTd).html(getLinkForUpload(oData.upload));
                                    }
                                },
                            ],
                            "order": [[2, "desc"]],
                            "processing": true,
                            "language": {
                                processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span> '
                            }
                        });
                    } else {
                        previewTable.ajax.reload();
                    }
                });

                checkFormSelections(false, null);
            }
        });

        function overviewSuccess(form, response) {
            replaceFormElements(response);
            if (onConfigTab) {
                checkFormSelections(true, form);
            }
        }

        function checkFormSelections(isNew, form) {
            var pickerForm = $('#wpform-picker-form');
            if (pickerForm.find('option').length == 1) {
                pickerForm.find(':submit').prop("disabled", true);
                if (isNew && (form === null || form.attr('id') != 'wpform-picker-form') &&
                    !pickerForm.find('option').text().startsWith('--')) {
                    pickerForm.find('form').submit();
                }
            }

            var haveOnlyOne = true;
            fieldsForm.find('select').each(function () {
                if ($(this).find('option').length > 1) {
                    haveOnlyOne = false;
                }

                var id = $(this).attr('id');
                var attr = id.replace('-field', '');
                $(this).find('option').each(function () {
                    var text = $(this).text().toLowerCase();
                    if (text.includes(attr.toLowerCase()) && text.length < attr.length * 3) {
                        $(this).prop('selected', true);
                    }
                });
            });
            if (haveOnlyOne) {
                fieldsForm.find(':submit').prop("disabled", true);
            }
        }

        function replaceFormElements(jsonObject) {
            var html = '';
            for (var i = 0; i < jsonObject.length; i++) {
                var formElement = jsonObject[i];
                if ('select' in formElement) {
                    html = convertSelectToHtml(formElement.select);
                    $('#' + formElement.select.id).html(html);
                }
            }
        }

        function convertSelectToHtml(selectObject) {
            var html = '';
            for (var i = 0; i < selectObject.options.length; i++) {
                var option = selectObject.options[i];
                html += '<option value="' + option.value + '">' + option.name + '</option>';
            }
            return html;
        }
    }
)(jQuery);
