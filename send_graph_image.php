<?php
// グラフライブラリクラスのビルダーのインクルード
require_once './graph/GraphBuilder.class.php';

// グラフデータの取得
$data = array();
foreach ($_GET as $key => $value) {
	$data[$key] = $value; 
}

//$graph = GraphBuilder::factory('BarGraph', $data, 'jpeg');
$graph = GraphBuilder::factory('CircleGraph', $data, 'jpeg');
//$graph = GraphBuilder::factory('LineGraph', $data, 'jpeg');

// グラフイメージを取得・base64エンコードして出力
$graphImage = $graph->fetch();
echo base64_encode($graphImage);
?>