<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storico</title>

    <link rel="icon" type="image/png" sizes="16x16" href="../img/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/favicon-32x32.png">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    
</head>
<body onresize="drawChart()" id="body">

    <!-- sfondi -->

    <video src="../img/Nuvole - 8599.mp4" autoplay loop muted></video>
    <div class="sfondo"></div>

    <!-- barra superiore di navigazione -->

    <nav class="navigazione">
        <a href="../index.php">
            <span class="material-symbols-outlined">
                home
            </span>
        </a> 
        <a href="./chi_siamo.php">Chi siamo</a>
        <?php
            require('../functions.php');
            if(!isset($_SESSION['loginUID'])) echo '<a href="./login.php">Accedi</a>';
            else echo '<a href="../auth/adminpanel.php">Admin Panel</a><a href="../auth/logout.php">Logout</a>';
        ?>
    </nav>

    <!-- zona di ricerca -->

    <div id="spazio"></div>

    <div class="page">
        <div class="zonaricerca">
            <h2>
                Benvenuto, inserisci la data della misurazione che vuoi controllare
            </h2>

            <form action="storico.php" method="post" class="formale">
                <label for="Data">Data</label>
                <input type="date" name="dataIn">
                <button type="submit" id="rec">
                    <h2>cerca</h2> 
                    <span class="material-symbols-outlined">
                        search
                    </span>
                </button>
            </form>
            
        </div>
    </div>

    <?php
    $dataSelezionata = null;
    if($_POST) {
            
        function cielo($res) { // $res MUST be an array containing numeric values labeled as "umidita" and "temperatura"
            if($res["umiditaMedia"] >= 90 && $res["temperaturaMedia"] <= 15) {
                return "Pioggia";
            } else if($res["umiditaMedia"] <= 85 && $res["umiditaMedia"] >= 75 && $res["temperaturaMedia"] <= 21) {
                return "Cielo Nuvoloso";
            } else if($res["umiditaMedia"] <= 50 ) {
                return "Cielo Soleggiato";
            } else {
                return "Cielo Parzialmente nuvoloso";
            }
        }

        $date = new DateTime($_POST["dataIn"]);
        $dataSelezionata = new DateTime($date->format("Y-m-d H:i:s"));        
        $year = $date->format("Y");
        
        $date->setTime(23,9,4); // set time at last measure for day Hour:minute:second 
        //$sql = "SELECT * FROM `y$year` WHERE DATE(data)='".$date->format('Y-m-d')."';";           
        //$res = $db->query($sql);
        
        $res = getData($db, $date->format('Y-m-d'));            
        

        if( $res && isset($res['data']) && isset($res['temperaturaMedia'])) {     
            echo '<div class="page" id="zona"> 
                    <div class="pacchetto">
                        <div class="info" id="infolarghezza1">
                            <h3 class="bordo">Misurazione giornaliera</h3>
                            <h3 class="bordo">Data: '. extractDate($res['data']) .'</h3>
                            <h3 class="bordo">'.cielo($res).'</h3>
                            <h3 class="bordo">Temperatura media: '.$res['temperaturaMedia'].'°</h3>
                            <h3 class="bordo">Temperatura max: '.$res['maxTemperatura'].'° - Ore: ' . substr($res['oraMaxTemperatura'],0,5) . ' </h3>
                            <h3 class="bordo">Temperatura min: '.$res['minTemperatura'].'° - Ore: ' . substr($res['oraMinTemperatura'],0,5) . ' </h3>
                            <h3 class="bordo">Umidita media: '.$res['umiditaMedia'].'%</h3>
                            <h3 class="bordo">Pressione media: '.$res['pressioneMedia'].' hPa</h3>
                            <h3 class="bordo">Direzione vento: '.$res['direzioneMaxVento'].'</h3>
                            <h3 class="bordo">Velocità media del vento: '.$res['mediaVelocitaVento'].' Km/h</h3>                                                      
                        </div>                        
                    </div>
                </div>';
        }else{
            echo '<div class="page" id="zona"> 
                    <div class="pacchetto">
                        <div class="info" id="infolarghezza1">
                            <h3 class="bordo">Misurazione giornaliera non disponibile</h3>
                            <h3 class="bordo">Data: ' . $date->format("d-m-Y") . '</h3>
                            <h3 class="bordo">Gradi: NA</h3>
                            <h3 class="bordo">Umidita: NA</h3>
                            <h3 class="bordo">Pressione: NA</h3>
                            <h3 class="bordo">Direzione vento: NA</h3>
                            <h3 class="bordo">Velocità del vento: NA</h3>
                            <h3>Misurazione n. NA<h3>                            
                        </div>                        
                    </div>
                </div>';
        }
    }
  
    ?>

    <div class="grafico2">
        <div id="curve_chart"></div>
    </div>

    <?php        
        $year = null;
        $currentMonth = null;
        if($dataSelezionata != null){
            $year = $dataSelezionata->format("Y");
            $currentMonth = (int) $dataSelezionata->format("m");
        }else{
            $date = new DateTime(date('Y/m/d H:i:s'));
            $year = $date->format("Y");
            $currentMonth = (int) $date->format("m");
        }        
         
        $dayOfMonth = -1;
        switch($currentMonth){
            case 11:
            case 4:
            case 6:
            case 9:{
                $dayOfMonth = 30;
                break;
            }
            case 2:{
                $dayOfMonth = 28;
                break;
            }
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
            case 10:
            case 12:{
                $dayOfMonth = 31;
                break;
            }    
            default:{
                die("Errore critico nel calcolo dei giorni del mese corrente");
            }
        }               
    ?>

    <script type="text/javascript" defer>
        console.log("on");

        let x = document.getElementById("body");
        let y = document.getElementById("body");
        let z;
        let k;

        
        <?php 
            $endDate = null;
            if($dataSelezionata != null){
                $endDate = $dataSelezionata;
            }else{
                $endDate = new DateTime(getLastDay($db));
            }
            
            $startDate = (new DateTime($endDate->format("Y-m-d H:i:s")))->modify("-31 day");
            $temperaturaMensile = [];
            $umiditaMensile = [];
            $giornoMese = [];
            for($i=30;$i>=0;--$i){
                $currentDay = (new DateTime($endDate->format("Y-m-d H:i:s")))->modify("-$i day");
                $res= getData($db,$currentDay->format("Y-m-d H:i:s"));
                //$res = $db->query("SELECT AVG(outHum) as umiditaMedia, AVG(tempOut) as temperaturaMedia FROM `y$year` WHERE DATE(data)= '".$currentDay->format('Y-m-d')."';");
                $temperaturaMensile[] = $res['temperaturaMedia'];
                
                $umiditaMensile[] = $res['umiditaMedia'];
                $giornoMese[] = $currentDay->format("M-d");
            }
            
        ?>


        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        
        const giorniSet = ["Lun","Mar","Mer","Gio","Ven","Sab","Dom"];
        let giorno = <?php echo ($date->format("d"));?>;
        let giornoSet = giorniSet[<?php echo ($date->format("N")-1);?>]
        
        function drawChart() {
            console.log(x.clientWidth);
            if(x.clientWidth/2 <= 441){
                z = x.clientWidth*0.90;
                k = y.clientHeight/4;
            }else if(x.clientWidth/2 >= 700){
                z= x.clientWidth/1.5;
                k = y.clientHeight/2;
            }else{
                z= x.clientWidth/1.5;
                k = y.clientHeight/3;
            }

            var data = google.visualization.arrayToDataTable([
                ['Giorno', 'Temperatura', 'Umidità'],
                ['<?php echo $giornoMese[0]; ?>',         <?php echo (($temperaturaMensile[0]==null)?0:$temperaturaMensile[0])?>, <?php echo (($umiditaMensile[0]==null)?0:$umiditaMensile[0]) ?>],
                ['<?php echo $giornoMese[1]; ?>',         <?php echo (($temperaturaMensile[1]==null)?0:$temperaturaMensile[1])?>, <?php echo (($umiditaMensile[1]==null)?0:$umiditaMensile[1]) ?>],
                ['<?php echo $giornoMese[2]; ?>',         <?php echo (($temperaturaMensile[2]==null)?0:$temperaturaMensile[2])?>, <?php echo (($umiditaMensile[2]==null)?0:$umiditaMensile[2]) ?>],
                ['<?php echo $giornoMese[3]; ?>',         <?php echo (($temperaturaMensile[3]==null)?0:$temperaturaMensile[3])?>, <?php echo (($umiditaMensile[3]==null)?0:$umiditaMensile[3]) ?>],
                ['<?php echo $giornoMese[4]; ?>',         <?php echo (($temperaturaMensile[4]==null)?0:$temperaturaMensile[4])?>, <?php echo (($umiditaMensile[4]==null)?0:$umiditaMensile[4]) ?>],
                ['<?php echo $giornoMese[5]; ?>',         <?php echo (($temperaturaMensile[5]==null)?0:$temperaturaMensile[5])?>, <?php echo (($umiditaMensile[5]==null)?0:$umiditaMensile[5]) ?>],
                ['<?php echo $giornoMese[6]; ?>',         <?php echo (($temperaturaMensile[6]==null)?0:$temperaturaMensile[6])?>, <?php echo (($umiditaMensile[6]==null)?0:$umiditaMensile[6]) ?>],
                ['<?php echo $giornoMese[7]; ?>',         <?php echo (($temperaturaMensile[7]==null)?0:$temperaturaMensile[7])?>, <?php echo (($umiditaMensile[7]==null)?0:$umiditaMensile[7]) ?>],
                ['<?php echo $giornoMese[8]; ?>',         <?php echo (($temperaturaMensile[8]==null)?0:$temperaturaMensile[8])?>, <?php echo (($umiditaMensile[8]==null)?0:$umiditaMensile[8]) ?>],
                ['<?php echo $giornoMese[9]; ?>',        <?php echo (($temperaturaMensile[9]==null)?0:$temperaturaMensile[9])?>, <?php echo (($umiditaMensile[9]==null)?0:$umiditaMensile[9]) ?>],
                ['<?php echo $giornoMese[10]; ?>',        <?php echo (($temperaturaMensile[10]==null)?0:$temperaturaMensile[10])?>, <?php echo (($umiditaMensile[10]==null)?0:$umiditaMensile[10]) ?>],
                ['<?php echo $giornoMese[11]; ?>',        <?php echo (($temperaturaMensile[11]==null)?0:$temperaturaMensile[11])?>, <?php echo (($umiditaMensile[11]==null)?0:$umiditaMensile[11]) ?>],
                ['<?php echo $giornoMese[12]; ?>',        <?php echo (($temperaturaMensile[12]==null)?0:$temperaturaMensile[12])?>, <?php echo (($umiditaMensile[12]==null)?0:$umiditaMensile[12]) ?>],
                ['<?php echo $giornoMese[13]; ?>',        <?php echo (($temperaturaMensile[13]==null)?0:$temperaturaMensile[13])?>, <?php echo (($umiditaMensile[13]==null)?0:$umiditaMensile[13]) ?>],
                ['<?php echo $giornoMese[14]; ?>',        <?php echo (($temperaturaMensile[14]==null)?0:$temperaturaMensile[14])?>, <?php echo (($umiditaMensile[14]==null)?0:$umiditaMensile[14]) ?>],
                ['<?php echo $giornoMese[15]; ?>',        <?php echo (($temperaturaMensile[15]==null)?0:$temperaturaMensile[15])?>, <?php echo (($umiditaMensile[15]==null)?0:$umiditaMensile[15]) ?>],
                ['<?php echo $giornoMese[16]; ?>',        <?php echo (($temperaturaMensile[16]==null)?0:$temperaturaMensile[16])?>, <?php echo (($umiditaMensile[16]==null)?0:$umiditaMensile[16]) ?>],
                ['<?php echo $giornoMese[17]; ?>',        <?php echo (($temperaturaMensile[17]==null)?0:$temperaturaMensile[17])?>, <?php echo (($umiditaMensile[17]==null)?0:$umiditaMensile[17]) ?>],
                ['<?php echo $giornoMese[18]; ?>',        <?php echo (($temperaturaMensile[18]==null)?0:$temperaturaMensile[18])?>, <?php echo (($umiditaMensile[18]==null)?0:$umiditaMensile[18]) ?>],
                ['<?php echo $giornoMese[19]; ?>',        <?php echo (($temperaturaMensile[19]==null)?0:$temperaturaMensile[19])?>, <?php echo (($umiditaMensile[19]==null)?0:$umiditaMensile[19]) ?>],
                ['<?php echo $giornoMese[20]; ?>',        <?php echo (($temperaturaMensile[20]==null)?0:$temperaturaMensile[20])?>, <?php echo (($umiditaMensile[20]==null)?0:$umiditaMensile[20]) ?>],
                ['<?php echo $giornoMese[21]; ?>',       <?php echo (($temperaturaMensile[21]==null)?0:$temperaturaMensile[21])?>, <?php echo (($umiditaMensile[21]==null)?0:$umiditaMensile[21]) ?>],
                ['<?php echo $giornoMese[22]; ?>',       <?php echo (($temperaturaMensile[22]==null)?0:$temperaturaMensile[22])?>, <?php echo (($umiditaMensile[22]==null)?0:$umiditaMensile[22]) ?>],
                ['<?php echo $giornoMese[23]; ?>',       <?php echo (($temperaturaMensile[23]==null)?0:$temperaturaMensile[23])?>, <?php echo (($umiditaMensile[23]==null)?0:$umiditaMensile[23]) ?>],
                ['<?php echo $giornoMese[24]; ?>',       <?php echo (($temperaturaMensile[24]==null)?0:$temperaturaMensile[24])?>, <?php echo (($umiditaMensile[24]==null)?0:$umiditaMensile[24]) ?>],
                ['<?php echo $giornoMese[25]; ?>',       <?php echo (($temperaturaMensile[25]==null)?0:$temperaturaMensile[25])?>, <?php echo (($umiditaMensile[25]==null)?0:$umiditaMensile[25]) ?>],
                ['<?php echo $giornoMese[26]; ?>',       <?php echo (($temperaturaMensile[26]==null)?0:$temperaturaMensile[26])?>, <?php echo (($umiditaMensile[26]==null)?0:$umiditaMensile[26]) ?>],
                ['<?php echo $giornoMese[27]; ?>',       <?php echo (($temperaturaMensile[27]==null)?0:$temperaturaMensile[27])?>, <?php echo (($umiditaMensile[27]==null)?0:$umiditaMensile[27]) ?>],
                ['<?php echo $giornoMese[28]; ?>',       <?php echo (($temperaturaMensile[28]==null)?0:$temperaturaMensile[28])?>, <?php echo (($umiditaMensile[28]==null)?0:$umiditaMensile[28]) ?>],
                ['<?php echo $giornoMese[29]; ?>',       <?php echo (($temperaturaMensile[29]==null)?0:$temperaturaMensile[29])?>, <?php echo (($umiditaMensile[29]==null)?0:$umiditaMensile[29]) ?>],
                ['<?php echo $giornoMese[30]; ?>',       <?php echo (($temperaturaMensile[30]==null)?0:$temperaturaMensile[30])?>, <?php echo (($umiditaMensile[30]==null)?0:$umiditaMensile[30]) ?>]
            ]);

            if(x.clientWidth>1355){

                var options = {
                    title: 'Temperatura e Umidità Mensile',
                    curveType: 'function',
                    legend: { position: 'bottom' },
                    width: Math.trunc(z),
                    height: Math.trunc(k),
                    series: {
                        0: { color: '#e2431e' },
                        1: { color: '#43459d' },
                        2: { color: '#f1ca3a' },
                        3: { color: '#6f9654' },
                        4: { color: '#1c91c0' },                    
                        5: { color: '#e7711b' }
                    }
                };

                var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

                chart.draw(data, options);
            }else{
                
                var options = {
                    title: 'Temperatura e Umidità Mensile',
                    curveType: 'function',
                    legend: { position: 'bottom' },
                    width: Math.trunc(z),
                    height: Math.trunc(k),
                    series: {
                        0: { color: '#e2431e' },
                        1: { color: '#43459d' },
                        2: { color: '#f1ca3a' },
                        3: { color: '#6f9654' },
                        4: { color: '#1c91c0' },                    
                        5: { color: '#e7711b' }
                    },
                    hAxis: {
                        textStyle: {
                            fontSize: 9
                        }
                    }                    
                };

                var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

                chart.draw(data, options);
            }
            
        }
    </script>
    <?php
        $db->close();
    ?>
</body>
</html>