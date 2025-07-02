
<?php include __DIR__ . '/../_header.php'; ?>



<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?rt=user">Home page</a></li>
        <li class="breadcrumb-item active">Movies</li>
    </ol>
</nav>
<h1 class="h2">Movies <span class="text"></span></h1>
<p>Choose which movie you want to see</p>



<script src="js/search/search.js"></script>
<div class="card">
    <h5 class="card-header">
        <div class="input-group mb-3">
            <div class="input-group-prepend">
            <span class="input-group-text" id="basic-addon3">Search</span>
            </div>
            <input type="text" id="search" onkeyup="search()" placeholder="type in movie name" class="form-control" >
        </div> 
    
    </h5>
   
   
    <div class="card-body">
        


               
        <?php

            $i=0;
            foreach( $movieList as $movie){
                
                if($i % 3 == 0){
                    echo '<div class="row">';
                }
                echo '<div style="margin-top: 50px;" class="col">';
                $img = $movie['movie']-> name . '.jpg';
               
                $str = '<h4><a class="title" href="index.php?rt=user/movie/'. $movie['movie']-> id .'">' . $movie['movie']-> name. '</a></h4> ';
                echo $str . '<img src="img/'. $img .'">';
                echo '</tr>';

                echo '</div>';
                if($i % 3 == 2){
                    echo ' </div>';
                    
                }

                $i +=1;
            }
            if($i % 3 == 2){
                
                echo '<div style width="30%;"class="col">';
                    echo ' </div>'; //GOTOV ROW
            }else if($i % 3 == 1){
                echo '<div style width="30%;"class="col">';
                echo ' </div>'; //GOTOV ROW
                echo '<div style width="30%;"class="col">';
                    echo ' </div>'; //GOTOV ROW
            }
                
                
        ?>
                    
    </div>
</div>

<style>
a.title{
    text-decoration: none;
    color:black;
}
a.title:hover{
    background-color:lightgray;
}
div.img{
    /*height:25%;*/
    width:25%;
}
img{
    width:300px;
    
}
</style>




<?php include __DIR__ . '/../_footer.php'; ?>