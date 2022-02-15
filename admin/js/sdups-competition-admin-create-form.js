'use strict';

(function ($) {
        var submissionsTable = null;
        var categoriesForm = null;

        $(document).ready(function () {
            var admin = new $.SDUPSAdminCommon();
            var subs = $('#submissions');

            var searchHeader = $('<tr/>');

            var n = 0;
            subs.find('thead th').each(function () {
                var that = $(this).clone();
                var title = $(that).text();
                if (n == 0 || n > 3) {
                    $(that).html('');
                } else {
                    $(that).html('<input type="text" style="font-weight: normal;" placeholder="Search ' + title + '" />');
                    $(that).addClass('dt-head-left');
                    $(this).addClass('dt-head-left');
                }
                $(that).attr('id', 'filter-holder-' + n++);
                $(that).appendTo(searchHeader);
            });

            $(searchHeader).prependTo(subs.find('thead'));

            var cols = admin.submissionsTableDefaultColumns;
            cols.splice(4, 1);
            cols.unshift(
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return '<input type="checkbox" name="entry_id" ' +
                            'value="' + row.entry_id + '" ' +
                            'checked/>';
                    },
                    className: 'dt-body-center',
                    orderable: false
                }
            );

            var options = {
                "columns": cols,
                "order": [[3, "desc"]],
                "initComplete": function () {
                    // Apply the search
                    this.api().columns([0]).every(function () {
                        var column = this;
                        var select = $('<input type="checkbox" id="chk">')
                            .appendTo('#filter-holder-0')
                            .on('change', function () {
                                var ischecked = $(this).is(':checked');
                                if (ischecked) {
                                    // If selected records should be displayed
                                    $.fn.dataTable.ext.search.pop();
                                    $.fn.dataTable.ext.search.push(
                                        function (settings, data, dataIndex) {
                                            var row = submissionsTable.row(dataIndex).node();
                                            var checked = $('#chk_' + dataIndex).prop('checked');
                                            var currentCheckChecked = $(row).find('input').prop('checked');
                                            if (currentCheckChecked) {
                                                return true;
                                            }

                                            return false;
                                        }
                                    );

                                    submissionsTable.draw();

                                } else {
                                    $.fn.dataTable.ext.search.pop();
                                    $.fn.dataTable.ext.search.push(
                                        function (settings, data, dataIndex) {
                                            var row = submissionsTable.row(dataIndex).node();
                                            var checked = $('#chk_' + dataIndex).prop('checked');
                                            var currentCheckChecked = $(row).find('input').prop('checked');
                                            if (currentCheckChecked) {
                                                return true;
                                            }

                                            return true;
                                        }
                                    );

                                    submissionsTable.draw();
                                }
                            });
                    });
                    this.api().columns([1]).every(function () {
                        var that = this;

                        $('input', '#filter-holder-1').on('keyup change clear', function () {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });
                    this.api().columns([2]).every(function () {
                        var that = this;

                        $('input', '#filter-holder-2').on('keyup change clear', function () {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });
                    this.api().columns([3]).every(function () {
                        var that = this;

                        $('input', '#filter-holder-3').on('keyup change clear', function () {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });
                    this.api().columns([4]).every(function () {
                        var column = this;
                        var select = $('<select style="font-weight: normal;"><option value="">-- Any --</option></select>')
                            .appendTo('#filter-holder-4')
                            .on('change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );

                                column
                                    .search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                            });

                        column.data().unique().sort().each(function (d, j) {
                            select.append('<option value="' + d + '">' + d + '</option>');
                        });
                    });

                    submissionsTable.columns.adjust();
                }
            };

            categoriesForm = $('#categories-form');
            categoriesForm.on('submit', function (e) {
                e.preventDefault();
                $('#submissions-table-container').show();
                if (submissionsTable === null) {
                    submissionsTable =
                        admin.submissionsTable(subs, categoriesForm,
                            processCategoriesFormData, null, options);
                    submissionsTable.on('draw', function () {
                        subs.find('input[type=checkbox]').change(function () {
                            if (this.checked) {
                                $(this.closest('tr')).removeClass('strikethrough');
                            } else {
                                $(this.closest('tr')).addClass('strikethrough');
                            }
                        });
                    });
                } else {
                    submissionsTable.ajax.reload();
                }
            });
        });

        function processCategoriesFormData(data) {
            var formData = categoriesForm.serializeArray().reduce(
                function (o, kv) {
                    o[kv.name] = kv.value;
                    return o;
                }, {});
            data['data'] = JSON.stringify(formData);
        }
    }
)
(jQuery);
