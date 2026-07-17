<!doctype html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 30px;
        }

        .box {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
        }

        .status {
            display: inline-block;
            padding: 10px 20px;
            background: #2196F3;
            color: #fff;
            border-radius: 5px;
            font-size: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="box">
        <h2>Статус заказа обновлен</h2>

        <p>
            Заказ № <b>{{ $order->id }}</b>
        </p>

        <p>
            Новый статус:
        </p>

        <div class="status">
            {{ $order->status }}
        </div>
    </div>
</body>
</html>
