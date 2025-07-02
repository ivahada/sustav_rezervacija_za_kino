
<?php include __DIR__ . '/../_header.php'; ?>


<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?rt=user">Home page</a></li>
        <li class="breadcrumb-item active">My reservations</li>
    </ol>
</nav>
<h1 class="h2">My reservations</h1>



<div class="card">
    <h5 class="card-header">My reservations</h5>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">Reservation ID</th>
                    <th scope="col">Movie</th>
                    <th scope="col">Hall</th>
                    <th scope="col">Date and Time</th>
                    <th scope="col">Number of Tickets</th>
                    <th scope="col">Open QR code</th>
                    <th scope="col"></th>
                   
                    </tr>
                </thead>
                <tbody>
                   <?php
                    foreach( $reservationList as $reservation){
                        echo '<tr>';
                        echo '<td>' .$reservation['id'] .'</td>';
                        $str = '<h6>' . $reservation['movie']-> name. ' </h6>';
                        echo '<td>' . $str . '</td>';
                        $hall = $reservation['projection'] -> hall_id;
                        echo '<td>' . $hall . '</td>';
                        $date = datum($reservation['projection']->date);
                        $time = $reservation['projection'] -> time;
                        echo '<td>'. $date .' u ' . substr($time, 0, -3) . 'h </td>';
                        $num_of_tics = $reservation['tics'];
                        echo '<td>' . $num_of_tics . '</td>';
                        $qt="'";
                        echo '<td><button onClick="openCode('. $reservation['id'] . ',' . $qt.$ime.$qt. ');" class="btn btn-info">Open</button></td>';
                        echo '<td><button class="btn btn-warning"><a class="cancel" href="index.php?rt=user/cancel/'.$reservation['id']. '">Cancel reservation</a></button></td>';
                        echo '</tr>';
                    }
                   ?>
                   
                </tbody>
                </table>
        </div>
        
    </div>
</div>

<script>
function openCode(res_id,ime){
    var mywindow = window.open('', 'PRINT', 'height=450,width=500');

    mywindow.document.write('<html><head><title>' + document.title  + '</title>');
    mywindow.document.write('</head><body >');
    mywindow.document.write('<h1>' + document.title  + '</h1>');
    mywindow.document.write('<h2>This is your reservation confirmation.</h2>');
    mywindow.document.write( '<img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='+ "reservationId%3A"+ 
                            res_id + "%20" + "user%3A" + ime+
                            '&choe=UTF-8" title="Confirmation" />');
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/


return true;
}
</script>



<style>
.collapse-row.collapsed + tr {
  display: none;
}

a.cancel{
    text-decoration: none;
    color:black;
}

button.cancel{
    display: table;
    margin: 0 auto;
}

button.cancel:hover{
    background-color:lightgray;
}
</style>

<?php

/*$data_array = iterator_to_array($result);
echo("<br>");
echo("<br>");
print_r($data_array[0]);*/




include __DIR__ . '/../_footer.php'; ?>