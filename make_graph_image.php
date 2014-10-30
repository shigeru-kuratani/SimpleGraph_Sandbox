<?php
// グラフライブラリ ビルダークラスのインクルード
require_once './graph/GraphBuilder.class.php';

// グラフデータの取得
$data = array();
foreach ($_GET as $key => $value) {
	$data[$key] = $value;
}

// グラフオブジェクトの生成
$graph = GraphBuilder::factory('BarGraph', $data, 'jpeg');
//$graph = GraphBuilder::factory('CircleGraph', $data, 'jpeg');
//$graph = GraphBuilder::factory('LineGraph', $data, 'jpeg');

// グラフイメージの出力
$graph->flush();
?>
