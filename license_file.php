<?php
function license(){

/*This file contains the License written by  the FACT Community and taken from the 
former FACT Quick Look Analysis website. IT will be displayed in e-mails and on the website under the tab 'license' */
$txt= <<< EOT

Please cite the FACT design paper and the QLA webpage  when using these data.
FACT design paper: H. Anderhub et al. JINST 8 (2013) P6008
\t http://iopscience.iop.org/1748-0221/8/06/P06008
\t QLA webpage: http://www.fact-project.org/monitoring
\t New QLA webpage: this page
If you intent to use the data, please let us know for reference.

Remarks:

* These are the results of a fast quick look analyis on site, i.e. they are preliminary. \n
* The quick look analysis includes all data, i.e. no data selection done.\n
* The given values are not fluxes but excess rates 
	(number of excess events per effective ontime), 
	i.e. there is a dependence on trigger threshold and 
	zenith distance of the observation (with the current 
	analysis for zenith distance smaller 40 degree and trigger 
	threshold smaller 500 DAC counts).\n
* Nights with less than 20 minutes of data are neglected for nightly binning. \n
* The QLA results are not reprocessed when a new software version is introduced. \n
* In case, you need further details about the data or a different binning, 
	please do not hesitate to contact us.\n
* The QLA contains all data since 12.12.2012. For older data, please contact us. \n
	 
EOT;

return $txt; 
}

?>
