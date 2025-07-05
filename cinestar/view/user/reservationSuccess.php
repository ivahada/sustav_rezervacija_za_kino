
<?php include __DIR__ . '/../_header.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?rt=user">Početna stranica</a></li>
        <li class="breadcrumb-item active">Potvrda rezervacije</li>
    </ol>
</nav>

<div id="elem">
    <h1 class="h2">Reservation confirmation <?php echo $reservation_id;?></h1>
    <p>You can print this confirmation or show it over the phone to cinema employee.</p>
    <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=<?php echo "reservationId%3A". $reservation_id . "%20" . "user%3A" . $ime;?>&choe=UTF-8" title="Confirmation" />

</div>


<button onClick="printIt()" class="btn btn-info">Print it</button>


<script>
function printIt(){
    var mywindow = window.open('', 'PRINT', 'height=800,width=600');

    mywindow.document.write('<html><head><title>' + document.title  + '</title>');
    mywindow.document.write('</head><body >');
    mywindow.document.write('<h1>' + document.title  + '</h1>');
    mywindow.document.write('<h2>This is your reservation confirmation.</h2>');
    mywindow.document.write(document.getElementById("elem").innerHTML);
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    mywindow.print();
    mywindow.close();

    return true;
}
</script>

<?php include __DIR__ . '/../_footer.php'; ?>