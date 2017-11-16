<?php include_once('header.php'); ?>
	
	
	<!-- Functions
	================================================== -->
	<div id="section-functions">
		<div class="container">
			<div class="eight columns">
				<p class="desc">List of words 
				</p><!-- // p.desc -->
			</div><!-- // .eight -->
			
			<div class="seven offset-by-one columns">
			
			</div><!-- // .seven -->
			
			<br class="clear" />
			
			<div class="sixteen columns">
				<table id="questions">
					<thead>
						<tr>
							<td>Category</td>
							<td>wText</td>
						</tr>
					</thead>
					
					<tbody>
						<?php
							// submit the query and capture the result
							$results = $db->prepare("SELECT wWordFiltered.wId, wWordFiltered.wCatId, wWordFiltered.wText, wCategory.wCatName FROM wWordFiltered JOIN wCategory WHERE (wWordFiltered.wCatId = wCategory.wCatId) AND (wWordFiltered.wScreenResult = 'Pass') ORDER BY wCatId");
							$results->execute();
							$wArray = $results->fetchAll();
							
							// find out how many records were retrieved
							$numRows = $results->rowCount();
							
							foreach ($wArray as $row) {
								
								$wId 			= $row['wId'];
								$wCatId			= $row['wCatId'];
								$wCatName		= $row['wCatName'];
								$wText			= $row['wText'];
								
								$filterResults = $zOutput->filterPhrase($wText, 6);
								
								$filteredWord	= $filterResults['filteredWord']															
						?>
						<tr>
							<td><?php echo $wCatName; ?></td>
							<td><?php echo $filteredWord; ?></td>
						</tr>
						
						<?php } ?>
						
					</tbody>
				</table>
				
				<br class="clear" />
				
			</div><!-- // .sixteen -->
			
		</div><!-- // .container -->
	</div><!-- // #section-functions -->
	
	<div id="section-stats">
		<div class="container">
			<div class="sixteen columns">
				<h3>Words Summary/Statistics</h3>
					
				<div class="eight columns alpha">
					<p class="box-stats">There are a total of <strong><?php echo $numRows; ?></strong> words.</p>
				</div><!-- // .eight -->
				
				<br class="clear" />

			</div><!-- // .sixteen -->
		</div><!-- // .container -->
	</div><!-- // #section-stats -->
	
	
<?php include_once('footer.php'); ?>
