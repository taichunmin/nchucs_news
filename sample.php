<?php
	include_once("include.php");
	include_once("header.php");
	if(!$ses->uid) tai_location('index.php');
	$title = array(
		'groupByDate' => '依日期看新聞',
		'groupByCategory' => '依分類看新聞',
	);
?>
	<div data-role="page" id="-<?php echo $_GET['act']; ?>" data-add-back-btn="true">
		<div data-role="header" data-position="fixed">
			<h1><?php echo $title[$_GET['act']]; ?></h1>
			<a href="index.php?act=logout" data-icon="alert" data-direction="reverse" class="ui-btn-right" data-ajax="false">登出</a>
<?php echo $pagenav; ?>
		</div>
		<div data-role="content">
<?php
//==================================================================================================
//==================================================================================================
?>
		</div>
<?php
include_once('footer.php');
?>
