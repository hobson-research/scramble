<?php include_once('header.php'); ?>
	
<?php 
	// If 
	if( isset( $_POST["submit"] ) ) {
		$filterStart = true;
	} else {
		$filterStart = false;
	}
?>

	<div id="section-stats">
		<div class="container">
			<div class="sixteen columns">
				<h3>Filter Statistics</h3>
					
				<div class="eight columns alpha">
					<p class="box-stats">There are a total of <strong><span id="numRows"></span></strong> words.</p>
				</div><!-- // .eight -->
				
				<div class="four columns">
					<p class="box-stats">Filter Criteria Pass <strong><span id="totalMatch"></span></strong></p>
				</div><!-- // .four -->
				
				<div class="four columns omega">
					<p class="box-stats">Filter Criteria Fail <strong><span id="totalError"></span></strong></p>
				</div><!-- // .four -->
				
				<br class="clear" />

			</div><!-- // .sixteen -->
		</div><!-- // .container -->
	</div><!-- // #section-stats -->
	
	<!-- Functions
	================================================== -->
	<div id="section-functions">
		<div class="container">
			<div class="twelve columns">
				<h3 style="font: normal normal 11px/18px Helvetica, Arial, sans-serif; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px;">Filtering</h3>
				<p class="desc">
					<ul class="checkList">
						<li>Remove duplicates within the same category</li>
						<li>Check if the phrase is an acronym</li>
						<li>Check if the phrase has more than two words</li>
						<li>Check if total number of characters is less than 4</li>
						<li>Check if any of the words in the phrase has more than 12 characters</li>
						<li>Check if non-alphabet characters exist</li>
					</ul>
				</p><!-- // p.desc -->
			</div><!-- // .twelve -->
			
			<div class="four columns">
				<form id="generateParams" action="batchFilter.php" method="post">
					<input type="submit" class="submit" name="submit" value="Update" />
				</form>
			</div><!-- // .four -->
			
			<br class="clear" />
			
			<div class="sixteen columns">
				<table id="questions" class="check">
					<thead>
						<tr>
							<td>Category</td>
							<td>wText</td>
							<td>Screen Results</td>
							<td>hasKeyword</td>
						</tr>
					</thead>
					
					<tbody>
						<?php
							// submit the query and capture the result
							$results = $db->prepare("SELECT wWord.wId, wWord.wCatId, wWord.wText, wWord.wKeyIndex, wWord.wAddedBy, wCategory.wCatName FROM wWord JOIN wCategory WHERE wWord.wCatId = wCategory.wCatId ORDER BY wCatId");
							$results->execute();
							$wArray = $results->fetchAll();
							
							// find out how many records were retrieved
							$numRows = $results->rowCount();
							
							// If updateing the database, empty the filtered table
							if ($filterStart = true) {
								$tSql = "TRUNCATE TABLE wWordFiltered";
								$tQuery = $db->prepare($tSql);
								$tQuery->execute();
							}
							
							foreach ($wArray as $row) {
								
								$wId 			= $row['wId'];
								$wCatId			= $row['wCatId'];
								$wCatName		= $row['wCatName'];
								$wText			= $row['wText'];
								$wKeyIndex		= $row['wKeyIndex'];
								$wAddedBy		= $row['wAddedBy'];
								
								// filterPhrase($wOriginal, $minCharsLimit = 3, $maxLength = 12, $allowAcronyms = false, $wKeyIndex = 0 )
								$filterResults = $zOutput->filterPhrase($wText, 3, 12, false, $wKeyIndex);
								
								$filteredWord	= $filterResults['filteredWord'];
								$wKeyIndex		= $filterResults['hasKeyword'];
								$screenResult	= $filterResults['screenResult'];
								
								$screenResultInsert	= str_replace('<br />', ' ', $screenResult);
								
								// Update if asked to filter
								if ($filterStart = true) {
									// query
									$sql = "INSERT INTO wWordFiltered (wId,wCatId,wText,wKeyIndex,wScreenResult,wAddedBy) VALUES (:wId,:wCatId,:wText,:wKeyIndex,:wScreenResult,:wAddedBy)";
									$q = $db->prepare($sql);
									$q->execute(
										array(
											':wId'				=> $wId,
											':wCatId'			=> $wCatId,
											':wText'			=> $filteredWord,
											':wKeyIndex'		=> $wKeyIndex,
											':wScreenResult'	=> $screenResultInsert,
											':wAddedBy'			=> $wAddedBy
										)
									);
								}
						?>
						<tr>
							<td><?php echo $wCatName; ?></td>
							<td><?php echo $filteredWord; ?></td>
							<td class="screenResult"><?php echo $screenResult; ?></td>
							<td class="hasKeyword"><?php echo $wKeyIndex; ?></td>

						</tr>
						
						<?php 
							}
							
							// Update if asked to filter
							if ($filterStart = true) {
								// Remove duplicates within the same category
								$rdsql = "DELETE FROM wWordFiltered USING wWordFiltered, wWordFiltered AS tempTable WHERE (wWordFiltered.wId > tempTable.wId) AND (wWordFiltered.wCatId = tempTable.wCatId) AND (wWordFiltered.wText = tempTable.wText)";
								$rdq = $db->prepare($rdsql);
								$rdq->execute();
							}
							
						?>
						
					</tbody>
				</table>
				
				<br class="clear" />
				
			</div><!-- // .sixteen -->
			
		</div><!-- // .container -->
	</div><!-- // #section-functions -->
	
	
<?php include_once('footer.php'); ?>
