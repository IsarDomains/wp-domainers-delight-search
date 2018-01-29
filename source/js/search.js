// <![CDATA[

/**
 * Function searchDomains
 * 
 * @param uniqueId - The unique generated HTML id
 * @param platformName - The name of the platform
 * @param targetSite - The basic websiteURL to search
 * @param partnerId - The id of Sedos affiliate programm
 * @param language - Sedos website language
 * @param safeSearch - 1=exclude adult domains (default), 2=include adult domains
 * @param searchVariations - whether to include variations of the keyword or not
 * @param searchTLDs - List of TLDs that should be searched (blank=all available extension)
 * @param target - The name of the target where the link should be opened (_blank, _self, _top, _parent)
 */
function searchDomains(uniqueId, platformName, targetSite, partnerId, language, safeSearch, searchVariations, searchTLDs, target){
	if(document.getElementById('domainersdelight_sedosearch_domainname_' + uniqueId).value.length ==0){
		document.getElementById('domainersdelight_sedosearch_error_' + uniqueId).style.visibility = "visible";
		return;
	}else {
		document.getElementById('domainersdelight_sedosearch_error_' + uniqueId).style.visibility = "hidden";
	}
	//the entered search term
	var term = document.getElementById('domainersdelight_sedosearch_domainname_' + uniqueId).value;
	var platformURL = "";
	if('sedo' == platformName) {
		platformURL = searchSedo(term, targetSite, partnerId, language, safeSearch, searchVariations, searchTLDs);
	} else if ('uniregistry' == platformName) {
		platformURL = searchUniregistry(term, targetSite, partnerId);
	} else if ('godaddy' == platformName) { 
		platformURL = searchGoDaddy(term, targetSite, partnerId);
	}
	window.open(platformURL,'' + target + '');
}

/**
 * Function searchSedo
 * 
 * @param term - The search term
 * @param targetSite - The basic websiteURL to search
 * @param partnerId - The id of Sedos affiliate programm
 * @param language - Sedos website language
 * @param safeSearch - 1=exclude adult domains (default), 2=include adult domains
 * @param searchVariations - whether to include variations of the keyword or not
 * @param searchTLDs - List of TLDs that should be searched (blank=all available extension)
 */
function searchSedo(term, targetSite, partnerId, language, safeSearch, searchVariations, searchTLDs) {
	var urlParams = "&safe_search=" + safeSearch + "&language=" + language + "&partnerid=" + partnerId + "&synonyms=" + searchVariations + getTLDSelection(searchTLDs);
	return targetSite + term + urlParams;	
}

/**
 * Function searchUniregistry
 * 
 * @param term - The search term
 * @param targetSite - The basic websiteURL to search
 * @param partnerId - The id of Uniregistry's affiliate program
 */
function searchUniregistry(term, targetSite, partnerId,){
	var urlParams = "&aid=" + partnerId;
	return targetSite + term + urlParams;
}

/**
 * Function searchGoDaddy
 * 
 * @param term - The search term
 * @param targetSite - The basic websiteURL to search
 * @param partnerId - The id of GoDaddy's affiliate program
 */
function searchGoDaddy(term, targetSite, partnerId,){
	var urlParams = "&isc=" + partnerId + "&checkAvail=1";
	return targetSite + term + urlParams;
}

/**
 * Function getTLDSelection
 * Split the searchTLDs and prepare it for usage
 * as URL parameter to query Sedo's database only
 * for the desired TLDs.
 * Code pattern is: cc[0]=com&cc[1]=de&cc[2]=...
 *
 * @param searchTLDs - Comma separated list of TLDs
 * @return URI encoded list of the tlds
 */
function getTLDSelection(searchTLDs) {
	var tldUrl = "";
	if(searchTLDs != ""){
		var tldArray = searchTLDs.split(",");
		for (var i = 0; i < tldArray.length; i++) {
			//pattern cc[0]=com&cc[1]=de&... Note: Brackets has to be encoded!
			tldUrl = tldUrl + "&cc[" + i + "]=" + tldArray[i].trim();
		}
	}
	return encodeURI(tldUrl);
}
// ]]>