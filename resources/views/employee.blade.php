<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel 8 PDF Example</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>
        <h2 class="text-center mb-3">Laravel HTML to PDF Example</h2>
        <table class="table table-bordered mb-5">
            <thead>
                <tr class="table-danger">
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">img</th>
                   
                </tr>
            </thead>
            <tbody>
                @foreach($locations as $location)
                <tr>
                    <th scope="row">{{ $location->id }}</th>
                    <td>{{ $location->name }}</td>
                    <td>{{ $location->img }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</body>

</html>