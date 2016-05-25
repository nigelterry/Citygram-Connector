<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PermitReport */

?>

<?php
	echo '<div><table border="1">';
	foreach ( $data as $key => $row ) {
		echo '<tr>';
		echo "<td>$key</td>";
		foreach ( $row as $data ) {
			echo "<td>$data</td>";
		}
		echo '</tr>';
	}
	echo '</table></div>';
?>

