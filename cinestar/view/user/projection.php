<?php include __DIR__ . '/../_header.php'; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?rt=user/movie/<?php echo $movie->id;?>"><?php echo $movie->name ?></a></li>
        <li class="breadcrumb-item active">Make reservation</li>
    </ol>
</nav>
<h1 class="h2"><?php echo $movie -> name; ?></h1>
<br><br>

<canvas id="myBoard" width="900" height="500"></canvas>



<?php include __DIR__ . '/../_footer.php'; ?>