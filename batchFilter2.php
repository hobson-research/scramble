<?php include_once('header.php'); ?>

<?php
		
		$zScrambleCatId = $_POST['zScrambleCatId'];
		$zDiffLowerLimit = $_POST['zDiffLowerLimit'];
		$zDiffUpperLimit = $_POST['zDiffUpperLimit'];
		
		if( !isset($zDiffLowerLimit) ) {
			$zDiffLowerLimit = 0;
		}
		
		if( !isset($zDiffUpperLimit) ) {
			$zDiffUpperLimit = 9999;
		}
		
		
			$wId 			= $row['wId'];
			$wCatId			= $row['wCatId'];
			$wText			= $row['wText'];
			$wKeyIndex		= $row['wKeyIndex'];
			$wAddedBy		= $row['wAddedBy'];
			
			// Increase count
			++$wCount;
			
			// zOutput->scramble( $phrase, $lowercase = true, $wKeyIndex = 0, $zDiffLowerLimit, $zDiffUpperLimit )
			$scrambleResults = $zOutput->scramble("Galatians", false, $wKeyIndex, $zDiffLowerLimit, $zDiffUpperLimit);
			
			$wText 			= $scrambleResults['wOriginal'];
			$wScrambled 	= $scrambleResults['wScrambled'];
			$wAnswers 		= serialize($scrambleResults['wAnswers']);
			$wKeyword		= $scrambleResults['wKeyword'];
			$wDifficulty 	= $scrambleResults['wDifficulty'];
			$wDiffFirst 	= $scrambleResults['wDiffFirst'];
			$wDiffSecond 	= $scrambleResults['wDiffSecond'];
			
			/*
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
					":wAddedBy" => "Park"
				)
			);
			 * 
			 */
?>

<?php include_once('footer.php'); ?>
