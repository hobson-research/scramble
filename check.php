<?php include_once('header.php'); ?>
	
<?php 
	// zCategory
	$catId = $_GET["catId"];
	if( !isset($catId) ) {
		$catId = 1;
	}
	
	// If the reviewer has clicked on the "update" button to update conflicting words
	if( isset( $_POST["submit"] ) ) {
		
		$aWord = $_POST["aWord"];
		
		$conflictCount = 0;
		
		foreach ( $aWord as $wId=>$wConflict ) {
			
			$saveQuery = "UPDATE wWordScrambled SET wConflict=? WHERE wId=?";
			$s = $db->prepare($saveQuery);
			$s->execute(
				array(
					$wConflict,
					$wId
				)
			);
			
			++$conflictCount;
		}
?>
	<div class="section-notify">
		<div class="container">
			<div class="sixteen columns">
				<p class="desc">Successfully updated <?php echo $conflictCount; ?> records.</p>
			</div><!-- // .sixteen -->
		</div><!-- // .container -->
	</div><!-- // .section-notification -->
<?php	
	}
?>


	<!-- Scramble
	================================================== -->
	<div id="section-zCode">
		<div class="container">
			
			<div class="sixteen columns">
				<h2 class="font-color-primary">Anagram Solver Check</h2>
				<p class="desc margin-bottom-30">
					You are looking at category <span class="font-green"><?php echo $catId; ?></span>.<br />
					You can view other categories by clicking on one of the links below.
				</p>
				<br class="clear" />
			</div>
			
			<br class="clear" />
			
			<?php
				// Query by categories
				$catQuery = $db->prepare("SELECT wCatId, wCatName FROM wCategory");
				$catQuery->execute();
				$catArray = $catQuery->fetchAll();
				
				foreach( $catArray as $key=>$category ) {
					$wCatId			= $category['wCatId'];
					$wCatName		= $category['wCatName'];
							
					echo "<div class='four columns'>";
					echo "<div class='box-zCategory'><a href='check.php?catId=$wCatId' title='View $wCatName'><span class='sublabel'>Category";
					echo "<span class='font-green'>" . $wCatId . "</span></span>";
					echo "<br /><span class='label'>";
					echo $wCatName;
					echo "</span></a></div>";
					echo "</div><!-- // .four -->";
				}
			?>
			
			<br class="clear" />
			
			<div class="sixteen columns">
				
				<form id="anagramCheckForm" action="check.php?catId=<?php echo $catId; ?>" method="post">
				<table id="questions">
					<thead>
						<tr>
							<td>Original</td>
							<td colspan="2">Answers Check</td>
							<td>Problematic words</td>
						</tr>
						
						<tr class="thead-desc">
							<td>wText</td>
							<td>Anagram Answers One</td>
							<td>Anagram Answers Two</td>
							<td>Check</td>
						</tr>
					</thead>
					
					<tbody>
						<?php
							// submit the query and capture the result
							
							$catQuery = "SELECT wWordScrambled.wId, wWordScrambled.wCatId, wWordScrambled.wText, wWordScrambled.wScrambled, wWordScrambled.wKeyIndex, wWordScrambled.wAnswers, wWordScrambled.wDiffFirst, wWordScrambled.wDiffSecond, wWordScrambled.wDifficulty, wWordScrambled.wConflict, wCategory.wCatName FROM wWordScrambled JOIN wCategory ";
		
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
							
							foreach ($wArray as $row) {
								
								$wId 			= $row['wId'];
								$wCatId			= $row['wCatId'];
								$wCatName		= $row['wCatName'];
								$wText			= $row['wText'];
								$wScrambled		= $row['wScrambled'];
								$wDifficulty	= $row['wDifficulty'];
								$wConflict		= $row['wConflict'];
							
							
						?>
						
							<tr>
								<td><?php echo $wText; ?></td>
								<?php
									$wCheck = trim($wText);
									$cArray = preg_split("/\s+/", $wCheck);
									
									$numWords = count($cArray);
									
									foreach( $cArray as $key=>$singleword ) {
										$wordCheck = strtolower($singleword);
										
										//Alphabeticly order the search term
										$alfa = array();
										for ($count = 0; $count <= strlen($wordCheck) -1; $count++){
											$char = $wordCheck[$count];
											array_push($alfa, $char);
										}
										
										sort($alfa);
										
										//Putting the word back toughether
										$wordCheck = implode("", $alfa);
										
										$cQuery = $db->prepare("SELECT DISTINCT Word, alfa FROM english WHERE alfa=:wordCheck");
										$cQuery->bindParam(':wordCheck', $wordCheck);
										$cQuery->execute();
										
										$results = $cQuery->fetchAll();
										
										if( $numWords == 1) {
											echo "<td colspan='2'>";
											foreach ($results as $row) {
												$Word 			= $row['Word'];
												$alfa			= $row['alfa'];
												
												if ( trim(strtolower($Word)) == trim(strtolower($singleword)) ) {
													echo "<span class='font-green'>$Word</span><br />";
												} else {
													echo $Word . "<br />";
												}
											}
											echo "</td>";
										} else {
											echo "<td>";
											foreach ($results as $row) {
												$Word 			= $row['Word'];
												$alfa			= $row['alfa'];
												
												if ( trim(strtolower($Word)) == trim(strtolower($singleword)) ) {
													echo "<span class='font-green'>$Word</span><br />";
												} else {
													echo $Word . "<br />";
												}
											}
											echo "</td>";
										}
									}
								?>
								<td>
									<input type="text" name="aWord[<?php echo $wId; ?>]" value="<?php echo $wConflict; ?>" placeholder="Problematic Word" />
								</td>
							</tr>
						
						<?php
							}
						?>
						</tbody>
					</table>
					<input type="submit" name="submit" class="submit" value="Update" />
					</form><!-- // form -->
				
				<br class="clear" />
				
			</div><!-- // .sixteen -->
			
		</div><!-- // .container -->
	</div><!-- // #section-functions -->
	
<?php include_once('footer.php'); ?>