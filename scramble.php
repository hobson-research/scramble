<?php include_once('header.php'); ?>
	
<?php 
	// zCategory
	$catId = $_GET["catId"];
	if( !isset($catId) ) {
		$catId = 1;
	}
	
	if( isset( $_POST["submit"] ) ) {
		
		$zScrambleCatId = $_POST['zScrambleCatId'];
		$zDiffLowerLimit = $_POST['zDiffLowerLimit'];
		$zDiffUpperLimit = $_POST['zDiffUpperLimit'];
		
		if( !isset($zDiffLowerLimit) ) {
			$zDiffLowerLimit = 0;
		}
		
		if( !isset($zDiffUpperLimit) ) {
			$zDiffUpperLimit = 9999;
		}
		
		// submit the query and capture the result
		$getFilteredPhrases = "SELECT wId, wCatId, wText, wKeyIndex, wAddedBy FROM wWordFiltered WHERE wScreenResult = 'Pass'";
		
		if( (1 <= $zScrambleCatId) && ($zScrambleCatId <= 10) ) {
			$getFilteredPhrases .= " AND wCatId = $zScrambleCatId";
		}
		
		$filteredPhrases = $db->prepare($getFilteredPhrases);
		$filteredPhrases->execute();
		
		$fPhrasesArray = $filteredPhrases->fetchAll();
		
		// find out how many records were retrieved
		$numRows = $filteredPhrases->rowCount();
		
		// count for all
		$rCount = 0;
		
		if( (1 <= $zScrambleCatId) && ($zScrambleCatId <= 10) ) {
			$eQuery = "DELETE FROM wWordScrambled WHERE wCatId = $zScrambleCatId";
		} else {
			$eQuery = "TRUNCATE TABLE wWordScrambled";
		}
		$e = $db->prepare($eQuery);
		$e->execute();
		
		foreach ($fPhrasesArray as $row) {
			
			$wId 			= $row['wId'];
			$wCatId			= $row['wCatId'];
			$wText			= $row['wText'];
			$wKeyIndex		= $row['wKeyIndex'];
			$wAddedBy		= $row['wAddedBy'];
			
			// Increase count
			++$wCount;
			
			// zOutput->scramble( $phrase, $lowercase = true, $wKeyIndex = 0, $zDiffLowerLimit, $zDiffUpperLimit )
			$scrambleResults = $zOutput->scramble($wText, false, $wKeyIndex, $zDiffLowerLimit, $zDiffUpperLimit);
			
			$wText 			= $scrambleResults['wOriginal'];
			$wScrambled 	= $scrambleResults['wScrambled'];
			$wAnswers 		= serialize($scrambleResults['wAnswers']);
			$wKeyword		= $scrambleResults['wKeyword'];
			$wDifficulty 	= $scrambleResults['wDifficulty'];
			$wDiffFirst 	= $scrambleResults['wDiffFirst'];
			$wDiffSecond 	= $scrambleResults['wDiffSecond'];
			
			$saveQuery = "INSERT INTO wWordScrambled (wCatId, wText, wScrambled, wKeyIndex, wKeyword, wAnswers, wDiffFirst, wDiffSecond, wDifficulty, wAddedBy) VALUES (:wCatId, :wText, :wScrambled, :wKeyIndex, :wKeyword, :wAnswers, :wDiffFirst, :wDiffSecond, :wDifficulty, :wAddedBy)";
			$s = $db->prepare($saveQuery);
			$s->execute(
				array(
					":wCatId" => $wCatId,
					":wText" => $wText,
					":wScrambled" => $wScrambled,
					":wKeyIndex" => $wKeyIndex,
					":wKeyword" => $wKeyword,
					":wAnswers" => $wAnswers,
					":wDiffFirst" => $wDiffFirst,
					":wDiffSecond" => $wDiffSecond,
					":wDifficulty" => $wDifficulty,
					":wAddedBy" => $wAddedBy
				)
			);
			
		}
		
	}
	
?>


	<?php if( isset( $_POST["submit"] ) ) { ?>
	<!-- Scramble Options
	================================================== -->
	<div id="section-resultSummary"">
		<div class="container">

			<div class="sixteen columns">
				<h3>Scramble Results</h3>	
			</div><!-- // .sixteen -->
			
			<br class="clear" />
			
			<div class="four columns">
				<h4>Category</h4>
				<?php echo $zScrambleCatId; ?>
			</div><!-- // .four -->
			
			<div class="four columns">
				<h4>Difficulty Lower Bound</h4>
				<?php echo $zDiffLowerLimit; ?>
			</div><!-- // .four -->
			
			<div class="four columns">
				<h4>Difficulty Upper Bound</h4>
				<?php echo $zDiffUpperLimit; ?>
			</div><!-- // .four -->
			
			<div class="four columns">
				<h4>Scramble Results</h4>
				Successful
			</div>
			
		</div><!-- // .container -->
	</div><!-- // .section-options -->
	<?php } ?>
	
	
	<div id="section-options">
		<div class="container">
			<div class="sixteen columns">
				<h3>Scramble Options</h3>	
			</div><!-- // .sixteen -->
				
			<form id="generateParams" action="scramble.php" method="post">
				<div class="four columns">
					<label for="zScrambleCatId">Category</label>
					<input type="text" name="zScrambleCatId" value="" />
				</div><!-- // .four -->
				
				<div class="four columns">
					<label for="zDiffLowerLimit">Difficulty Lower Limit</label>
					<input type="text" name="zDiffLowerLimit" placeholder="100" />
				</div><!-- // .four -->
									
				<div class="four columns">
					<label for="zDiffUpperLimit">Difficulty Upper Limit</label>
					<input type="text" name="zDiffUpperLimit" placeholder="1000" />
				</div><!-- // .four -->
				
				<div class="four columns">
					<label>Generate scrambled sets</label>
					<input type="submit" class="submit" name="submit" disabled="disabled" value="Scramble" />
				</div>
			</form>
			
		</div><!-- // .container -->
	</div><!-- // .section-options -->
	


	<!-- Scramble
	================================================== -->
	<div id="section-zCode">
		<div class="container">
			
			<?php
				// Query by categories
				$catQuery = $db->prepare("SELECT wCatId, wCatName FROM wCategory");
				$catQuery->execute();
				$catArray = $catQuery->fetchAll();
				
				foreach( $catArray as $key=>$category ) {
					$wCatId			= $category['wCatId'];
					$wCatName		= $category['wCatName'];
							
					echo "<div class='four columns'>";
					echo "<div class='box-zCategory'><a href='scramble.php?catId=$wCatId' title='View $wCatName'><span class='sublabel'>Category";
					echo "<span class='font-green'>" . $wCatId . "</span></span>";
					echo "<br /><span class='label'>";
					echo $wCatName;
					echo "</span></a></div>";
					echo "</div><!-- // .four -->";
				}
			?>
			
			<br class="clear" />
			
			<div class="sixteen columns">
				<table id="questions">
					<thead>
						<tr>
							<td>Category</td>
							<td>Word</td>
							<td>Scramble</td>
							<td>Keyword</td>
							<td colspan="2">z-Tree Answers</td>
							<td>Difficulty</td>
						</tr>
						
						<tr class="thead-desc">
							<td>wCatId</td>
							<td>wText</td>
							<td>wScrambled</td>
							<td>wKeyword</td>
							<td>wAnswer[0]</td>
							<td>wAnswer[1]</td>
							<td>wDifficulty</td>
						</tr>
					</thead>
					
					<tbody>
						<?php
							// submit the query and capture the result
							
							$catQuery = "SELECT wWordScrambled.wId, wWordScrambled.wCatId, wWordScrambled.wText, wWordScrambled.wScrambled, wWordScrambled.wKeyIndex, wWordScrambled.wAnswers, wWordScrambled.wDiffFirst, wWordScrambled.wDiffSecond, wWordScrambled.wDifficulty, wCategory.wCatName FROM wWordScrambled JOIN wCategory ";

							// Display only one category?
							if ( isset($catId) ) { 
								$catQuery .= "WHERE wWordScrambled.wCatId = wCategory.wCatId AND wWordScrambled.wCatId = $catId ORDER BY wId";
							} else {
								// If no specific category, display all
								$catQuery .= "WHERE wWordScrambled.wCatId = wCategory.wCatId ORDER BY wCatId";
							}
							
							$results = $db->prepare($catQuery);
							$results->execute();
							
							$wArray = $results->fetchAll();
							
							// find out how many records were retrieved
							$numRows = $results->rowCount();
							
							$wCount = 0;
							
							$totalDifficulty = 0;
							
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
							
							$totalDifficulty += $wDifficulty;
							
							// Increase count
							++$wCount;
							
						?>
						<tr>
							<td><?php echo $wCount; ?></td>
							<td><?php echo $wText; ?></td>
							<td><?php echo $wScrambled; ?></td>
							<td><?php echo $wKeyIndex; ?></td>
							<?php
								
								// If the phrase has only a single word
								if ( count($wAnswers) == 1 ) {
									echo "<td class='singleAnswer' colspan='2'>";
									
									foreach( $wAnswers[0] as $key=>$value ) {
										echo $value;
										echo "<br />";
										
										$phraseDigitCount1 = strlen($value);
									};
									echo "<span class='wDigitCount'>" . $phraseDigitCount1 . " digits</span>";
									echo "</td>";
								}
								
								// If the phrase has more than one word
								if ( count($wAnswers) > 1 ) {
									echo "<td>";
									
									foreach( $wAnswers[0] as $key=>$value ) {
										echo $value;
										echo "<br />";
										
										$phraseDigitCount1 = strlen($value);
									};
									
									echo "<span class='wDigitCount'>" . $phraseDigitCount1 . " digits</span>";
									
									echo "</td>";
									
									echo "<td>";
									
									foreach( $wAnswers[1] as $key=>$value ) {
										echo $value;
										echo "<br />";
										
										$phraseDigitCount2 = strlen($value);
									};
									
									echo "<span class='wDigitCount'>" . $phraseDigitCount2 . " digits</span>";
									
									echo "</td>";
								}
								
							?>
							<td>
								<span class="wDifficulty"><?php echo $wDifficulty; ?></span><br />
								<span class="wCalculation">Word #1's difficulty is <span class="font-green"><?php echo $wDiffFirst; ?></span><br />
								
								<?php 
									if( isset($wDiffSecond) ) {
										echo "Word #2's difficulty is <span class='font-green'>$wDiffSecond</span>";
									}
									
								?>
									
									
								
								</span>
								
							</td>
						</tr>
						
						<?php 
							}
							
							$averageDifficulty = $totalDifficulty / $numRows;
						?>
						
					</tbody>
				</table>
				
				<br class="clear" />
				
			</div><!-- // .sixteen -->
			
		</div><!-- // .container -->
	</div><!-- // #section-functions -->
	
	<div id="section-stats">
		<div class="container">
			<div class="sixteen columns">
				<h3>Summary</h3>
					
				<div class="eight columns alpha">
					<p class="box-stats">There are a total of <strong><?php echo $numRows; ?></strong> words.</p>
				</div><!-- // .eight -->
				
				<div class="eight columns omega">
					<p class="box-stats">The average difficulty of category <?php echo $zCatQuery; ?> is <strong><?php echo round($averageDifficulty, 2); ?></strong>.</p>
				</div><!-- // .eight -->
				
				<br class="clear" />

			</div><!-- // .sixteen -->
		</div><!-- // .container -->
	</div><!-- // #section-stats -->
	
	
<?php include_once('footer.php'); ?>