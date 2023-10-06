<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Import Export Excel & CSV in Laravel 9</title>
    {{-- we will use Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5 text-center">
        <h2 class="mb-5">
            Laravel 10 Import and Export CSV & Excel to Database
        </h2>

        <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data"
            class="row row-cols-lg-auto g-2 align-items-center justify-content-md-center mt-5 mb-3">
            @csrf
            <div class="col-12">
                <input type="file" name="file" class="form-control" required>
            </div>

            <div class="col-12">
                <button class="btn btn-primary" type="submit">Import data</button>
            </div>

            <div class="col-12">
                <a class="btn btn-success" href="{{ route('users.export') }}">Export data</a>
            </div>
        </form>

        <div class="py-4">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <td>#</td>
                        <td>Name</td>
                        <td>Email</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $key => $user)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
