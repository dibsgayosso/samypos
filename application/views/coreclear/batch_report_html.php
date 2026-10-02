<!DOCTYPE html>
<head>

</head>
<body>

<?php foreach ($data as $title=>$tabular_data) { ?>
<h1><?php echo $title;?></h1>
<table width="100%">
<?php foreach($tabular_data as $row) { ?>
	<tr>
	<?php foreach($row as $cell) { ?>
		<td><?php echo $cell; ?></td>
	<?php } ?>
	</tr>
<?php } ?>
	
</table>
<?php } ?>
</body>
</html>