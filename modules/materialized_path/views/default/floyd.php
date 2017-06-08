<?php
echo "Таблица кратчайших путей:" . "<br/>";
    echo "<table border='1'>";
    for ($i = 0; $i < $number; ++$i)
    {
        echo "<tr>";

        for ($j = 0; $j < $number; ++$j)
        {
            if ($distance[$i][$j] == $inf)
                echo "<td>"."INF"."</td>";
            else
                echo "<td>".$distance[$i][$j]."</td>";//str_pad($distance[$i][$j], 7);
        }
        echo "<tr/>";
    }
    echo "</table>";
?>