<!doctype html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 30px;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            padding: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        th {
            background: #fafafa;
        }

        .total {
            text-align: right;
            font-size: 18px;
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Спасибо за заказ!</h2>

        <p>
            Заказ № <b>{{ $order->id }}</b>
        </p>

        <p>
            Статус:
            <b>{{ $order->status }}</b>
        </p>

        <table>
            <thead>
                <tr>
                    <th>Товар</th>
                    <th>Количество</th>
                    <th>Цена</th>
                    <th>Сумма</th>
                </tr>
            </thead>

            <tbody>
                @foreach($order->positions as $position)
                    <tr>
                        <td>{{ $position->product->name }}</td>
                        <td>{{ $position->count }}</td>
                        <td>{{ number_format($position->price, 2, '.', ' ') }}</td>
                        <td>{{ number_format($position->price * $position->count, 2, '.', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>

        <div class="total">
            Итого:
            {{ number_format($order->positions->sum(fn($p) => $p->price * $p->count), 2, '.', ' ') }}
        </div>
    </div>
</body>
</html>
