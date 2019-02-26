<script>
    var layer1 = {
    "type": "FeatureCollection",
    "features": [
        <?php
            require_once("../connection.php");
            $query1 = "SELECT * FROM proposal WHERE lat IS NOT NULL and lon IS NOT NULL";
            if (!($conn = dbConnect()))
                throw new Exception("mysql ".mysqli_connect_error());
            if (!($res = mysqli_query($conn, $query1)))
                throw new Exception("mysql ".mysqli_error($conn));
            while ($row = mysqli_fetch_assoc($res)) {
                print('{');
                print('"type": "Feature",');
                print('"geometry": { "type": "Point", "coordinates": ['.$row["lon"].','.$row["lat"].'] },');
                print('"properties":');
                print('{');
                print('"location": "<div id=\'fontMap\'><b>What</b>: '.$row["name"].'",');
                print('"info": "<b>How</b>: '.$row["description"].'",');
                print('"street": "<b>Where</b>: '.$row["address"].'",');
                print('"city": "<b>Who</b>: '.$row["available_positions"].' volontari</div>"');
                print('}');
                print('},');
            }
            mysqli_free_result($res);
            mysqli_close($conn);
        ?>
    ]
    };
</script>