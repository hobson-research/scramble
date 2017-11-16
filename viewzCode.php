<?php include_once('header.php'); ?>
	
<?php 
	// zCategory
	$catId = $_GET["catId"];
	if( !isset($catId) ) {
		$catId = 1;
	}
	
	if( isset( $_POST["submit"] ) ) {
		for($i=1; $i<=10; $i++) {
			$zSetQuery = "SELECT wId, wCatId FROM wWordScrambled";
			$zSetQuery .= " WHERE wCatId = $i ORDER BY wId";
			
			$zSet = $db->prepare($zSetQuery);
			$zSet->execute();
			$zArray = $zSet->fetchAll();
			
			shuffle($zArray);
			
			$zPeriodCount = 1;
			$zQuestionCount = 1;
			
			foreach ($zArray as $row) {
				
				$wId 			= $row['wId'];
				
				$saveQuery = "UPDATE wWordScrambled SET zPeriod=?, zQuestionNumber=? WHERE wId=?";
				$s = $db->prepare($saveQuery);
				$s->execute(
					array(
						$zPeriodCount,
						$zQuestionCount,
						$wId
					)
				);
				
				if($zQuestionCount < 20) {
					$zQuestionCount++;
				} else {
					$zQuestionCount = 1;
					$zPeriodCount++;
				}
			}
		}
	}
	
?>
	<div id="section-options">
		<div class="container">
			<div class="twelve columns">
				<p class="desc">Click the button on the right to randomly assign z-Tree period/question numbers.</p>
			</div><!-- // .twelve -->
			
			<div class="four columns">
				<form id="assignZNumbers" action="viewzCode.php" method="post">
					<input type="submit" class="submit" name="submit" disabled="disabled" value="Assign" />
				</form>
			</div><!-- // .four -->
			
			<br class="clear" />
		</div><!-- // .container -->
	</div><!-- #sectoin-options -->


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
					echo "<div class='box-zCategory'><a href='viewzCode.php?catId=$wCatId' title='View $wCatName'><span class='sublabel'>Category";
					echo "<span class='font-green'>" . $wCatId . "</span></span>";
					echo "<br /><span class='label'>";
					echo $wCatName;
					echo "</span></a></div>";
					echo "</div><!-- // .four -->";
				}
			?>
			
			<br class="clear" />
			
			<div class="sixteen columns">
				<?php
					// submit the query and capture the result
					
					$catQuery = "SELECT wWordScrambled.wId, wWordScrambled.wCatId, wWordScrambled.wText, wWordScrambled.wScrambled, wWordScrambled.wKeyIndex, wWordScrambled.wAnswers, wWordScrambled.wDiffFirst, wWordScrambled.wDiffSecond, wWordScrambled.wDifficulty, wWordScrambled.zPeriod, wWordScrambled.zQuestionNumber, wCategory.wCatName FROM wWordScrambled JOIN wCategory ";

					// Display only one category?
					if ( isset($catId) ) { 
						$catQuery .= "WHERE wWordScrambled.wCatId = wCategory.wCatId AND wWordScrambled.wCatId = $catId ORDER BY zPeriod, zQuestionNumber";
					} else {
						// If no specific category, display all
						$catQuery .= "WHERE wWordScrambled.wCatId = wCategory.wCatId ORDER BY wCatId";
					}
					
					$results = $db->prepare($catQuery);
					$results->execute();
					
					$wArray = $results->fetchAll();
					
					// find out how many records were retrieved
					$numRows = $results->rowCount();
					
					foreach ($wArray as $row) {
						
						$wId 				= $row['wId'];
						$wCatId				= $row['wCatId'];
						$wCatName			= $row['wCatName'];
						$wText				= $row['wText'];
						$wScrambled			= $row['wScrambled'];
						$wKeyIndex			= $row['wKeyIndex'];
						$wAnswers			= unserialize($row['wAnswers']);
						$wDifficulty		= $row['wDifficulty'];
						$zPeriod			= $row['zPeriod'];
						$zQuestionNumber	= $row['zQuestionNumber'];
				?>
				
				<div class="question">
					<div class="four columns alpha">
						<div class="box-ztreeInfo">
							<?php if( isset($zPeriod) ) { ?>
								<div class="info-ztreePeriod">
									<span class="label">Stage</span>
									<span class="number"><?php echo $zPeriod ?></span>
									<span class="slash">/</span>
								</div><!-- // .info-ztreePeriod -->
								
								<div class="info-ztreeNumber">
									<span class="label">Question</span>
									<span class="number"><?php echo $zQuestionNumber ?></span>
								</div><!-- // .info-ztreeNumber -->
							<?php } else { ?>
								<div class="info-ztreePeriod">
									Not in the sets
								</div><!-- // .box-ztreePeriod -->
							<?php } ?>
						</div><!-- // .ztreeInfo -->
					</div><!-- // .four -->
					
					<div class="four columns">
						<div class="info-qYear">
							<span class="label">Category</span>
							<?php echo $wCatName; ?><br />
						</div><!-- // .info-qYear -->
					</div><!-- // .four -->
					
					<div class="four columns">
						<div class="info-qSection">
							<span class="label">Original</span>
							<?php echo $wText; ?><br />
						</div><!-- // .info-qSection -->
					</div><!-- // .four -->
					
					<div class="four columns omega">
						<div class="info-qNumber">
							<span class="label">Scrambled</span>
							<?php echo $wScrambled; ?><br />
						</div><!-- // .info-qNumber -->
					</div><!-- // .four -->
					
					<br class="clear" />
					
					<div class="eight columns alpha">
						<div class="wrap-textarea-left">
							<label>Question Code</label><br />
							<textarea class="selectAll" rows="10" cols="40"><?php echo $zOutput->zQuestionCode($wScrambled, $wCatName, $zQuestionNumber); ?></textarea>
						</div><!-- .wrap-textarea-left -->
					</div><!-- // .six -->
					
					<div class="eight columns omega">
						<div class="wrap-textarea-right">
							<label>Button Code</label><br />
							<textarea class="selectAll" rows="10" cols="40"><?php echo $zOutput->zButtonCode($wText, $wAnswers, $zQuestionNumber); ?></textarea>
						</div><!-- .wrap-textarea-right -->
					</div><!-- // .six -->
					
					<br class="clear" />
				</div><!-- // div.questions-->
				
				
				<?php } ?>
				
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
					<p class="box-stats">Stat</p>
				</div><!-- // .eight -->
				
				<br class="clear" />

			</div><!-- // .sixteen -->
		</div><!-- // .container -->
	</div><!-- // #section-stats -->
	
	
<?php include_once('footer.php'); ?>