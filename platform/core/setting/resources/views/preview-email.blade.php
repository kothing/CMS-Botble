<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ trans('core/setting::setting.preview') }}</title>

    <style>
        body {
            font-family: Roboto, Helvetica, Arial, sans-serif;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {

            font-size: 15px;
            padding-bottom: 10px;
        }

        .btn-primary {
            color: #fff !important;
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }

        .btn-secondary {
            color: #fff !important;
            background-color: #6c757d !important;
            border-color: #6c757d !important;
        }

        .btn {
            display: inline-block;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: center;
            text-decoration: none;
            vertical-align: middle;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            border-radius: 0.25rem;
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .container {
            margin-top: 30px;
            display: flex;
            justify-content: center;
        }

        .iframe {
            margin-right: 30px;
            width: 900px;
            overflow: hidden;
            height: 100vh;
        }

        @media (max-width: 576px) {
            .container {
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
        }

        h3 {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="iframe">
            <iframe src="{{ $iframeUrl . ($inputData ? '?' . http_build_query($inputData) : '') }}" frameborder="0" width="100%" height="100%"></iframe>
        </div>
        <div>
            <h3>{{ trans('core/setting::setting.enter_sample_value') }}</h3>
            <form method="POST">
                @csrf
                @foreach($variables as $key => $variable)
                    <div class="form-group">
                        <label class="form-label" for="txt-{{ $key }}">{{ trans($variable) }}</label>
                        <input type="text" class="form-control" id="txt-{{ $key }}" name="{{ $key }}" value="{{ Arr::get($inputData, $key) }}">
                    </div>
                @endforeach
                <button type="submit" class="btn btn-primary">{{ trans('core/setting::setting.submit') }}</button>
                <a class="btn btn-secondary" href="{{ $backUrl }}">{{ trans('core/setting::setting.back') }}</a>
            </form>
        </div>
    </div>
</body>
</html>
