<?php

/*** set the content type header ***/
header("Content-type: text/css");
require_once('../../irrigationScheduler.class.php');
if(isset($_SESSION['field']))
	$irrigationScheduler = new irrigationScheduler( $_SESSION['field']);
else
	$irrigationScheduler = new irrigationScheduler();
?>

/******* GENERAL RESET *******/
html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em,
font, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, dl, dt, dd, ol, ul,fieldset, form, legend, table, caption, tbody,
 tfoot, thead, tr, th, td {
border:0pt none;
font-family:inherit;
font-size: 100%;
font-style:inherit;
font-weight:inherit;
margin:0pt;
padding:0pt;
vertical-align:baseline;
}

label
{
border:0pt none;
font-family:inherit;
font-size: 100%;
font-style:inherit;
font-weight:inherit;
margin:0pt;
padding:0pt;
vertical-align:baseline;
}
li
{
border:0pt none;
font-family:inherit;
font-size: 110%;
font-style:inherit;
font-weight:inherit;
margin:0pt;
padding:0pt;
vertical-align:baseline;
}

body {
	font: 100%/1.4 Arial, Helvetica, sans-serif;
	background: #ffffff;
	margin: 0;
	padding: 0;
	color: #000000;
}

/* ~~ Element/tag selectors ~~ */
ul, ol, dl { /* Due to variations between browsers, it"s best practices to zero padding and margin on lists. For consistency, you can either specify the amounts you want here, or on the list items (LI, DT, DD) they contain. Remember that what you do here will cascade to the .nav list unless you write a more specific selector. */
	padding: 0;
	margin: 0;
}

h1, h2, h3, h4, h5, h6, p {
	margin: 0px;	 /* removing the top margin gets around an issue where margins can escape from their containing div. The remaining bottom margin will hold it away from any elements that follow. */
	padding-right: 15px;
	padding-left: 10px; /* adding the padding to the sides of the elements within the divs, instead of the divs themselves, gets rid of any box model math. A nested div with side padding can also be used as an alternate method. */
	color: #000000;
}

h1 {
	font-size:16pt;
	font-weight: bold;	
}

h2{
	font-size:14pt;
	font-weight: bold;
}

h3{
	font-size:12pt;
	font-weight: bold;	
}

p{
	font-size:12pt;	
}
a img { /* this selector removes the default blue border displayed in some browsers around an image when it is surrounded by a link */
	border: none;
}

/* ~~ Styling for your site"s links must remain in this order - including the group of selectors that create the hover effect. ~~ */
a:link {
	color:#277484;
	font-weight:bold;
	text-decoration: underline; /* unless you style your links to look extremely unique, it"s best to provide underlines for quick visual identification */
}
a:visited {
	color: #1a4c57;
	font-weight:bold;
	text-decoration: underline;
}
a:hover, a:active, a:focus { /* this group of selectors will give a keyboard navigator the same hover experience as the person using a mouse. */
	text-decoration: none;
	font-weight:bold;
	color: #35a3ba;
}

/* ~~ this container surrounds all other divs giving them their percentage-based width ~~ */
.container {
	width: 100%;
<?php
	global $irrigationScheduler;
	if($irrigationScheduler->session->isApp == 0 )
	{
		echo "max-width: 400px; "; /* a max-width may be desirable to keep this layout from getting too wide on a large monitor. This keeps line length more readable. IE6 does not respect this declaration. */
	}
?>
	
	/*max-width: 1536px;/* a max-width may be desirable to keep this layout from getting too wide on a large monitor. This keeps line length more readable. IE6 does not respect this declaration. */
	min-width: 240px;/* a min-width may be desirable to keep this layout from getting too narrow. This keeps line length more readable in the side columns. IE6 does not respect this declaration. */
	background: #FFF;
	margin: 0 auto; /* the auto value on the sides, coupled with the width, centers the layout. It is not needed if you set the .container"s width to 100%. */
	border: thin solid #000;
}

/* ~~the header is not given a width. It will extend the full width of your layout. It contains an image placeholder that should be replaced with your own linked logo~~ */
.header {

	wisth: 100%;
	background: #35a3ba;
	border-bottom-width: thin;
	border-bottom-style: solid;
	border-bottom-color: #111;
}
.container .header-alt {
	background: #35a3ba;
	margin: 0px;
}


/* ~~ This is the layout information. ~~ 

1) Padding is only placed on the top and/or bottom of the div. The elements within this div have padding on their sides. This saves you from any "box model math". Keep in mind, if you add any side padding or border to the div itself, it will be added to the width you define to create the *total* width. You may also choose to remove the padding on the element in the div and place a second div within it with no width and the padding necessary for your design.

*/
.content {
	padding: 10px 0px;
width:100%;
}

/* ~~ This grouped selector gives the lists in the .content area space ~~ */
.content ul, .content ol { 
	padding: 0 15px 15px 40px; /* this padding mirrors the right padding in the headings and paragraph rule above. Padding was placed on the bottom for space between other elements on the lists and on the left to create the indention. These may be adjusted as you wish. */
}

/* Special formatting for the global navigation menu 
	1. hide sub-navigation unless user visits parent or sub-nav pages
	2. Bold current navigation "box", remove link, use current page color
*/
.globalNavigation {
	background: #acd373;
	position:relative;
	border-top-width: thin;
	border-top-style: solid;
	border-top-color: #c4df9b;
	text-align:center;
	padding: 0px;
}
.inputNavigation {
	background: #3cb6cd;
	position:relative;
	border-top-width: thin;
	border-top-style: solid;
	border-top-color: #c4df9b;
	text-align:center;
	padding: 0px;
}

.mainNavigation {
	background: #33BB99;
	position:relative;
	border-top-width: thin;
	border-top-style: solid;
	border-top-color: #c4df9b;
	text-align:center;
	padding: 0px;
}
.chartNavigation {
	background: #33BB44;
	position:relative;
	border-top-width: thin;
	border-top-style: solid;
	border-top-color: #c4df9b;
	text-align:center;
	padding: 0px;
}


.container .globalNavigation ul li, .container .globalNavigation ul li ul {
	list-style-type: none;
	border-bottom-width: thin;
	border-bottom-style: solid;
	border-bottom-color: #c4df9b;
}
#current {

	font-weight:bold;
}
.container .globalNavigation ul ul .secondLevel {
	background-color: #7cc576;
}
#currentParent {
	background-color: #71b26b;
	font-weight:bold;

}

.hideSubNavigation{
	display:none;	
}
/* ~~ The footer ~~ */
.footer {
	background: #3cb6cd;
	height: 60px;
	position:relative;
	border-top-width: thin;
	border-top-style: solid;
	border-top-color: #111;
	text-align: center;
	padding:5px 0px 0px 0px;
}

.container .footer ul li {
	list-style-type: none;
	display: inline;
	margin-top: 0px;
	margin-right: 5px;
	margin-bottom: 0px;
	margin-left: 5px;
}
/* ~~ miscellaneous float/clear classes ~~ */
.fltrt {  /* this class can be used to float an element right in your page. The floated element must precede the element it should be next to on the page. */
	float: right;
	margin-left: 8px;
}
.fltlft { /* this class can be used to float an element left in your page. The floated element must precede the element it should be next to on the page. */
	float: left;
	margin-right: 8px;
}
.clearfloat { /* this class can be placed on a <br /> or empty div as the final element following the last floated div (within the #container) if the #footer is removed or taken out of the #container */
	clear:both;
	height:0;
	font-size: 1px;
	line-height: 0px;
}

.container .globalNavigation ul li a {
	color: #000;
	text-decoration: none;
	font-weight: normal;
}
.container .footer ul li a {
	color: #000;
	text-decoration: none;
	font-weight: bold;
}

/* table formatting */

.currentRow {
	background-color:	#d5e9b9;
}

.healthyCol {
	background-color:	#acd473;	
}

.warningCol{
	background-color:	#fff899;
}

.dangerCol{
	background-color: #ffcccc;
}

.forageCut{
	border-style: solid;
	border-color: #ffffff;
	border-width: 1px;
}

.error{
	display:none;
	color: #c42028;
}

.errorPHP{
	
	color: #c42028;	
}

.success{
	font-weight:bold;	
}

#tableTitle{
	font-weight:bold;
	font-size:	14pt;
}

#budgettable{
}

/* visual design elements */

.separator{
	background-image:url(/irrigation-scheduler/images/separator_04.gif);
	background-repeat:repeat-x;
	width: 300px;
	margin: 5px;
}

.error404text{
	background-color:#3cb6cd;
	padding:5px 0px;	
}

#delete-field{
	display:none;
	font-weight:bold;
}

form{
	padding: 0px 10px;
}
.container .globalNavigation ul #currentParent a {
	font-weight: bold;
}
.container #error404 {
	background-color: #DADADA;
	height: 356px;
	margin: 0px;
	float: left;

}

.container .content .hidden {
	padding-left:5px;
	display: none;	
}
.container .content tr td {
	padding-right: 5px;
	padding-left: 5px;
}
