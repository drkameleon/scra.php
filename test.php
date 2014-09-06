<?php

class A
{
	public $v1, $v2;

	public function __construct()
	{
		$this->v1 = 1;
		$this->v2 = 2;

		echo "Executed A:constructor\n";
	}

	public function dump()
	{
		echo ".v1 = ".$this->v1."\n";
		echo ".v2 = ".$this->v2."\n";
	}
}

class B extends A
{
	public function __construct()
	{
		parent::__construct();
		echo "Executed B:constructor\n";
	}
}

$a = new A();
$a->dump();

$b = new B();
$b->dump();

$subject = "{{pp-move-indef}}{{Use dmy dates|date=April 2012}}
{{Infobox country
|conventional_long_name = Federal Republic of Somalia<ref name=provisional/>
|native_name = {{smaller|''Jamhuuriyadda Federaalka Soomaaliya''}} ([[Somali language|so]])<br/ >{{lower|0.1em|{{big|جمهورية الصومال الفدرالية}}}} ([[Arabic language|ar]])<br/ >{{smaller|''{{transl|ar|ALA-LC|Jumhūriyyat aṣ-Ṣūmāl al-Fiderāliyya}}''}}
|common_name = Somalia
|image_flag = Flag of Somalia.svg
|image_coat = Coat of arms of Somalia.svg
|image_map = Somalia (orthographic projection).svg
|map_caption = 
|national_anthem = {{lang|so|''[[Qolobaa Calankeed]]''}}<br><center>[[File:QolobaaCalankeed.ogg]]</center>
|government_type = [[Federal Government of Somalia|Federal parliamentary republic]]
|leader_title1 = [[List of Presidents of Somalia|President]]
|leader_name1 = [[Hassan Sheikh Mohamud]]
|leader_title2 = [[Prime Minister of Somalia|Prime Minister]]
|leader_name2 = [[Abdiweli Sheikh Ahmed]]
|legislature = [[Federal Parliament of Somalia|Federal Parliament]]
|capital = [[Mogadishu]]
|latd=2|latm=2|latNS=N|longd=45|longm=21|longEW=E
|largest_city = Mogadishu
|area_km2 = 637,657
|area_sq_mi = 246,200 <!--Do not remove per [[WP:MOSNUM]]-->
|area_rank = {{smaller|44<sup>th</sup>}}
|area_magnitude = 1 E11
|percent_water = 
|population_estimate_year = 2012
|population_estimate_rank = {{smaller|86<sup>th</sup>}}
|population_density_rank = {{smaller|199<sup>th</sup>}}
|GDP_PPP_year = 2010
|GDP_PPP_per_capita_rank = {{smaller|224<sup>th</sup>}}
|Gini_year = |Gini_change = <!--increase/decrease/steady--> |Gini = <!--number only--> |Gini_ref = |Gini_rank = 
|HDI_year = 2011
|HDI_change = <!--increase/decrease/steady-->
|HDI = <!--number only-->
|HDI_ref = 
|HDI_rank = unranked
|sovereignty_type = [[History of Somalia|Formation]]
|established_event1 = [[Somali maritime history|Somali city-states]] 
|established_date1 = {{circa}} 200 {{small|[[Before Common Era|BCE]]}}
|established_event2 = [[Sultanate of Mogadishu]]
|established_date2 = 10th century
|established_event3 = [[Warsangali Sultanate]]
|established_date3 = 13th century
|established_event4 = [[Ajuuraan Empire]]
|established_date4 = 14th century
|established_event5 = [[Majeerteen Sultanate]]
|established_date5 = 18th century
|established_event6 = [[British Somaliland]]
|established_date6 = 1884
|established_event7 = [[Italian Somaliland]]
|established_date7 = 1889
|established_event8 = Union, Independence and Constitution
|established_event10 = [[Constitution of Somalia|Current Constitution]]
|established_date10 = 1 August 2012
|currency = [[Somali shilling]]
|currency_code = SOS
|time_zone = [[East Africa Time|EAT]]
|utc_offset = +3
|time_zone_DST = {{nowrap|not observed}}
|utc_offset_DST = +3
|drives_on = right
|calling_code = [[+252]]
|cctld = [[.so]]
}}
This is some sentence with some content";

preg_match_all("/\|capital = \[\[(\w+)\]\]/", $subject, $matches);
print_r($matches);

