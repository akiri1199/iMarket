<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Import Export Excel & CSV in Laravel 9</title>
    {{-- we will use Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
    <!-- Font Awesome CSS -->
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>
    <style>
        .wrapper {
            margin-block-start: 5vh;
        }

        .dataTables_filter {
            float: inline-end;
        }

        .table-hover>tbody>tr:hover {
            background-color: #ccffff;
        }

        @media only screen and (min-width: 768px) {
            .table {
                table-layout: fixed;
                max-width: 100% !important;
            }
        }

        thead {
            background: #ddd;
        }

        .table td:nth-child(2) {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .highlight {
            background: #ffff99;
        }


        .sorting_disabled {
            /* display: none; */
        }

        @media only screen and (max-width: 767px) {

            /* Force table to not be like tables anymore */
            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            /* Hide table headers (but not display: none;, for accessibility) */
            thead tr,
            tfoot tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            td {
                /* Behave  like a "row" */
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50% !important;
            }

            td:before {
                /* Now like a table header */
                position: absolute;
                /* Top/left values mimic padding */
                top: 6px;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
            }

            .table td:nth-child(1) {
                background: #ccc;
                height: 100%;
                inset-block-start: 0;
                inset-inline-start: 0;
                font-weight: bold;
            }
        }

        .container_whole {
            padding: 30px;
        }

        th {
            text-align: center !important;
        }
    </style>
</head>

<body>
    <div class="container_whole mt-5 text-center">
        <h2 class="mb-5">
            iMarket（適時開示ネット）
        </h2>
        <div class="row">
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-auto">
                        <input type="text" class="form-control" placeholder="証券コード、企業名" id="searchText" />
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" id="searchBtn">検索</button>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <a href="/" class="btn btn-success">ホームページ</a>
            </div>
        </div>
        <div class="row" style="margin-top: 30px">
            <div class="col-sm-2">
                @if (count($main_info) > 0)
                    <h4>{{ $main_info[0]->stock_code }}-{{ $main_info[0]->company }}</h4>
                @endif
            </div>
            <div class="col-sm-10"></div>
        </div>

        <div class="py-4">
            <h3>累計/通期</h3>
            <table class="table table-striped table-hover table-bordered" id="main_table">
                <thead class="text-center pt-4">
                    <th>決算期</th>
                    <th>四半期</th>
                    <th>売上高</th>
                    <th>営業利益</th>
                    <th>経常利益</th>
                    <th>純利益</th>
                    <th>EPS</th>
                    <th>営業利益率</th>
                    <th>経常利益率</th>
                    <th>売上高<br />前年比</th>
                    <th>営業利益<br />前年比</th>
                    <th>経常利益<br />前年比</th>
                    <th>純利益<br />前年比</th>
                </thead>
                <tbody>
                    @foreach ($main_data as $item)
                        <tr>
                            <td>{{ $item->fiscal }}</td>
                            <td>{{ $item->fiscal_term }}</td>
                            <td>{{ $item->sales_amount }}</td>
                            <td>{{ $item->operating_income }}</td>
                            <td>{{ $item->odinary_profit }}</td>
                            <td>{{ $item->net_income }}</td>
                            <td>{{ $item->net_income_per_share }}</td>
                            <td>{{ $item->income_per_share }}</td>
                            <td>{{ $item->profit_per_share }}</td>
                            <td>{{ $item->diff_sales }}</td>
                            <td>{{ $item->diff_income }}</td>
                            <td>{{ $item->diff_profit }}</td>
                            <td>{{ $item->diff_net }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
        <div class="py-4">
            <h3>四半期</h3>
            <table class="table table-striped table-hover table-bordered" id="sub_table">
                <thead class="text-center pt-4">
                    <th>決算期</th>
                    <th>四半期</th>
                    <th>売上高</th>
                    <th>営業利益</th>
                    <th>経常利益</th>
                    <th>純利益</th>
                    <th>EPS</th>
                    <th>営業利益率</th>
                    <th>経常利益率</th>
                    <th>売上高<br />前年比</th>
                    <th>営業利益<br />前年比</th>
                    <th>経常利益<br />前年比</th>
                    <th>純利益<br />前年比</th>
                </thead>
                <tbody>
                    @foreach ($sub_data as $item)
                        <tr>
                            <td>{{ $item->fiscal }}</td>
                            <td>{{ $item->fiscal_term }}</td>
                            <td>{{ $item->sales_amount }}</td>
                            <td>{{ $item->operating_income }}</td>
                            <td>{{ $item->odinary_profit }}</td>
                            <td>{{ $item->net_income }}</td>
                            <td>{{ $item->net_income_per_share }}</td>
                            <td>{{ $item->income_per_share }}</td>
                            <td>{{ $item->profit_per_share }}</td>
                            <td>{{ $item->diff_sales }}</td>
                            <td>{{ $item->diff_income }}</td>
                            <td>{{ $item->diff_profit }}</td>
                            <td>{{ $item->diff_net }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
    <script src='https://code.jquery.com/jquery-3.7.0.js'></script>
    <!-- Data Table JS -->
    <script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>
    <script src='https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js'></script>
    <script src='https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js'></script>
    <script>
        $(document).ready(function() {
            var temp_for_main = "no";
            var main_table = $('#main_table').DataTable({
                pageLength: 18,
                language: {
                    "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/ja.json",
                    'paginate': {
                        'previous': '<span class="fa fa-chevron-left"></span>',
                        'next': '<span class="fa fa-chevron-right"></span>'
                    },
                    "lengthMenu": ' <select class="form-control input-sm">' +
                        '<option value="18">18</option>' +
                        '<option value="36">36</option>' +
                        '<option value="-1">All</option>' +
                        '</select> 件'
                },
                stateSave: true,
                processing: true,
                columnDefs: [{
                    targets: '_all',
                    render: function(data, type, row) {
                        if (data > 1000000) {
                            var dividedValue = (data / 1000000);
                            return dividedValue;
                        }
                        if (typeof data == 'string') {
                            return data.replace("四半期", "Q").replace("第", "").replace("年", "/")
                                .replace("月期", "");
                        }
                    }
                }],
                rowCallback: function(row, data, index) {
                    if (index > 0) {
                        var currentCellValue = data[0];
                        var previousRowData = this.api().row(index - 1).data();
                        if (temp_for_main == "no") {
                            if (currentCellValue == previousRowData[0]) {
                                $(row).find('td:eq(0)').empty();
                            } else {
                                temp_for_main = currentCellValue;
                            }
                        } else {
                            if (currentCellValue == temp_for_main) {
                                $(row).find('td:eq(0)').empty();

                            } else {
                                temp_for_main = currentCellValue;
                            }
                        }
                    }
                    var salesRow = data[7];
                    if (!salesRow) {
                        $(row).hide();
                    }
                }
            })
            var temp_for_sub = "no";
            var sub_table = $('#sub_table').DataTable({
                language: {
                    "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/ja.json",
                    'paginate': {
                        'previous': '<span class="fa fa-chevron-left"></span>',
                        'next': '<span class="fa fa-chevron-right"></span>'
                    },
                    "lengthMenu": ' <select class="form-control input-sm">' +
                        '<option value="10">10</option>' +
                        '<option value="20">20</option>' +
                        '<option value="30">30</option>' +
                        '<option value="40">40</option>' +
                        '<option value="50">50</option>' +
                        '<option value="100">100</option>' +
                        '<option value="-1">All</option>' +
                        '</select> 件'
                },
                stateSave: true,
                processing: true,
                columnDefs: [{
                    targets: '_all',
                    render: function(data, type, row) {
                        if (data > 1000000) {
                            var dividedValue = (data / 1000000);
                            return dividedValue;
                        }
                        if (typeof data === 'string') {
                            return data.replace("四半期", "Q").replace("第", "").replace("年", "/")
                                .replace("月期", "");
                        } else {
                            return data;
                        }
                    }
                }],
                rowCallback: function(row, data, index) {
                    if (index > 0) {
                        var currentCellValue = data[0];
                        var previousRowData = this.api().row(index - 1).data();
                        if (temp_for_sub == "no") {
                            if (currentCellValue == previousRowData[0]) {
                                $(row).find('td:eq(0)').empty();
                            } else {
                                temp_for_sub = currentCellValue;
                            }
                        } else {
                            if (currentCellValue == temp_for_sub) {
                                $(row).find('td:eq(0)').empty();

                            } else {
                                temp_for_sub = currentCellValue;
                            }
                        }
                    }
                }
            })
            $('#searchBtn').on('click', function() {
                var searchText = $("#searchText").val();
                if (searchText != "") {
                    window.location.href = "/detail/" + searchText;
                }

            });
        });
    </script>
</body>

</html>
