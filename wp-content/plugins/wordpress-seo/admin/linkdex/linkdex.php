<?php

class Linkdex {
	
	function __construct() {
		require WPSEO_PATH."/admin/linkdex/TextStatistics.php";
	}
	
	function output( $post ) {
		global $wpseo_metabox;
		
		$options = get_wpseo_options();

		if ( is_int( $post ) )
			$post = get_post( $post );
		if ( !$post )
			return; 

		if ( !class_exists('DOMDocument') ) {
			$output = '<div class="wpseo_msg"><p><strong>'.__('Error', 'wordpress-seo' ).':</strong> '.sprintf(__("your hosting environment does not support PHP's %sDocument Object Model%s.", 'wordpress-seo' ), '<a href="http://php.net/manual/en/book.dom.php">','</a>').' '.__("To enjoy all the benefits of the page analysis feature, you'll need to (get your host to) install it.", 'wordpress-seo' ).'</p></div>';
			return $output;
		}
		
		if ( !wpseo_get_value('focuskw') ) {
			$output = '<div class="wpseo_msg"><p><strong>'.__('Error', 'wordpress-seo' ).':</strong> '.__("you have not specified a focus keyword, you'll have to enter one, then save or update this post, before this report works.", 'wordpress-seo' ).'</p></div>';
			return $output;
		}
	
		$output = '<div class="wpseo_msg"><p><strong>'.__('Note', 'wordpress-seo' ).':</strong> '.__('to update this page analysis, save as draft or update and check this tab again', 'wordpress-seo' ).'.</p></div>';
		
		$results	= '';
		$job 		= array();

		$sampleurl = get_sample_permalink($post->ID);
		$job["pageUrl"] = preg_replace( '/%(post|page)name%/', $sampleurl[1], $sampleurl[0] );
		$job["pageSlug"] = urldecode( $post->post_name );
		$job["keyword"]	= wpseo_get_value('focuskw');
		$job["keyword_folded"] = $this->strip_separators_and_fold( $job["keyword"] );

		$dom = new domDocument; 
		$dom->strictErrorChecking = false; 
		$dom->preserveWhiteSpace = false; 
		@$dom->loadHTML($post->post_content);
		$xpath = new DOMXPath($dom);

		$statistics = new TextStatistics;
		
		// Keyword
		$this->ScoreKeyword($job, $results);
		
		// Title
		if ( wpseo_get_value('title') ) {
			$title = wpseo_get_value('title');
		} else {
			if ( isset( $options['title-'.$post->post_type] ) && $options['title-'.$post->post_type] != '' )
				$title_template = $options['title-'.$post->post_type];
			else
				$title_template = '%%title%% - %%sitename%%';
			$title = wpseo_replace_vars($title_template, (array) $post );		
		}
		$this->ScoreTitle($job, $results, $title, $statistics);
		unset($title);

		// Meta description
		$description = '';
		if ( wpseo_get_value('metadesc') ) {
			$description = wpseo_get_value('metadesc');
		} else {
			if ( isset( $options['metadesc-'.$post->post_type] ) && $options['metadesc-'.$post->post_type] != '' )
				$description = wpseo_replace_vars( $options['metadesc-'.$post->post_type], (array) $post );
		}
		$this->ScoreDescription($job, $results, $description, $wpseo_metabox->wpseo_meta_length, $statistics);
		unset($description);
	
		// Body
		$body 	= $this->GetBody( $post );	
		$firstp = $this->GetFirstParagraph( $post );
		$this->ScoreBody($job, $results, $body, $firstp, $statistics);
		unset($body);
		unset($firstp);

		// URL
		$this->ScoreUrl($job, $results, $statistics);	

		// Headings
		$headings = $this->GetHeadings($post->post_content);
		$this->ScoreHeadings($job, $results, $headings);
		unset($headings);

		// Images
		$alts = $this->GetImagesAltText($post->post_content);
		$imgs = $this->GetImageCount($dom, $xpath);
		$this->ScoreImagesAltText($job, $results, $alts, $imgs);
		unset($alts);
		unset($imgs);

		// Anchors
		$anchors 	= $this->GetAnchorTexts($dom, $xpath);
		$count 		= $this->GetAnchorCount($dom, $xpath);
		$this->ScoreAnchorTexts($job, $results, $anchors, $count);
		unset($anchors);
		unset($count);
		unset($dom);

		asort($results);

		$output .= '<table class="wpseoanalysis">';	
		foreach ($results as $result) {
			$scoreval = substr( $result, 0, 1 );
			switch ( $scoreval ) {
				case 1:
				case 2:
				case 3:
					$score = 'poor';
					break;
				case 4:
				case 5:
				case 6:
					$score = 'ok';
					break;
				case 7:
				case 8:
				case 9:
					$score = 'good';
					break;
			}
			$analysis = substr( $result, 2, strlen($result) );
			$output .= '<tr><td class="score"><img alt="'.$score.'" src="'.WPSEO_URL.'images/score_'.$score.'.png"/></td><td>'.$analysis.'</td></tr>';
		}
		$output .= '</table>';
		$output .= '<hr/>';
		$output .= '<p style="font-size: 13px;"><a href="http://yoa.st/linkdex"><img class="alignleft" style="margin: 0 10px 5px 0;" src="'.WPSEO_URL.'images/linkdex-logo.png" alt="Linkdex"/></a>'.sprintf(__( 'This page analysis brought to you by the collaboration of Yoast and %sLinkdex%s. Linkdex is an SEO suite that helps you optimize your site and offers you all the SEO tools you\'ll need. Yoast uses %sLinkdex%s and highly recommends you do too!', 'wordpress-seo' ),'<a href="http://yoa.st/linkdex">','</a>', '<a href="http://yoa.st/linkdex">','</a>').'</p>';
	
		unset($results);
		unset($job);

		return $output;
	}

	function SaveScoreResult(&$results, $scoreValue, $scoreUrlStatusMessage) {
		$results[] = $scoreValue.'|'.$scoreUrlStatusMessage;
	}

	function strip_separators_and_fold($inputString, $removeOptionalCharacters=false) {
		$keywordCharactersAlwaysReplacedBySpace = array(",", "'", "\"", "?", "’", "“", "”", "|","/");
		$keywordCharactersRemovedOrReplaced = array("_","-");
		$keywordWordsRemoved = array(" a ", " in ", " an ", " on ", " for ", " the ", " and ");

		// lower
		$inputString = wpseo_strtolower_utf8($inputString);

		// default characters replaced by space
		$inputString = str_replace($keywordCharactersAlwaysReplacedBySpace, ' ', $inputString);

		// standardise whitespace
		$inputString = preg_replace('/\s+/',' ',$inputString);

		// deal with the separators that can be either removed or replaced by space
		if ($removeOptionalCharacters) {
			// remove word separators with a space
			$inputString = str_replace($keywordWordsRemoved, ' ', $inputString);

			$inputString = str_replace($keywordCharactersRemovedOrReplaced, '', $inputString);				
		} else {
			$inputString = str_replace($keywordCharactersRemovedOrReplaced, ' ', $inputString);		
		}
		
		// standardise whitespace again
		$inputString = preg_replace('/\s+/',' ',$inputString);

		return $inputString;
	}
	
	function ScoreKeyword($job, &$results) {
		$keywordStopWord = __("The keyword for this page contains one or more %sstop words%s, consider removing them. Found '%s'.", 'wordpress-seo' );
	
		if ( wpseo_stopwords_check( $job["keyword"] ) !== false )
			$this->SaveScoreResult( $results, 5, sprintf( $keywordStopWord,"<a href=\"http://en.wikipedia.org/wiki/Stop_words\">", "</a>", wpseo_stopwords_check( $job["keyword"] ) ) );
	}
	
	function ScoreUrl($job, &$results, $statistics) {
		$urlGood 		= __("The keyword / phrase appears in the URL for this page.", 'wordpress-seo' );
		$urlMedium 		= __("The keyword / phrase does not appear in the URL for this page. If you decide to rename the URL be sure to check the old URL 301 redirects to the new one!", 'wordpress-seo' );
		$urlStopWords	= __("The slug for this page contains one or more <a href=\"http://en.wikipedia.org/wiki/Stop_words\">stop words</a>, consider removing them.", 'wordpress-seo' );
		$longSlug		= __("The slug for this page is a bit long, consider shortening it.", 'wordpress-seo' );
		
		$needle 	= $this->strip_separators_and_fold( $job["keyword"] );
		$haystack1 	= $this->strip_separators_and_fold( $job["pageUrl"], true );
		$haystack2 	= $this->strip_separators_and_fold( $job["pageUrl"], false );

		if (strrpos($haystack1,$needle) || strrpos($haystack2,$needle))
			$this->SaveScoreResult( $results, 9, $urlGood );
		else
			$this->SaveScoreResult( $results, 5, $urlMedium );	

		// Check for Stop Words in the slug
		if ( wpseo_stopwords_check( $job["pageSlug"], true ) !== false )
			$this->SaveScoreResult( $results, 5, $urlStopWords );

		// Check if the slug isn't too long relative to the length of the keyword
		if ( ( $statistics->text_length( $job["keyword"] ) + 20 ) < $statistics->text_length( $job["pageSlug"] ) && 40 < $statistics->text_length( $job["pageSlug"] ) )
			$this->SaveScoreResult( $results, 5, $longSlug );
	}

	function ScoreTitle($job, &$results, $title, $statistics) {		
		$scoreTitleMinLength 		 = 40;
		$scoreTitleMaxLength 		 = 70;
		$scoreTitleKeywordLimit		 = 0;

		$scoreTitleMissing 			 = __("Please create a page title.", 'wordpress-seo' );
		$scoreTitleCorrectLength 	 = __("The page title is more than 40 characters and less than the recommended 70 character limit.", 'wordpress-seo' );
		$scoreTitleTooShort 		 = __("The page title contains %d characters, which is less than the recommended minimum of 40 characters. Use the space to add keyword variations or create compelling call-to-action copy.", 'wordpress-seo' );
		$scoreTitleTooLong 			 = __("The page title contains %d characters, which is more than the viewable limit of 70 characters; some words will not be visible to users in your listing.", 'wordpress-seo' );
		$scoreTitleKeywordMissing 	 = __("The keyword / phrase %s does not appear in the page title.", 'wordpress-seo' );
		$scoreTitleKeywordBeginning  = __("The page title contains keyword / phrase, at the beginning which is considered to improve rankings.", 'wordpress-seo' );
		$scoreTitleKeywordEnd 		 = __("The page title contains keyword / phrase, but it does not appear at the beginning; try and move it to the beginning.", 'wordpress-seo' );
		$scoreTitleKeywordIn 		 = __("The page title contains keyword / phrase.", 'wordpress-seo' );

		if ( $title == "" ) {
			$this->SaveScoreResult($results, 1, $scoreTitleMissing);
		} else {	
			$length = $statistics->text_length( $title );
			if ($length < $scoreTitleMinLength)
				$this->SaveScoreResult( $results, 6, sprintf($scoreTitleTooShort, $length) );
			else if ($length > $scoreTitleMaxLength)
				$this->SaveScoreResult( $results, 6, sprintf($scoreTitleTooLong, $length) );
			else
				$this->SaveScoreResult( $results, 9, $scoreTitleCorrectLength );

			// TODO MA Keyword/Title matching is exact match with separators removed, but should extend to distributed match
			$needle_position = stripos( $title, $job["keyword_folded"] );

			if ( $needle_position === false )
				$needle_position = stripos( $title, $job["keyword"] );

			if ( $needle_position === false )
				$this->SaveScoreResult( $results, 2, sprintf( $scoreTitleKeywordMissing, $job["keyword_folded"] ) );

			if ( $needle_position <= $scoreTitleKeywordLimit )
				$this->SaveScoreResult( $results, 9, $scoreTitleKeywordBeginning );
			else
				$this->SaveScoreResult( $results, 6, $scoreTitleKeywordEnd );
		}
	}

	function ScoreAnchorTexts($job, &$results, $anchor_texts, $count) {
		$scoreNoLinks 					= __("No outbound links appear in this page, consider adding some as appropriate.", 'wordpress-seo' );
		$scoreKeywordInOutboundLink		= __("You're linking to another page with the keyword you want this page to rank for, consider changing that if you truly want this page to rank.", 'wordpress-seo' );
		$scoreLinksDofollow				= __("This page has %s outbound link(s).", 'wordpress-seo' );
		$scoreLinksNofollow				= __("This page has %s outbound link(s), all nofollowed.", 'wordpress-seo' );
		$scoreLinks						= __("This page has %s nofollowed link(s) and %s normal outbound link(s).", 'wordpress-seo' );

		
		if ( $count['external']['nofollow'] == 0 && $count['external']['dofollow'] == 0 ) {
			$this->SaveScoreResult( $results, 6, $scoreNoLinks );
		} else {
			$found = false;
			foreach ($anchor_texts as $anchor_text) {
				if ( wpseo_strtolower_utf8( $anchor_text ) == $job["keyword_folded"] )
					$found = true;
			}
			if ( $found )
				$this->SaveScoreResult($results, 2, $scoreKeywordInOutboundLink);

			if ( $count['external']['nofollow'] == 0 && $count['external']['dofollow'] > 0  ) {
				$this->SaveScoreResult($results, 9, sprintf( $scoreLinksDofollow, $count['external']['dofollow'] ) );
			} else if ( $count['external']['nofollow'] > 0 && $count['external']['dofollow'] == 0  ) {
				$this->SaveScoreResult($results, 7, sprintf( $scoreLinksNofollow, $count['external']['nofollow'] ) );
			} else {
				$this->SaveScoreResult($results, 8, sprintf( $scoreLinks, $count['external']['nofollow'], $count['external']['dofollow'] ) );
			}
		}

	}

	function GetAnchorTexts(&$dom, &$xpath) {
		$query 			= "//a|//A";
		$dom_objects 	= $xpath->query($query);
		$anchor_texts	= array();
		foreach ($dom_objects as $dom_object) {
			if ( $dom_object->attributes->getNamedItem('href') ) {
				$href = $dom_object->attributes->getNamedItem('href')->textContent;
				if ( substr( $href, 0, 4 ) == 'http' )
					$anchor_texts['external'] = $dom_object->textContent;
			}
		}
		unset($dom_objects);
		return $anchor_texts;
	}

	function GetAnchorCount(&$dom, &$xpath) {
		$query 			= "//a|//A";
		$dom_objects 	= $xpath->query($query);
		$count = array( 
			'total' => 0,
			'internal' => array( 'nofollow' => 0, 'dofollow' => 0 ), 
			'external' => array( 'nofollow' => 0, 'dofollow' => 0 ), 
			'other' => array( 'nofollow' => 0, 'dofollow' => 0 ) 
		);
		
		foreach ($dom_objects as $dom_object) {
			$count['total']++;
			if ( $dom_object->attributes->getNamedItem('href') ) {
				$href 	= $dom_object->attributes->getNamedItem('href')->textContent;
				$wpurl	= get_bloginfo('url'); 
				if ( substr( $href, 0, 1 ) == "/" || substr( $href, 0, strlen( $wpurl ) ) == $wpurl )
					$type = "internal";
				else if ( substr( $href, 0, 4 ) == 'http' )
					$type = "external";
				else
					$type = "other";
				if ( $dom_object->attributes->getNamedItem('rel') ) {
					$link_rel = $dom_object->attributes->getNamedItem('rel')->textContent;
					if ( stripos($link_rel, 'nofollow') !== false )
						$count[$type]['nofollow']++;
					else
						$count[$type]['dofollow']++;
				} else {
					$count[$type]['dofollow']++;
				}
			}
		}
		return $count;
	}
	
	function ScoreImagesAltText($job, &$results, $alts, $imgcount) {
		$scoreImagesNoImages 			= __("No images appear in this page, consider adding some as appropriate.", 'wordpress-seo' );
		$scoreImagesNoAlt			 	= __("The images on this page are missing alt tags.", 'wordpress-seo' );
		$scoreImagesAltKeywordIn		= __("The images on this page contain alt tags with the target keyword / phrase.", 'wordpress-seo' );
		$scoreImagesAltKeywordMissing 	= __("The images on this page do not have alt tags containing your keyword / phrase.", 'wordpress-seo' );

		if ( $imgcount == 0 ) {
			$this->SaveScoreResult($results,6,$scoreImagesNoImages);
		} else if ( count($alts) == 0 && $imgcount != 0 ) {
			$this->SaveScoreResult($results,3,$scoreImagesNoAlt);
		} else {
			$found=false;
			foreach ($alts as $alt) {
				$haystack1=$this->strip_separators_and_fold($alt,true);
				$haystack2=$this->strip_separators_and_fold($alt,false);
				if (strrpos($haystack1,$job["keyword_folded"])!==false)
					$found=true;
				else if (strrpos($haystack2,$job["keyword_folded"])!==false)
					$found=true;
			}
			if ($found)
				$this->SaveScoreResult($results,9,$scoreImagesAltKeywordIn);				
			else 
				$this->SaveScoreResult($results,5,$scoreImagesAltKeywordMissing);
		}

	}

	function GetImagesAltText($postcontent) {
		preg_match_all( '/<img [^>]+ alt=(["\'])([^\\1]+)\\1[^>]+>/im', $postcontent, $matches );
		$alts = array();
		foreach ( $matches[2] as $alt ) {
			$alts[] = wpseo_strtolower_utf8( $alt );
		}
		return $alts;
	}

	function GetImageCount(&$dom, &$xpath) {
		$query 			= "//img|//IMG";
		$dom_objects 	= $xpath->query($query);
		$count 			= 0;
		foreach ($dom_objects as $dom_object)
			$count++;
		return $count;
	}
	
	function ScoreHeadings($job, &$results, $headings) {
		$scoreHeadingsNone				= __("No heading tags appear in the copy.", 'wordpress-seo' );
		$scoreHeadingsKeywordIn			= __("Keyword / keyphrase appears in %s (out of %s) headings in the copy. While not a major ranking factor, this is beneficial.", 'wordpress-seo' );
		$scoreHeadingsKeywordMissing	= __("You have not used your keyword / keyphrase in any heading in your copy.", 'wordpress-seo' );

		$headingCount = count( $headings );
		if ( $headingCount == 0 )
			$this->SaveScoreResult( $results, 6, $scoreHeadingsNone );
		else {
			$found = 0;
			foreach ($headings as $heading) {
				$haystack1 = $this->strip_separators_and_fold( $heading , true );
				$haystack2 = $this->strip_separators_and_fold( $heading , false );

				if ( strrpos( $haystack1, $job["keyword_folded"]) !== false )
					$found++;
				else if ( strrpos( $haystack2, $job["keyword_folded"]) !== false )
					$found++;
			}
			if ( $found )
				$this->SaveScoreResult($results,9, sprintf( $scoreHeadingsKeywordIn, $found, $headingCount ) );
			else 
				$this->SaveScoreResult($results,3,$scoreHeadingsKeywordMissing);
		}
	}

	// Currently just returns an array of the text content
	function GetHeadings( $postcontent ) {
		preg_match_all('/<h([1-6])([^>]+)?>(.*)?<\/h\\1>/i', $postcontent, $matches);
		$headings = array();
		foreach ($matches[3] as $heading) {
			$headings[] = wpseo_strtolower_utf8( $heading );
		}
		return $headings;
	}	
	
	function ScoreDescription($job, &$results, $description, $maxlength = 155, $statistics) {
		$scoreDescriptionMinLength = 120;
		$scoreDescriptionCorrectLength	= __("In the specified meta description, consider: How does it compare to the competition? Could it be made more appealing?", 'wordpress-seo' );
		$scoreDescriptionTooShort 		= __("The meta description is under 120 characters, however up to %s characters are available. %s", 'wordpress-seo' );
		$scoreDescriptionTooLong		= __("The specified meta description is over %s characters, reducing it will ensure the entire description is visible. %s", 'wordpress-seo' );
		$scoreDescriptionMissing		= __("No meta description has been specified, search engines will display copy from the page instead.", 'wordpress-seo' );
		$scoreDescriptionKeywordIn		= __("The meta description contains the primary keyword / phrase.", 'wordpress-seo' );
		$scoreDescriptionKeywordMissing	= __("A meta description has been specified, but it does not contain the target keyword / phrase.", 'wordpress-seo' );

		$metaShorter					= '';
		if ($maxlength != 155)
			$metaShorter				= __("The available space is shorter than the usual 155 characters because Google will also include the publication date in the snippet.", 'wordpress-seo' );
		
		if ( $description == "" ) {
			$this->SaveScoreResult($results,1,$scoreDescriptionMissing);
		} else {
			$length = $statistics->text_length( $description );
			
			if ($length < $scoreDescriptionMinLength)
				$this->SaveScoreResult( $results, 6, sprintf($scoreDescriptionTooShort, $maxlength, $metaShorter) );
			else if ($length <= $maxlength)
				$this->SaveScoreResult( $results, 9, $scoreDescriptionCorrectLength);
			else
				$this->SaveScoreResult( $results, 6, sprintf($scoreDescriptionTooLong, $maxlength, $metaShorter) );

			// TODO MA Keyword/Title matching is exact match with separators removed, but should extend to distributed match
			$haystack1 = $this->strip_separators_and_fold($description,true);
			$haystack2 = $this->strip_separators_and_fold($description,false);
			if (strrpos($haystack1,$job["keyword_folded"])===false && strrpos($haystack2,$job["keyword_folded"])===false)
				$this->SaveScoreResult($results,3,$scoreDescriptionKeywordMissing);
			else 
				$this->SaveScoreResult($results,9,$scoreDescriptionKeywordIn);	
		}
	}


	function ScoreBody($job, &$results, $body, $firstp, $statistics) {		
		$scoreBodyGoodLimit 	= 300;
		$scoreBodyPoorLimit 	= 100;

		$scoreBodyGoodLength 	= __("There are %d words contained in the body copy, this is greater than the 300 word recommended minimum.", 'wordpress-seo' );
		$scoreBodyPoorLength 	= __("There are %d words contained in the body copy, this is below the 300 word recommended minimum. Add more useful content on this topic for readers.", 'wordpress-seo' );
		$scoreBodyBadLength 	= __("There are %d words contained in the body copy. This is far too low and should be increased.", 'wordpress-seo' );

		$scoreKeywordDensityLow 	= __("The keyword density is %s%%, which is a bit low, the keyword was found %s times.", 'wordpress-seo' );
		$scoreKeywordDensityHigh 	= __("The keyword density is %s%%, which is over the advised 5.5%% maximum, the keyword was found %s times.", 'wordpress-seo' );
		$scoreKeywordDensityGood 	= __("The keyword density is %s%%, which is great, the keyword was found %s times.", 'wordpress-seo' );

		$scoreFirstParagraphLow		= __("The keyword doesn't appear in the first paragraph of the copy, make sure the topic is clear immediately.", 'wordpress-seo' );
		$scoreFirstParagraphHigh	= __("The keyword appears in the first paragraph of the copy.", 'wordpress-seo' );

		$fleschurl					= '<a href="http://en.wikipedia.org/wiki/Flesch-Kincaid_readability_test#Flesch_Reading_Ease">'.__('Flesch Reading Ease', 'wordpress-seo' ).'</a>';
		$scoreFlesch				= __("The copy scores %s in the %s test, which is considered %s to read. %s", 'wordpress-seo' );
		
		// Copy length check
		$wordCount = $statistics->word_count( $body );
		
		if ( $wordCount < $scoreBodyPoorLimit )
			$this->SaveScoreResult( $results, 1, sprintf( $scoreBodyBadLength, $wordCount ) );
		else if ( $wordCount < $scoreBodyGoodLimit )
			$this->SaveScoreResult( $results, 5, sprintf( $scoreBodyPoorLength, $wordCount ) );
		else
			$this->SaveScoreResult( $results, 9, sprintf( $scoreBodyGoodLength, $wordCount ) );

		$body = wpseo_strtolower_utf8( $body );
		
		// Keyword Density check
		if ( $wordCount > 0 ) {
			$keywordCount 		= preg_match_all("/".$job["keyword"]."/msiU", $body, $res);
			$keywordWordCount 	= str_word_count( $job["keyword"] );
			$keywordDensity 	= number_format( ( ($keywordCount / ($wordCount - (($keywordCount -1) * $keywordWordCount))) * 100 ) , 2 );
		}

		if ( $keywordDensity < 1 ) {
			$this->SaveScoreResult( $results, 4, sprintf( $scoreKeywordDensityLow, $keywordDensity, $keywordCount ) );		
		} else if ( $keywordDensity > 5.5 ) {
			$this->SaveScoreResult( $results, 1, sprintf( $scoreKeywordDensityHigh, $keywordDensity, $keywordCount ) );		
		} else {
			$this->SaveScoreResult( $results, 9, sprintf( $scoreKeywordDensityGood, $keywordDensity, $keywordCount ) );		
		}

		$firstp = wpseo_strtolower_utf8( $firstp );
		
		// First Paragraph Test
		if ( stripos( $firstp, $job["keyword"] ) === false && strpos( $firstp, $job["keyword_folded"] ) === false ) {
			$this->SaveScoreResult( $results, 3, $scoreFirstParagraphLow );
		} else {
			$this->SaveScoreResult( $results, 9, $scoreFirstParagraphHigh );		
		}

		$lang = get_bloginfo('language');
		if ( substr($lang, 0, 2) == 'en' ) {
			// Flesch Reading Ease check
			$flesch = $statistics->flesch_kincaid_reading_ease($body);

			$note = '';
			if ( $flesch >= 90 ) {
				$level = __('very easy');
				$score = 9;
			} else if ( $flesch >= 80 ) {
				$level = __('easy');
				$score = 8;
			} else if ( $flesch >= 70 ) {
				$level = __('fairly easy');
				$score = 7;
			} else if ( $flesch >= 60 ) {
				$level = __('OK');
				$score = 7;
			} else if ( $flesch >= 50 ) {
				$level = __('fairly difficult');
				$note = __('Try to make shorter sentences to improve readability.', 'wordpress-seo' );
				$score = 6;
			} else if ( $flesch >= 30 ) {
				$level = __('difficult');
				$note = __('Try to make shorter sentences, using less difficult words to improve readability.', 'wordpress-seo' );
				$score = 5;
			} else if ( $flesch >= 0 ) {
				$level = __('very difficult');
				$note = __('Try to make shorter sentences, using less difficult words to improve readability.', 'wordpress-seo' );
				$score = 4;
			}
			$this->SaveScoreResult( $results, $score, sprintf( $scoreFlesch, $flesch, $fleschurl, $level, $note ) );	
		}
	}

	function GetBody( $post ) {		
		// Strip shortcodes, for obvious reasons
		$origHtml = wpseo_strip_shortcode( $post->post_content );
		if ( trim( $origHtml ) == '' )
			return '';

		$htmdata2 = preg_replace( "/\n|\r/"," ",$origHtml );
		if ( $htmdata2 == null )
			$htmdata2 = $origHtml;
		else
			unset( $origHtml );

		$htmdata3 = preg_replace( "/<(\x20*script|script).*?(\/>|\/script>)/", "", $htmdata2 );
		if ( $htmdata3 == null)
			$htmdata3 = $htmdata2;
		else
			unset( $htmdata2 );

		$htmdata4 = preg_replace( "/<!--.*?-->/", "", $htmdata3 );
		if ( $htmdata4 == null )
			$htmdata4 = $htmdata3;
		else
			unset( $htmdata3 );

		$htmdata5 = preg_replace( "/<(\x20*style|style).*?(\/>|\/style>)/", "", $htmdata4 );
		if ( $htmdata5 == null)
			$htmdata5 = $htmdata4;
		else
			unset( $htmdata4 );			

		return $htmdata5;
	}

	function GetFirstParagraph( $post ) {
		// To determine the first paragraph we first need to autop the content, then match the first paragraph and return.		
		preg_match( '/<p>(.*)<\/p>/', wpautop( $post->post_content ), $matches );
		return $matches[1];
	}
}