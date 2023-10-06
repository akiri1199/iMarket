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
            margin-top: 5vh;
        }

        .dataTables_filter {
            float: right;
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
                top: 0;
                left: 0;
                font-weight: bold;
            }

        }

        .container_whole {
            padding: 30px;
        }

        th {
            text-align: center !important;
        }

        /* * {
            border: 1px solid black;
        } */
    </style>
</head>

<body>
    <div class="container_whole mt-5 text-center">
        <h2 class="mb-5">
            iMarket（適時開示ネット）
        </h2>

        <form action="{{ route('source.import') }}" method="POST" enctype="multipart/form-data"
            class="row row-cols-lg-auto g-2 align-items-center justify-content-md-center mt-5 mb-3">
            @csrf
            <div class="col-12">
                <input type="file" name="file" class="form-control" required>
            </div>

            <div class="col-12">
                <button class="btn btn-primary" type="submit">Import data</button>
            </div>

            {{-- <div class="col-12">
                <a class="btn btn-success" href="{{ route('users.export') }}">Export data</a>
            </div> --}}
        </form>
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
                <div class="row">
                    <div class="col-auto">
                        <select name="" id="disclusure_date" class="form-select">
                            <option value="-1">開示日</option>
                            @foreach ($update_date as $item)
                                <option value="{{ $item->update_date }}">{{ $item->update_date }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
            </div>
        </div>

        <div class="py-4">
            <table class="table table-striped table-hover table-bordered" id="main_table">
                <thead class="text-center pt-4">
                </thead>
                <tbody>

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
            var table = $('#main_table').DataTable({
                language: {
                    'paginate': {
                        'previous': '<span class="fa fa-chevron-left"></span>',
                        'next': '<span class="fa fa-chevron-right"></span>'
                    },
                    "lengthMenu": 'Display <select class="form-control input-sm">' +
                        '<option value="10">10</option>' +
                        '<option value="20">20</option>' +
                        '<option value="30">30</option>' +
                        '<option value="40">40</option>' +
                        '<option value="50">50</option>' +
                        '<option value="100">100</option>' +
                        '<option value="-1">All</option>' +
                        '</select> results'
                },
                stateSave: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('source.load') }}",
                columnDefs: [{
                    orderable: false,
                    targets: '_all'
                }],
                columns: [{
                        data: 'stock_code',
                        name: 'stock_code'
                    },
                    {
                        data: 'company',
                        name: 'company'
                    },
                    {
                        data: 'stock_code',
                        name: 'stock_code'
                    },
                    {
                        data: 'fiscal',
                        name: 'fiscal'
                    },
                    {
                        data: 'fiscal_term',
                        name: 'fiscal_term'
                    },
                    {
                        data: 'sales_amount',
                        name: 'sales_amount'
                    },
                    {
                        data: 'sales_rate',
                        name: 'sales_rate'
                    },
                    {
                        data: 'operating_income',
                        name: 'operating_income'
                    },
                    {
                        data: 'operating_rate',
                        name: 'operating_rate'
                    },
                    {
                        data: 'odinary_profit',
                        name: 'odinary_profit'
                    },
                    {
                        data: 'odinary_rate',
                        name: 'odinary_rate'
                    },
                    {
                        data: 'net_income',
                        name: 'net_income'
                    },
                    {
                        data: 'net_rate',
                        name: 'net_rate'
                    },
                    {
                        data: 'net_income_per_share',
                        name: 'net_income_per_share'
                    },
                    {
                        data: 'stock_code',
                        name: 'stock_code'
                    }

                ],

                'rowsGroup': [0],
                rowCallback: function(row, data) {
                    // Add a description to each row
                    var description =
                        "fasfsadf"; // Replace 'description' with the actual property name in your data object
                    if (description) {
                        $(row).attr('title', description);
                    }
                },
                initComplete: function() {
                    $('.sorting, .sorting_asc, .sorting_desc').removeClass(
                        'sorting sorting_asc sorting_desc').addClass('no-sort');
                    var tableHeader = $('#main_table thead');
                    var newRow = $('<tr>');
                    newRow.append($('<th ">').text('時間'));
                    newRow.append($('<th>').text('会社名'));
                    newRow.append($('<th>').text('PDF'));
                    newRow.append($('<th>').text('決算期'));
                    newRow.append($('<th>').text('四半期'));

                    newRow.append($('<th colspan="2">').text('売上高'));
                    newRow.append($('<th colspan="2">').text('営業利益'));
                    newRow.append($('<th colspan="2">').text('経常利益'));
                    newRow.append($('<th colspan="2">').text('純利益'));

                    newRow.append($('<th>').text('EPS'));
                    newRow.append($('<th>').text('コード'));
                    // Append the new row to the table header
                    tableHeader.append(newRow);
                }
            })


            $('#searchBtn').on('click', function() {
                var searchText = $("#searchText").val();
                table.destroy();
                //   table.destroy();
                table = $('#main_table').DataTable({
                    language: {
                        'paginate': {
                            'previous': '<span class="fa fa-chevron-left"></span>',
                            'next': '<span class="fa fa-chevron-right"></span>'
                        },
                        "lengthMenu": 'Display <select class="form-control input-sm">' +
                            '<option value="10">10</option>' +
                            '<option value="20">20</option>' +
                            '<option value="30">30</option>' +
                            '<option value="40">40</option>' +
                            '<option value="50">50</option>' +
                            '<option value="-1">All</option>' +
                            '</select> results'
                    },
                    stateSave: true,
                    processing: true,
                    serverSide: true,
                    ajax: '/search?keyword=' + searchText,
                    columnDefs: [{
                        orderable: false,
                        targets: '_all'
                    }],
                    columns: [{
                            data: 'stock_code',
                            name: 'stock_code'
                        },
                        {
                            data: 'company',
                            name: 'company'
                        },
                        {
                            data: 'stock_code',
                            name: 'stock_code'
                        },
                        {
                            data: 'fiscal',
                            name: 'fiscal'
                        },
                        {
                            data: 'fiscal_term',
                            name: 'fiscal_term'
                        },
                        {
                            data: 'sales_amount',
                            name: 'sales_amount'
                        },
                        {
                            data: 'sales_rate',
                            name: 'sales_rate'
                        },
                        {
                            data: 'operating_income',
                            name: 'operating_income'
                        },
                        {
                            data: 'operating_rate',
                            name: 'operating_rate'
                        },
                        {
                            data: 'odinary_profit',
                            name: 'odinary_profit'
                        },
                        {
                            data: 'odinary_rate',
                            name: 'odinary_rate'
                        },
                        {
                            data: 'net_income',
                            name: 'net_income'
                        },
                        {
                            data: 'net_rate',
                            name: 'net_rate'
                        },
                        {
                            data: 'net_income_per_share',
                            name: 'net_income_per_share'
                        },
                        {
                            data: 'stock_code',
                            name: 'stock_code'
                        }
                    ]
                })
            });
        });
    </script>
</body>


</html>
