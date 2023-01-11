
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <script src="./script.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
    <title>Meteo Velletri</title>
</head>
<body>
    <div class="titolo">
        <h1>Meteo Velletri</h1>
        <h1 id="orologio"></h1>
    </div>

    <?php 
        $i=0;
        $meteo;
        $conn = new mysqli("localhost","root","","meteo:prova_ufficiale");
        if($conn===false){
            echo "<p>connessione non effettuata</p>".$conn->connect_error;
        }
        
        $sql = "SELECT * FROM meteo";
        if($risultato= $conn->query($sql)){
            if($risultato->num_rows > 0){
                while($row= $risultato->fetch_array()){
                    $id[$i]=$row["id"];
                    $umidita[$i]=$row["umidita"];
                    $pressione[$i]=$row["pressione"];
                    $direzione[$i]=$row["dir-vent"];
                    $velocita[$i]=$row["velocita"];
                    $temperatura[$i]=$row["temperatura"];
                    $data[$i]=$row["data"];
                    ++$i;
                }
            }
        }
        
    ?>
    <div class="page">

        <div class="info" id="infolarghezza1">
            <?php 
            if($pressione[$i-1]<100){
                $meteo="nevoso";
            }
            elseif($pressione[$i-1]>100 && $pressione[$i-1]<400 ){
                $meteo="Piovoso";
            }
            elseif($pressione[$i-1]>400 && $pressione[$i-1]<800 ){
                $meteo="Nuvoloso";
            }
            elseif($pressione[$i-1]>=800){
                $meteo="Sereno";
            }?>
            <h6>Misurazione n. <?php echo $id[$i-1]?></h6>

            <h3>Misurazione giornaliera</h3>

            <div>
                <div>
                    <h3>Gradi: <?php echo $temperatura[$i-1]?> °</h3>
                    
                    <h3>Umidita:<?php echo $umidita[$i-1]?>%</h3>
                    
                    <h3>Pressione: <?php echo $pressione[$i-1]?> hPa</h3>
                </div>
                    
                <div>
                    <h3>Direzione vento: <?php echo $direzione[$i-1]?></h3>

                    <h3>Velocità del vento: <?php echo $velocita[$i-1]?>Km-h </h3>
                    
                    <h3>Cielo <?php echo $meteo?> </h3>
                </div>
            </div>
        
            <h6>Data e ora misurazione: <?php echo $data[$i-1]?> </h6>

        </div>

        <div class="zonaricerca">
            <div class="info" id="infolarghezza2">
                <h2>Benvenuto, inserisci la data della misurazione che vuoi controllare</h2>
                <form action="index.php" method="post">
                    <label for="Data">Data</label>
                    <input type="date" name="dataIn">
                    <input type="submit" value="Cerca">
                </form>
                
            <?php 
            $permesso=true;
            $contatore=0;
            if(isset($_POST["dataIn"])){
                $scelta=$_POST["dataIn"];
                for($k=0;$k<$i;++$k){
                    if($scelta==substr($data[$k], 0, 10)){
                        $contatore=$k;
                        $permesso =true;
                    }else{
                        $permesso=false;
                        
                    }
                }
            }

            ?>
                <?php 
                if($pressione[$i-1]<100){
                    $meteo="nevoso";
                }
                elseif($pressione[$i-1]>100 && $pressione[$i-1]<400 ){
                    $meteo="Piovoso";
                }
                elseif($pressione[$i-1]>400 && $pressione[$i-1]<800 ){
                    $meteo="Nuvoloso";
                }
                elseif($pressione[$i-1]>=800){
                    $meteo="Sereno";
                }?>
                <h6>Misurazione n. <?php echo $id[$contatore]?></h6>
                    
                <h3>Misurazione giornaliera</h3>

                <div>
                    <div>
                        <h3>Gradi: <?php echo $temperatura[$contatore]?> °</h3>
                        
                        <h3>Umidita:<?php echo $umidita[$contatore]?>%</h3>
                        
                        <h3>Pressione: <?php echo $pressione[$contatore]?> hPa</h3>
                    </div>
                                
                    <div>
                        <h3>Direzione vento: <?php echo $direzione[$contatore]?></h3>

                        <h3>Velocità del vento: <?php echo $velocita[$contatore]?>Km-h </h3>
                        
                        <h3>Cielo <?php echo $meteo?> </h3>
                    </div>
                </div>
                <h6>Data e ora misurazione: <?php echo $data[$contatore]?> </h6>
            </div>
            
        </div>
    </div>
    
    

    <div class="google-maps">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d119353.67344546747!2d12.708443897629602!3d41.66142618973117!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x13259d3837d7c5e3%3A0x55f6bd41422d989d!2s00049%20Velletri%20RM!5e1!3m2!1sit!2sit!4v1670597366816!5m2!1sit!2sit" width="800" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>    
    </div>

</body>       
</html>