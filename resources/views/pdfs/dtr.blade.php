<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daily Time Record</title>
    <style>
        html { margin: 0px; margin-left: 10px;}
        table,
        td,
        tr,
        th {

            border-collapse: collapse;
            text-align: center;
            font-family: arial;
            font-size: 12px;
            border-spacing: 0;
            border: 1px solid black;
        }

        div {
            padding-left: 230px;
        }

        p {
            text-align: left;
            font-family: arial;
            font-size: 12px;
        }

        p.civil_service_title {
            text-align: left;
            font-family: arial;
            font-size: 10px;
        }

        p.civil_service_title2 {
            padding-left: 10px;
            font-family: arial;
            font-size: 12px;
        }

        p.name {
            padding-left: 170px;
            font-family: arial;
            font-size: 12px;
        }

        p.dtr {
            padding-left: 100px;
            font-family: arial;
            font-weight: bold;
            font-size: 16px;
        }

        p.circles {
            padding-left: 150px;
            font-family: arial;
            font-size: 12px;
        }

        p.line1 {
            padding-left: 40px;
            font-family: arial;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <main>
        <div>
            <caption style="position: relative;">
                <EM>
                <p class="civil_service_title">Civil Service Form No. 48</p>
                <p class="dtr">DAILY TIME RECORD </p>
                <p class="circles">-----o0o-----</p>
                <p style="position: absolute; top: 100px; left: 130px; font-weight:bold; font-style:normal; font-size: 14px;">{{ $data['name'] }}</p>
                <p class="line1">_____________________________________</p>
                <p class="name">     (Name)</p>
                <p style="position: absolute; top: 170px; left: 120px; font-weight:bold; font-style:normal; font-size: 14px;">{{ $data['month'] }}</p>
                <p class="civil_service_title2"> For the month of______________________________________<br>
                Official hours for arrival <br>and departure
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;
                Regular days________________<br><br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                 Saturdays___________________ </p>
                </EM>
            </caption>
            <table border="1">
                <tr>
                    <th rowspan="2">Day</th>
                    <th colspan="2">A.M.</th>
                    <th colspan="2">P.M.</th>
                    <th colspan="2">Undertime</th>
                <tr>
                <th>Arrival</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Departure</th>
                <th>Hours</th>
                <th>Minutes</th>
                @foreach ($data['collection'] as $item)
                    <tr>
                        <th>{{ $item['day'] }}</th>
                        <td>{{ $item['am_start_time'] }}</td>
                        <td>{{ $item['am_end_time'] }}</td>
                        <td>{{ $item['pm_start_time'] }}</td>
                        <td>{{ $item['pm_end_time'] }}</td>
                        <td>{{ $item['overtime_start_time'] }}</td>
                        <td>{{ $item['overtime_end_time'] }}</td>
                    </tr>
                @endforeach
                <tr><th colspan="5">
                <div>Total</div>
                <td></td>
                <td></td>
            </table>
            <p>I certify on my honor that the above is a true and correct report of the <br>
                hours of work performed, record of which was made daily at the time <br>
                 of arrival and departure from office. </p><br>
                ___________________________________________
                <br>
                    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;
                        <strong>(Signature)</strong>
                    </p>
                <p> VERIFIED as to the prescribed office hours:</p>
                <br>
                ___________________________________________<br>
                <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   &nbsp;&nbsp;
                    <strong>(In-charge)</strong>
                </p>
        </div>
    </main>
</body>
</html>



