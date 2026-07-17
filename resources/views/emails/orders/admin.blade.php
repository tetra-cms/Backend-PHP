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

        .image {
            width: 32px;
            height: 32px;
        }

        .row {
            display: flex;
            flex-direction: row;
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
        <h2>Поступил новый заказ</h2>

        <p>
            Новый заказ № <b>{{ $order->id }}</b>
        </p>

        <p>
            ФИО:
            <b>{{ $order->client->fcs }}</b>
        </p>

        <p>
            Адрес:
            <b>{{ $order->client->address . ' (' . $order->client->city . ')' }}</b>
        </p>

        <p>
            Телефон:
            <b>{{ $order->client->phone }}</b>
        </p>

        <p>
            Тип оплаты:
            <b>{{ __('order.payments.' . $order->payment_type->value) }}</b>
        </p>

        <p>
            Тип доставки:
            <b>{{ __('order.delivery.' . $order->delivery_type->value) }}</b>
        </p>

        <p>
            Комментарий:
            <b>{{ $order->client->comment }}</b>
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
                        <td class="row">
                            <img class="image" src="{{ url('api/products/image/' . $position->product->id) }}">
                            <b>{{ $position->product->name }}</b>
                        </td>
                        <td>{{ $position->quantity . ($position->product->supply_quantum ? " x " . $position->product->supply_quantum : '') }}</td>
                        <td>{{ number_format($position->price, 2, '.', ' ') . ($position->product->supply_quantum ? " x " . $position->product->supply_quantum : '') }}</td>
                        <td>{{ number_format($position->total_price, 2, '.', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>

        <div class="total">
            Итого:
            {{ number_format($order->total_price, 2, '.', ' ') }}
        </div>
    </div>
</body>
</html>
