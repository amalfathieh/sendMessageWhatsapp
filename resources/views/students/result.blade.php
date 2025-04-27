<!DOCTYPE html>
<html>

<head>
    <title>نتيجة الارسال</title>
</head>
<body>
<div class="" style="margin: 50px">

    <h2>نتيجة الارسال</h2>
        <table border="1">
            <thead>
            <tr>
                <th >الرقم</th>
                <th >الرسالة</th>
                <th >الحالة</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($results as $res)
                <tr>
                    <td>{{ $res['phone'] }}</td>
                    <td>{{ $res['message'] }}</td>
                    <td>{{ $res['status'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
</div>
</body>
