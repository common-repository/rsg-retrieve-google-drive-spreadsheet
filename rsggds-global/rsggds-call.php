<?php


use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;
use Google\Cloud\BigQuery\BigQueryClient;
use Google\Cloud\ServiceBuilder;
if( ! class_exists('RSGGDSS_CALL') ) :

	class RSGGDSS_CALL{

		function rsggds_call_api($link,$title){
			$client = new Google_Client();
			
			$serviceRequest = new DefaultServiceRequest("");
			ServiceRequestFactory::setInstance($serviceRequest);
			$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
			$worksheetFeed = $spreadsheetService->getPublicSpreadsheet($link);
			if( $worksheetFeed == 'error_men_tol' ){
				return 'error_men_tol';	
			}else{
				$worksheet = $worksheetFeed->getByTitle($title);
				if($worksheet != 'rg__error'){
					return $worksheet;
				}else{
					return 'error_men_tol';
				}
			}
			
		}
	    
	}
endif;
