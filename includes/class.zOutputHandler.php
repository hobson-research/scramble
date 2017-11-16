<?php

class zOutputHandler extends zTree {
	
	public $pointCorrect 		= 4;
	public $pointNotAnswered	= 0;
	public $pointWrong 			= -1;
	
	public function __construct() {
	
	}
	
	
	// Array cartesian product to get ansewr combinations
	private function array_cartesian_product($arrays)
	{
	    $result = array();
	    $arrays = array_values($arrays);
	    $sizeIn = sizeof($arrays);
	    $size = $sizeIn > 0 ? 1 : 0;
	    foreach ($arrays as $array)
	        $size = $size * sizeof($array);
	    for ($i = 0; $i < $size; $i ++)
	    {
	        $result[$i] = array();
	        for ($j = 0; $j < $sizeIn; $j ++)
	            array_push($result[$i], current($arrays[$j]));
	        for ($j = ($sizeIn -1); $j >= 0; $j --)
	        {
	            if (next($arrays[$j]))
	                break;
	            elseif (isset ($arrays[$j]))
	                reset($arrays[$j]);
	        }
	    }
	    return $result;
	}
	
	
	public function filterPhrase($wOriginal, $minCharsLimit = 3, $maxLength = 12, $allowAcronyms = false, $wKeyIndex = 0 ) {
		
		$word = trim($wOriginal);
		
		// Screening criteria 
		$testAcronym 		= false;
		$passMinCharsLimit	= true;
		$passWordLimit 		= true;
		$passMaxCharsLimit 	= true;
		$testSpecialChars 	= false;
		$hasKeyword 		= 0;
		$screenResult 		= '';
		
		if( strlen($word) <= $minCharsLimit) {
			$passMinCharsLimit = false;
		}
		
		// If all characters are uppercase letters, assign true to $testAcronym
		if( $word == strtoupper($word) ) {
			$testAcronym = true;
		} else {
			// If not an acronym, uppercase first character of each word
			// $word = ucwords($word);
		}
		
		// Split the phrase into words
		$wordArray = preg_split("/\s+/", $word);
		
		// Count the word
		$wordCount = count($wordArray);
		
		// If more than two words
		if( $wordCount > 2) {
			$passWordLimit = false;
		}
		
		foreach( $wordArray as $key=>$word ) {
			
			if (!preg_match('/^[A-Z]+$/i', $word)) {
				$testSpecialChars = true;
			}
			
			$wordLength[$key] = strlen($word);
			
			if ( strlen($word) > $maxLength ) {
				$passMaxCharsLimit = false;
			}	
		}
		
		if( $wordCount == 2 ) {
			if( $wKeyIndex == 'None' ) {
				$hasKeyword = 0;
			} else {
				if( $wordLength[0] < $wordLength[1] ) {
					$hasKeyword = 1;
					if( $wordArray[0] == 'The' ) {
						$hasKeyword = 0;
					}
				} elseif ( $wordLength[0] > $wordLength[1] ) {
					$hasKeyword = 2;
				} else {
					$hasKeyword = 0;
				}
			}

		}
		
		if ( ($testAcronym == false) && ($passMinCharsLimit == true) && ($passWordLimit == true) && ($passMaxCharsLimit == true) && ($testSpecialChars == false) ) {
			$screenResult = "Pass";
		}
		
		if( $testAcronym == true ) { $screenResult = "Acronym<br />"; }
		if( $passMinCharsLimit == false ) { $screenResult .= "Less than or equal to $minCharsLimit characters<br />"; }
		if( $passWordLimit == false ) { $screenResult .= "More than two words<br />"; }
		if( $passMaxCharsLimit == false ) { $screenResult .= "Over 12 characters in one (or more) word<br />"; }
		if( $testSpecialChars == true ) { $screenResult .= "Non-alphabetic characters exist<br />"; }
		
		// Return array 
		$resultArray = array();
		
		$resultArray['filteredWord'] 		= implode(' ', $wordArray);
		$resultArray['hasKeyword']			= $hasKeyword;
		$resultArray['screenResult']		= $screenResult;
		
		return $resultArray;
		
	}
	
	
	// Scramble and return original, shuffled, answers, and similarity
	private function scrambleWord($wOriginal, $lowercase = false) {
		
		if ($lowercase == true) {
			$phrase = strtolower($wOriginal);
		} else {
			$phrase = $wOriginal;
		}
		
		// Shuffle what's leftover
		$wShuffle = str_shuffle($phrase);
		
		// Into an array
		// original phrase
		$phraseChars = str_split($phrase);
		
		// shuffled phrase
		$wShuffleChars = str_split($wShuffle);
		
		// Set up an array to hold matching index arrays
		$indexMatchArray = array();
		$answerArray = array();
		
		foreach($phraseChars as $index=>$letter) {
			$indexMatch = array_keys($wShuffleChars, $letter);
			
			foreach($indexMatch as $key => &$value) {
				++$value;
			}
			
			$indexMatchArray[$index] = $indexMatch;
		}
		
		// var_dump($indexMatchArray);
		
		$indexMatchArray = self::array_cartesian_product($indexMatchArray);
		
		// getting the answers
		foreach($indexMatchArray as $i=>$value) {
			
			if ( count($value) != count( array_unique($value) ) ) {
				continue;
			}
			
			$answerArray[$i] = implode($value);
		}
		
		similar_text( strtolower($wOriginal), $wShuffle, $wSimilarity );
		
		$textLength = strlen($wOriginal);
		$wDifficulty = (100 - $wSimilarity) * $textLength;
		
		$scrambleWordResults = array(
			"wScrambled" => $wShuffle,
			"wDifficulty" => round($wDifficulty, 2),
			"wAnswers" => $answerArray,
		);
		
		return $scrambleWordResults;
	}
	
	
	// Find and return conflicting answers
	public function findConflictAnswers($wOriginal, $wConflict) {
		foreach($phraseChars as $index=>$letter) {
			$indexMatch = array_keys($wShuffleChars, $letter);
			
			foreach($indexMatch as $key => &$value) {
				++$value;
			}
			
			$indexMatchArray[$index] = $indexMatch;
		}
		
		// var_dump($indexMatchArray);
		
		$indexMatchArray = self::array_cartesian_product($indexMatchArray);
		
		// getting the answers
		foreach($indexMatchArray as $i=>$value) {
			
			if ( count($value) != count( array_unique($value) ) ) {
				continue;
			}
			
			$answerArray[$i] = implode($value);
		}
	}
	
	public function scramble( $phrase, $lowercase = false, $wKeyIndex = 0, $zDiffLowerLimit, $zDiffUpperLimit ) {
		
		$scrambleResults = array();
		$outputResults = array();
		
		// Split by space
		$wordArray = preg_split("/\s+/", $phrase);
		$wordCount = count($wordArray);
		
		if($wKeyIndex !== 0) {
			$wKeyIndexCalc = $wKeyIndex - 1;
			// $outputResults['wKeyword'] = $wordArray[$wKeyIndexCalc];
		}
		
		if( $wKeyIndex == 1 ) {
			$posKey = 0;
			$posNonkey = 1;
		} elseif( $wKeyIndex == 2 ) {
			$posKey = 1;
			$posNonkey = 0;
		} else {
			
		}
		
		// Repeat n times until we get the difficulty in the range
		for ($i = 1; $i <= 20; $i++) {
			foreach( $wordArray as $key=>$word ) {
				$scrambleResults[$key] = self::scrambleWord($word, $lowercase);
			}
			
			if ( $wKeyIndex == 0 ) {
				// If there is no Keyword, take the higher difficulty
				$wDifficulty = max( $scrambleResults[0]['wDifficulty'], $scrambleResults[1]['wDifficulty'] );
			} else {
				// Otherwise
				$wDifficulty = $scrambleResults[$posKey]['wDifficulty'] + ( $scrambleResults[$posNonkey]['wDifficulty'] * 0.2 );
				
				$wKeyIndexUse = $wKeyIndex - 1;
				$outputResults['wKeyword'] = $wordArray[$wKeyIndexUse];
			}
			
			// If within the range, quit scrambling
			if ( (($zDiffLowerLimit <= $wDifficulty) && ($wDifficulty <= $zDiffUpperLimit)) == true ) {
				$outputResults['wDiffFirst'] = $scrambleResults[0]['wDifficulty'];
				$outputResults['wDiffSecond'] = $scrambleResults[1]['wDifficulty'];
				break;
			} else {
				
			}
			
		}
		
		// How was the difficulty calculated?
		$outputResults['wScrambled'] = '';
		$outputResults['wCalculation'] = '';
		
		foreach( $scrambleResults as $key=>$word ) {
			// Assign answers to output results array
			$outputResults['wAnswers'][$key] = $scrambleResults[$key]['wAnswers'];
			
			$wordNumber = $key + 1;
			
			// Generate difficulty calculation text
			$outputResults['wCalculation'] .= "<br />Word $wordNumber's difficulty was <a href='#' class='hoverTip' class='hoverTip'>" . $scrambleResults[$key]['wDifficulty'] . "<span class='tip'>What?</span></a>";
		}
		
		$outputResults['wOriginal'] = $phrase;
		$outputResults['wScrambled'] = $scrambleResults[0]['wScrambled'] . ' ' . $scrambleResults[1]['wScrambled'];
		$outputResults['wDifficulty'] = $wDifficulty;

		return $outputResults;
		
	}
	
	
	public function zQuestionCode($wScrambled, $wCatName = "Unassigned", $zQuestionNumber = "0") {
		
		$qRTF = "{\\rtf \n"; 
		$qRTF .= "{\\fonttbl  \n";
		$qRTF .= "{\\f0 Arial;}  \n";
		$qRTF .= "{\\f1 Courier New;}  \n";
		$qRTF .= "} \n";
		$qRTF .= "{\colortbl;\\red0\green0\blue0;\\red255\green155\blue0;\\red10\green90\blue130;} \n";
		$qRTF .= "\line\line\line \n";
		$qRTF .= "\ql \cf3 Question $zQuestionNumber \line \n";
		$qRTF .= "\\fs40 \\f0 \cf1 Category: $wCatName \line\line\line \n";
		$qRTF .= "\\f1 \n";
		
		$phrase = trim($wScrambled);
		// Split the phrase into words
		$wordArray = preg_split("/\s+/", $phrase);
		
		$wCount = count($wordArray);
		
		foreach($wordArray as $i => $word) {
			$wOriginalArray = str_split($word);
			
			foreach( $wOriginalArray as $key=>$character ) {
				$qRTF .= $character . " "; 
			}
			
			if( ($wCount > 1) && ($i == 0) ) {
				$qRTF .= "   ";
			}
		}
		
		$qRTF .= "\line \n";
		
		foreach($wordArray as $i => $word) {
			$wOriginalArray = str_split($word);
			$digitCount = count($wOriginalArray);
			
			if ($i == 0) {
				$qRTF .= "\cf2 ";
			} else {
				$qRTF .= "\cf3 ";
			}
			
			foreach( $wOriginalArray as $key=>$character ) {
				$humanKey = $key + 1;
				
				if( ($digitCount > 9) && ($key == 9)  ) {
					$qRTF .= "\\fs20 ";
					$qRTF .= $humanKey . "  ";
				} elseif ( ($digitCount > 9) && ($key > 9)  ) {
					$qRTF .= $humanKey . "  ";
				} else {
					$qRTF .= $humanKey . " "; 
				}
			}
			
			if ($digitCount > 9) {
				$qRTF .= "\\fs40 ";
			}
			
			if( ($wCount > 1) && ($i == 0) ) {
				$qRTF .= "   ";
			}
		}
		
		$qRTF .= "\n}";
		return $qRTF;
	}
	
	
	// Function for converting alphabet answer to numbers
	public function zButtonCode($wText, $wAnswers, $zQuestionNumber) {

		$zQuestionNumber = sprintf("%02s",   $zQuestionNumber);
		
		$answerVariable 	= "answer" . $zQuestionNumber;
		
		// Answer variables for phrases with two words
		$answerVariable1 	= "answer" . $zQuestionNumber . "a";
		$answerVariable2 	= "answer" . $zQuestionNumber . "b";
		
		
		$correctVariable 	= "correct" . $zQuestionNumber;
		
		// Correct variables for phrases with two words
		$correctVariable1 	= "correct" . $zQuestionNumber . "a";
		$correctVariable2 	= "correct" . $zQuestionNumber . "b";
		
		
		// Variable variables
		$answerPoint[$answerNumber] = $this->pointCorrect;
		
		$output = "//DON'T CHANGE \n";
		$output .= "getquestion = 0; \n\n";
		
		$output .= "//UNSCRAMBLED PHRASE: " . $wText . "\n";
		$output .= "//HAVE TO PUT THE RIGHT ANSWER AFTER ==  \n";
		$output .= "//(" . $this->pointCorrect . " = correct, " . $this->pointNotAnswered . " = not submitted  " . $this->pointWrong . " = wrong)  \n";
		
		// If the phrase has only a single word
		if ( count($wAnswers) == 1 ) {
		
			$i = 1;
			
			foreach( $wAnswers[0] as $key=>$answer ) {
				if($i == 1) {
					$output .= "if ( $answerVariable1 == $answer ) { $correctVariable = $this->pointCorrect; } \n"; 
				} else {
					$output .= "elsif ( $answerVariable1 == $answer ) { $correctVariable = $this->pointCorrect; } \n";
				}
				
				$i++;
			};
			
			$output .= "elsif ( $answerVariable1 == 0 ) { $correctVariable = $this->pointNotAnswered; } \n";
			$output .= "else { $correctVariable = $this->pointWrong; }";

		}
		
		// If the phrase has more than one word
		if ( count($wAnswers) > 1 ) {
			
			$i = 1;
			
			// First Phrase
			$output .= "\n//ANSWERS FOR THE FIRST PHRASE \n";
			
			foreach( $wAnswers[0] as $key=>$answer ) {
				if($i == 1) {
					$output .= "if ( $answerVariable1 == $answer ) { $correctVariable1 = $this->pointCorrect; } \n"; 
				} else {
					$output .= "elsif ( $answerVariable1 == $answer ) { $correctVariable1 = $this->pointCorrect; } \n";
				}
								
				$i++;
			};
			
			$output .= "elsif ( $answerVariable1 == 0 ) { $correctVariable1 = $this->pointNotAnswered; } \n";
			$output .= "else { $correctVariable1 = $this->pointWrong; } \n\n";

			$i = 1;
			
			// First Phrase
			$output .= "//ANSWERS FOR THE SECOND PHRASE \n";
			
			foreach( $wAnswers[1] as $key=>$answer ) {
				if($i == 1) {
					$output .= "if ( $answerVariable2 == $answer ) { $correctVariable2 = $this->pointCorrect; } \n"; 
				} else {
					$output .= "elsif ( $answerVariable2 == $answer ) { $correctVariable2 = $this->pointCorrect; } \n";
				}
							
				$i++;
			};
			
			$output .= "elsif ( $answerVariable2 == 0 ) { $correctVariable2 = $this->pointNotAnswered; } \n";
			$output .= "else { $correctVariable2 = $this->pointWrong; } \n\n";
			
			$output .= "if ( $correctVariable1 == $this->pointCorrect & $correctVariable2 == $this->pointCorrect ) { $correctVariable = $this->pointCorrect; } \n";
			$output .= "elsif ( $correctVariable1 == $this->pointNotAnswered & $correctVariable2 == $this->pointNotAnswered ) { $correctVariable = $this->pointNotAnswered; } \n";
			$output .= "else { $correctVariable = $this->pointWrong; }";
		}
		
		return $output;
	}



	// Function for converting alphabet answer to numbers
	public function zAuditQuestionCode($zQuestionNumber, $auditRank) {
		$auditRankQuery = $auditRank - 1;
		$output = "getquestion == $zQuestionNumber & auditselection > $auditRankQuery";
		return $output;
	}
}
	
?>