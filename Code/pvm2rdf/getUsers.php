<?php

/* Copy inc_sample.php to inc.php and fill in your credentials */

include_once("inc.php");

function getUsers() {
  $query="SELECT * FROM pvm_users";
  $res=selectQuery($query); 
  $out='';
  foreach ($res as $row) { 
    $out.=getRealPerson($row);
  }
  return display(TurtlePrefixUsers().$out);
}

function getMetaInformation($id) {
  $query="SELECT * FROM pvm_usermeta where user_id=$id";
  $res=selectQuery($query); 
  $a=array();
  foreach ($res as $row) {
    $a[$row['meta_key']]=$row['meta_value'];
  }
  return $a ;
}  

function getRealPerson($arow) {
  $uid=$arow['ID'];
  $meta=getMetaInformation($uid);
  $nickname=$arow['user_nicename'];
  $firstname=$meta['first_name'];
  $lastname=$meta['last_name'];
  $name="$firstname $lastname";
  $a=array();
  $a[]=' a foaf:Person ';
  $a[]=' pvm:hasUID "'.$uid.'"';
  $a[]=' foaf:name "'.$name.'"';
  $a[]=' pvm:loginName "'.$arow['user_login'].'"';
  $a[]=' pvm:displayName "'.$arow['display_name'].'"';
  $a[]=' foaf:nick "'.$nickname.'"';
  $a[]=' foaf:givenName "'.$firstname.'"';
  $a[]=' foaf:familyName "'.$lastname.'"';
  $a[]=' foaf:mbox "'.$arow['user_email'].'"';
  if (!empty($meta['location'])) { 
    $a[]=' foaf:location "'.$meta['location'].'"'; 
  }
  $a[]=' dcterms:created "'
	.str_replace(" ", "T", $arow['user_registered']).'"';
  return "pvmp:U". $uid . join(" ;\n  ",$a) . " . \n\n" ;
}

function TurtlePrefixUsers() {
return '
@prefix pvm: <http://pvm.uni-leipzig.de/Model/> .
@prefix pvmp: <http://pvm.uni-leipzig.de/Data/Person/> .
@prefix owl: <http://www.w3.org/2002/07/owl#> .
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
@prefix foaf: <http://xmlns.com/foaf/0.1/> .
@prefix dcterms: <http://purl.org/dc/terms/> .

<http://pvm.uni-leipzig.de/Data/Personen/>
    a owl:Ontology ;
    rdfs:label "Dump aus der PVM User-Datenbank" .

';
}

if (defined( 'ABSPATH' )) {
  // define WP shortcode
  add_shortcode( 'getUsers', 'getUsers' );
} else { echo getUsers(); }

?>
