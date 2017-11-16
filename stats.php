<?php include_once('header.php'); ?>
	
<?php
	$catQuery = "SELECT wWordScrambled.wId, wWordScrambled.wCatId, wWordScrambled.wText, wWordScrambled.wScrambled, wWordScrambled.wKeyIndex, wWordScrambled.wAnswers, wWordScrambled.wDiffFirst, wWordScrambled.wDiffSecond, wWordScrambled.wDifficulty, wCategory.wCatName FROM wWordScrambled JOIN wCategory ";
	$catQuery .= "WHERE wWordScrambled.wCatId = wCategory.wCatId ORDER BY wCatId";
		
	$results = $db->prepare($catQuery);
	$results->execute();
	
	$wArray = $results->fetchAll();
	
	// find out how many records were retrieved
	$numRows = $results->rowCount();
	
	$totalDifficulty = array();
	$catCount = array();
	
	foreach ($wArray as $row) {
		
	$wId 			= $row['wId'];
	$wCatId			= $row['wCatId'];
	$wCatName		= $row['wCatName'];
	$wText			= $row['wText'];
	$wScrambled		= $row['wScrambled'];
	$wKeyIndex		= $row['wKeyIndex'];
	$wAnswers		= unserialize($row['wAnswers']);
	$wDiffFirst		= $row['wDiffFirst'];
	$wDiffSecond	= $row['wDiffSecond'];
	$wDifficulty	= $row['wDifficulty'];
	$wAddedBy		= $row['wAddedBy'];
	
	// Increase count
	++$catCount[$wCatId];
	
	$totalDifficulty['total'] += $wDifficulty;
	$totalDifficulty[$wCatId] += $wDifficulty;
	}
?>
	
	<div id="section-stats">
		<div class="container">
			<div class="sixteen columns">
				<h3>Filter Statistics</h3>
				<?php
				
					for($i = 1; $i <= 10; $i++) {
						echo "Category $i <br />";
						echo "Total Difficulty: $totalDifficulty[$i] <br />";
						echo "Number of Words: $catCount[$i] <br />";
						echo "Average Difficulty: " . round($totalDifficulty[$i] / $catCount[$i], 2);
						echo "<br /><br />";
					}
					
					$averageDifficulty['total'] = $totalDifficulty['total'] / $numRows;
					echo round($averageDifficulty, 2);
					echo "<br />";
					echo $numRows;
				?>
				
				<br class="clear" />
			</div><!-- // .sixteen -->
		</div><!-- // .container -->
	</div><!-- // #section-stats -->
	
<?php include_once('footer.php'); ?>
