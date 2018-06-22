<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <style>
        .green {
            background-color: green;
            color: black;
        }

        .red {
            background-color: red;
            color: black;
        }
    </style>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">


    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

@if(isset($message))
    {{$message}}
@endif


<form method="POST" action="{{route('checkRobotTxt')}}">
    {{csrf_field()}}
    <input type="text" name="siteAddress"/>
    <input type="submit" value="Проверьте сейчас"/>
</form>



<?php $address = session('address'); ?>
@if(isset($address))
    <input type="button" onclick="tableToExcel('testTable', 'insvisions test case')" value="Export to Excel">
    <br/>   <h1>запрос сайта: {{session('address')}}</h1>
@endif

<div class="flex-center position-ref full-height">
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">Home</a>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    @endif

    @if(isset($array))
        <table class="table" id="testTable">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">№</th>
                <th scope="col">Название проверки</th>
                <th scope="col">Статус</th>
                <th scope="col"></th>
                <th scope="col">Текущее состояние</th>
            </tr>
            </thead>
            <tbody>

            @foreach($array as $item)
                <tr>
                    <th scope="row">{{$item['no'] ?? ''}}</th>
                    <td>{{$item['no'] ?? ''}}</td>
                    <td>{{$item['testName'] ?? ''}}</td>

                    <td @if($item['status'] == 'Ок') class="green" @else class="red" @endif>{{$item['status'] ?? ''}}</td>
                    <td>Состояние</td>
                    <td>{{$item['condition'] ?? ''}}</td>
                </tr>
                <tr>
                    <th scope="row"></th>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Рекомендации</td>
                    <td>{{$item['currentState'] ?? ''}}</td>
                </tr>

                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

    {{--<div class="content">--}}
    {{--<div class="title m-b-md">--}}
    {{--Laravel--}}
    {{--</div>--}}

    {{--<div class="links">--}}
    {{--<a href="https://laravel.com/docs">Documentation</a>--}}
    {{--<a href="https://laracasts.com">Laracasts</a>--}}
    {{--<a href="https://laravel-news.com">News</a>--}}
    {{--<a href="https://forge.laravel.com">Forge</a>--}}
    {{--<a href="https://github.com/laravel/laravel">GitHub</a>--}}
    {{--</div>--}}
    {{--</div>--}}
</div>
<script>
  var tableToExcel = (function() {
    var uri = 'data:application/vnd.ms-excel;base64,'
      , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
      , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
      , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
    return function(table, name) {
      if (!table.nodeType) table = document.getElementById(table)
      var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
      window.location.href = uri + base64(format(template, ctx))
    }
  })()
</script>

</body>
</html>
